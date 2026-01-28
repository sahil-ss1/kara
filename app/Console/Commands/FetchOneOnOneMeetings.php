<?php

namespace App\Console\Commands;

use App\Imports\GoogleCalendars;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FetchOneOnOneMeetings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google:fetch-1on1-meetings 
                            {--user= : User ID to fetch meetings for}
                            {--days=7 : Number of days to look ahead}
                            {--calendar= : Calendar ID (default: user\'s primary calendar)}
                            {--json : Output as JSON}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch upcoming 1-on-1 meetings from Google Calendar for meeting prep';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📅 Fetching Upcoming 1-on-1 Meetings from Google Calendar');
        $this->info('=' . str_repeat('=', 60));

        // Get user
        $userId = $this->option('user');
        $user = $userId ? User::find($userId) : User::whereNotNull('google_token')->first();

        if (!$user) {
            $this->error('❌ No user found.');
            if ($userId) {
                $this->error("   User ID {$userId} not found.");
            } else {
                $this->error('   No users with Google Calendar authentication found.');
            }
            return 1;
        }

        // Check if google_token exists and is not empty
        // google_token is cast as JSON, so it could be [], {}, or null
        $googleToken = $user->google_token;
        $hasToken = !empty($googleToken) && 
                   (is_array($googleToken) ? !empty($googleToken) : $googleToken !== null && $googleToken !== '');
        
        if (!$hasToken) {
            $this->error('❌ User does not have Google Calendar connected.');
            $this->error("   User: {$user->email}");
            $this->error("   Google Name: " . ($user->google_name ?? 'Not set'));
            $this->error("   Google Token: " . (empty($googleToken) ? 'Not set' : 'Set but may be invalid'));
            $this->error('   Please connect Google Calendar first via: /google/login');
            $this->info('');
            $this->info('   Note: Clicking "Sync with Google" in profile settings redirects to OAuth.');
            $this->info('   You must complete the OAuth flow to receive authentication tokens.');
            return 1;
        }

        $this->info("📧 User: {$user->email}");
        $this->info("📧 Google Account: " . ($user->google_name ?? 'N/A'));

        $daysAhead = (int) $this->option('days');
        $calendarId = $this->option('calendar') ?? $user->google_calendar_id ?? 'primary';

        $this->info("📆 Calendar ID: {$calendarId}");
        $this->info("⏰ Looking ahead: {$daysAhead} days");
        $this->newLine();

        try {
            $meetings = GoogleCalendars::get_one_on_one_meetings($user, $daysAhead, $calendarId);

            if (empty($meetings)) {
                $this->warn('⚠️  No 1-on-1 meetings found in the next ' . $daysAhead . ' days.');
                $this->info('   Looking for events with "1:1" or "one-on-one" in the title.');
                return 0;
            }

            // Output results
            if ($this->option('json')) {
                $this->outputJson($meetings);
            } else {
                $this->outputTable($meetings);
            }

            $this->newLine();
            $this->info("✅ Found " . count($meetings) . " upcoming 1-on-1 meeting(s)");

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error fetching meetings: ' . $e->getMessage());
            if ($this->option('verbose')) {
                $this->error('Stack trace: ' . $e->getTraceAsString());
            }
            return 1;
        }
    }

    /**
     * Output results as formatted table
     */
    protected function outputTable(array $meetings): void
    {
        $this->info('📋 UPCOMING 1-ON-1 MEETINGS');
        $this->info(str_repeat('─', 100));

        $headers = ['When', 'Title', 'Team Member', 'Duration', 'Status'];
        
        $rows = array_map(function ($meeting) {
            $when = $meeting['is_today'] 
                ? 'Today ' . Carbon::parse($meeting['start_time'])->format('H:i')
                : ($meeting['is_tomorrow']
                    ? 'Tomorrow ' . Carbon::parse($meeting['start_time'])->format('H:i')
                    : Carbon::parse($meeting['start_time'])->format('M d, H:i'));
            
            $member = $meeting['team_member'];
            $memberName = $member 
                ? ($member['matched'] ? $member['name'] : $member['name'] . ' ⚠️')
                : 'Unknown';
            
            $duration = $meeting['duration_minutes'] . ' min';
            
            $status = $meeting['days_until'] < 0 
                ? 'Past'
                : ($meeting['days_until'] === 0 
                    ? 'Today'
                    : ($meeting['days_until'] === 1 
                        ? 'Tomorrow'
                        : $meeting['days_until'] . ' days'));

            return [
                $when,
                $this->truncate($meeting['meeting_title'], 30),
                $this->truncate($memberName, 25),
                $duration,
                $status,
            ];
        }, $meetings);

        $this->table($headers, $rows);

        // Detailed view
        $this->newLine();
        $this->info('📝 DETAILED MEETING INFORMATION');
        $this->info(str_repeat('─', 100));

        foreach ($meetings as $index => $meeting) {
            $this->info(($index + 1) . ". " . $meeting['meeting_title']);
            $this->line("   📅 Time: " . $meeting['start_time_formatted'] . " - " . $meeting['end_time_formatted']);
            
            if ($meeting['team_member']) {
                $member = $meeting['team_member'];
                $this->line("   👤 Team Member: " . $member['name'] . " (" . $member['email'] . ")");
                
                if (!$member['matched']) {
                    $this->line("   ⚠️  " . ($member['note'] ?? 'Not found in team members'));
                }
            } else {
                $this->line("   👤 Team Member: Not identified");
            }
            
            if ($meeting['location']) {
                $this->line("   📍 Location: " . $meeting['location']);
            }
            
            if ($meeting['meeting_link']) {
                $this->line("   🔗 Meeting Link: " . $meeting['meeting_link']);
            }
            
            if ($meeting['description']) {
                $desc = strip_tags($meeting['description']);
                $this->line("   📝 Description: " . $this->truncate($desc, 80));
            }
            
            $this->newLine();
        }
    }

    /**
     * Output results as JSON
     */
    protected function outputJson(array $meetings): void
    {
        $this->line(json_encode($meetings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Truncate string to specified length
     */
    protected function truncate(string $string, int $length): string
    {
        return strlen($string) > $length 
            ? substr($string, 0, $length - 3) . '...' 
            : $string;
    }
}
