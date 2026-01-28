<?php

namespace App\Imports;

use App\Services\Google;
use Auth;
use Carbon\Carbon;
//use Google\Service\Calendar\EventCreator;
use Google_Service_Calendar_ConferenceData;
use Google_Service_Calendar_ConferenceSolutionKey;
use Google_Service_Calendar_CreateConferenceRequest;
use Illuminate\Support\Str;

class GoogleCalendars
{
    public static function get_calendars(): array
    {
        $service = app(Google::class)->connectUser(Auth::user())->service('Calendar');

        $calendars = [];
        if (! $service->getClient()->isAccessTokenExpired()) {
            $pageToken = null;

            do {
                // Ask the sub class to perform an API call with this pageToken (initially null).
                $list = $service->calendarList->listCalendarList(compact('pageToken'));

                foreach ($list->getItems() as $item) {
                    // The sub class is responsible for mapping the data into our database.
                    $calendars[$item->id] = $item->summary;
                }

                // Get the new page token from the response.
                $pageToken = $list->getNextPageToken();

                // Continue until the new page token is null.
            } while ($pageToken);
        }

        return $calendars;
    }

    public static function add_event($calendar_id, $event)
    {
        $service = app(Google::class)->connectUser(Auth::user())->service('Calendar');
        $optionalParameters = ['sendUpdates' => 'all', 'conferenceDataVersion' => 1, 'sendNotifications' => true];
        $event = $service->events->insert($calendar_id, $event, $optionalParameters);

        return $event;
    }

    public static function create_event($calendar_id, $summary, $start, $description, $member)
    {
        // $calendar = self::get_calendar($calendar_id);
        $start = Carbon::parse($start, Auth::user()->timezone)->tz('UTC');
        $event = new \Google_Service_Calendar_Event([
            'summary' => $summary,
            'description' => $description,
            'start' => [
                'dateTime' => $start->toRfc3339String(),
                // 'timeZone' => 'UTC',//$calendar->getTimeZone(),//
            ],
            'end' => [
                'dateTime' => $start->addHour()->toRfc3339String(),
                // 'timeZone' => 'UTC',//$calendar->getTimeZone(),//
            ],
            'attendees' => [
                ['email' => Auth::user()->google_name],
                ['email' => $member->email], //array('email' => 'dimgem@gmail.com'),//
            ],
            'reminders' => [
                'useDefault' => true,
            ],
        ]);
        //$event->setLocation('The Neighbourhood');
        $event->setConferenceData(self::getconferenceData());

        //$EventCreator = new \Google_Service_Calendar_EventCreator();
        //$EventCreator->setDisplayName( 'Training Team' );
        //$EventCreator->setEmail( Auth::user()->google_name );
        //$event->setCreator($EventCreator );

        return self::add_event($calendar_id, $event);
    }

    public static function get_event($calendar_id, $event_id)
    {
        $service = app(Google::class)->connectUser(Auth::user())->service('Calendar');
        $event = $service->events->get($calendar_id, $event_id);

        return $event;
    }

    public static function get_calendar($calendar_id)
    {
        $service = app(Google::class)->connectUser(Auth::user())->service('Calendar');
        $calendar = $service->calendars->get($calendar_id);

        return $calendar;
    }

    public static function getconferenceData(): Google_Service_Calendar_ConferenceData
    {
        $conferenceData = new Google_Service_Calendar_ConferenceData([
            'createRequest' => new Google_Service_Calendar_CreateConferenceRequest([
                'requestId' => Str::random(10),
                'conferenceSolutionKey' => new Google_Service_Calendar_ConferenceSolutionKey([
                    'type' => 'hangoutsMeet',
                ]),
            ]),
        ]);

        return $conferenceData;
    }

    //write function to create google calendar event
    public static function update_event($calendar_id, $event_id, $summary, $start, $end, $description)
    {
        $event = self::get_event($calendar_id, $event_id);
        $event->setSummary($summary);
        $event->setDescription($description);
        $event->getStart()->setDateTime($start);
        $event->getStart()->setTimeZone('America/Chicago');
        $event->getEnd()->setDateTime($end);
        $event->getEnd()->setTimeZone('America/Chicago');

        return self::add_event($calendar_id, $event);
    }

    //write function to delete google calendar event
    public static function delete_event($calendar_id, $event_id)
    {
        $service = app(Google::class)->connectUsing(Auth::user()->google_token, Auth::user()->google_refresh_token)->service('Calendar');
        $event = $service->events->delete($calendar_id, $event_id);

        return $event;
    }

    //write function to get google calendar events
    public static function get_events($calendar_id, $timeMin, $timeMax, $user = null, $maxResults = 250)
    {
        // Support both web (Auth::user()) and command (explicit user) contexts
        $user = $user ?? Auth::user();
        
        if (!$user) {
            throw new \Exception('User not authenticated');
        }
        
        $service = app(Google::class)->connectUser($user)->service('Calendar');
        $optParams = [
            'maxResults' => $maxResults,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => $timeMin,
            'timeMax' => $timeMax,
        ];
        $results = $service->events->listEvents($calendar_id, $optParams);

        return $results->getItems();
    }

    /**
     * Fetch upcoming 1-on-1 meetings for a user
     * Filters events containing "1:1" or "one-on-one" in the title
     * Identifies team members from attendees
     * 
     * @param \App\Models\User $user The user to fetch meetings for
     * @param int $daysAhead Number of days to look ahead (default: 7)
     * @param string|null $calendarId Calendar ID (default: primary)
     * @return array Structured data for meeting prep screen
     */
    public static function get_one_on_one_meetings($user, int $daysAhead = 7, ?string $calendarId = null): array
    {
        // Use primary calendar if not specified
        $calendarId = $calendarId ?? $user->google_calendar_id ?? 'primary';

        // Calculate time range
        $timeMin = Carbon::now()->toRfc3339String();
        $timeMax = Carbon::now()->addDays($daysAhead)->toRfc3339String();

        // Fetch events using existing method
        $events = self::get_events($calendarId, $timeMin, $timeMax, $user, 250);

        // Filter and process 1-on-1 meetings
        $oneOnOneMeetings = [];
        $userEmail = strtolower($user->google_name ?? $user->email);

        foreach ($events as $event) {
            $summary = $event->getSummary() ?? '';
            
            // Check if event title contains 1-on-1 keywords
            if (!self::isOneOnOneMeeting($summary)) {
                continue;
            }

            // Get event details
            $start = $event->getStart();
            $end = $event->getEnd();
            $startTime = $start->getDateTime() ?? $start->getDate();
            $endTime = $end->getDateTime() ?? $end->getDate();

            // Identify team member from attendees
            $attendees = $event->getAttendees() ?? [];
            $teamMember = self::identifyTeamMember($attendees, $user, $userEmail);

            // Extract meeting link (Google Meet, Zoom, etc.)
            $meetingLink = self::extractMeetingLink($event);

            // Build structured data for meeting prep screen
            $oneOnOneMeetings[] = [
                'meeting_id' => $event->getId(),
                'meeting_title' => $summary,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'start_time_formatted' => Carbon::parse($startTime)->format('Y-m-d H:i:s'),
                'end_time_formatted' => Carbon::parse($endTime)->format('Y-m-d H:i:s'),
                'duration_minutes' => Carbon::parse($startTime)->diffInMinutes(Carbon::parse($endTime)),
                'description' => $event->getDescription() ?? '',
                'location' => $event->getLocation() ?? '',
                'meeting_link' => $meetingLink,
                'team_member' => $teamMember,
                'attendees' => self::formatAttendees($attendees),
                'days_until' => Carbon::now()->diffInDays(Carbon::parse($startTime), false),
                'is_today' => Carbon::parse($startTime)->isToday(),
                'is_tomorrow' => Carbon::parse($startTime)->isTomorrow(),
            ];
        }

        // Sort by start time
        usort($oneOnOneMeetings, function ($a, $b) {
            return strtotime($a['start_time']) <=> strtotime($b['start_time']);
        });

        return $oneOnOneMeetings;
    }

    /**
     * Check if event title indicates a 1-on-1 meeting
     */
    protected static function isOneOnOneMeeting(string $title): bool
    {
        $titleLower = strtolower($title);
        
        $keywords = [
            '1:1',
            '1-on-1',
            'one-on-one',
            'one on one',
            '1-1',
            '1 to 1',
        ];

        foreach ($keywords as $keyword) {
            if (strpos($titleLower, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Identify team member from event attendees
     */
    protected static function identifyTeamMember(array $attendees, $user, string $userEmail): ?array
    {
        // Get organization - handle both session-based (web) and CLI contexts
        $organization = null;
        
        // Try session first (web context)
        if (session()->has('organization')) {
            $organization = session('organization');
        } else {
            // For CLI context or when session is not available, try user's organization() method
            $organization = $user->organization();
            
            // If still null, try to get first organization from user's organizations
            if (!$organization) {
            $organization = $user->organizations()->first();
            }
        }

        if (!$organization) {
            \Log::warning('No organization found for user', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'has_session' => session()->has('organization'),
                'organizations_count' => $user->organizations()->count(),
            ]);
            return null;
        }

        // Normalize user email for comparison
        $userEmail = strtolower(trim($userEmail));

        foreach ($attendees as $attendee) {
            $attendeeEmail = strtolower(trim($attendee->getEmail() ?? ''));
            
            // Skip empty emails
            if (empty($attendeeEmail)) {
                continue;
            }
            
            // Skip the manager's own email
            if ($attendeeEmail === $userEmail || ($attendee->getSelf() ?? false)) {
                continue;
            }

            // Try to find matching member in organization
            // Use case-insensitive email matching and trim whitespace
            $member = \App\Models\Member::where('organization_id', $organization->id)
                ->whereRaw('LOWER(TRIM(email)) = ?', [$attendeeEmail])
                ->where('active', true)
                ->first();

            if ($member) {
                \Log::info('Team member matched from Google Calendar attendee', [
                    'member_id' => $member->id,
                    'member_email' => $member->email,
                    'attendee_email' => $attendeeEmail,
                    'organization_id' => $organization->id,
                ]);
                
                return [
                    'id' => $member->id,
                    'name' => $member->full_name ?? ($member->firstName . ' ' . $member->lastName),
                    'first_name' => $member->firstName,
                    'last_name' => $member->lastName,
                    'email' => $member->email,
                    'matched' => true,
                ];
            }
        }

        // If no member found, return first non-manager attendee info
        foreach ($attendees as $attendee) {
            $attendeeEmail = strtolower(trim($attendee->getEmail() ?? ''));
            
            if (empty($attendeeEmail)) {
                continue;
            }
            
            if ($attendeeEmail !== $userEmail && !($attendee->getSelf() ?? false)) {
                \Log::warning('Attendee not found in team members', [
                    'attendee_email' => $attendeeEmail,
                    'organization_id' => $organization->id,
                    'attendee_display_name' => $attendee->getDisplayName(),
                ]);
                
                return [
                    'id' => null,
                    'name' => $attendee->getDisplayName() ?? $attendeeEmail,
                    'email' => $attendeeEmail,
                    'matched' => false,
                    'note' => 'Not found in team members - may need to add to HubSpot',
                ];
            }
        }

        return null;
    }

    /**
     * Extract meeting link (Google Meet, Zoom, etc.) from event
     */
    protected static function extractMeetingLink($event): ?string
    {
        // Check for Google Meet link in conference data
        $conferenceData = $event->getConferenceData();
        if ($conferenceData) {
            $entryPoints = $conferenceData->getEntryPoints();
            if ($entryPoints && count($entryPoints) > 0) {
                return $entryPoints[0]->getUri();
            }
        }

        // Check description for meeting links
        $description = $event->getDescription() ?? '';
        if (preg_match('/(https?:\/\/[^\s]+)/', $description, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Format attendees list
     */
    protected static function formatAttendees(array $attendees): array
    {
        $formatted = [];
        
        foreach ($attendees as $attendee) {
            $formatted[] = [
                'email' => $attendee->getEmail(),
                'display_name' => $attendee->getDisplayName() ?? $attendee->getEmail(),
                'response_status' => $attendee->getResponseStatus() ?? 'needsAction',
                'self' => $attendee->getSelf() ?? false,
            ];
        }

        return $formatted;
    }
}
