<?php

namespace App\Imports;

use App\Enum\GoalType;
use App\Helpers\HubspotClientHelper;
use App\Models\Activity;
use App\Models\Deal;
use App\Models\Member;
use App\Models\Stage;
use App\Models\User;
use HubSpot\Client\Crm\Deals\Model\SimplePublicObjectInput;
use Illuminate\Support\Carbon;

class HubspotDeals
{
    public static function sync_with_hubspot($hubspot, User $user, $organization_id)
    {
        if (! $hubspot) {
            $hubspot = HubspotClientHelper::createFactory($user);
        }
        try {
            // Cache members and stages to avoid N+1 queries (performance optimization)
            $members = Member::where('organization_id', $organization_id)
                ->get()
                ->keyBy('hubspot_id');
            
            $stages = Stage::select('stages.id as stage_id', 'stages.hubspot_id as stage_hubspot_id', 'pipelines.id as pipeline_id', 'pipelines.hubspot_id as pipeline_hubspot_id')
                ->join('pipelines', 'pipelines.id', '=', 'stages.pipeline_id')
                ->where('pipelines.organization_id', $organization_id)
                ->get()
                ->keyBy(function($item) {
                    return $item->pipeline_hubspot_id . '_' . $item->stage_hubspot_id;
                });
            
            $after = null;
            $deals_ids = [];
            do {
                // Increased from 10 to 100 deals per page for better performance
                $apiResponse = $hubspot->crm()->deals()->basicApi()->getPage(100, $after, 'pipeline,dealstage,dealname,amount,closedate,createdate,hubspot_owner_id,hs_lastmodifieddate,hs_time_in_,hs_is_closed,hs_is_closed_won,hs_next_step,hs_manual_forecast_category', null, 'tasks,calls,emails,meetings', false);
                $deals = $apiResponse['results'];

                foreach ($deals as $deal) {
                    $properties = $deal->getProperties();
                    
                    // Skip if required properties are missing
                    if (empty($properties['pipeline']) || empty($properties['dealstage'])) {
                        continue;
                    }
                    
                    // Use cached member lookup instead of database query
                    $member = null;
                    if (!empty($properties['hubspot_owner_id'])) {
                        $member = $members->get($properties['hubspot_owner_id']);
                    }
                    
                    // Use cached stage/pipeline lookup instead of database query
                    $lookupKey = $properties['pipeline'] . '_' . $properties['dealstage'];
                    $pipeline = $stages->get($lookupKey);
                    if ($pipeline) {
                        $associations = $deal->getAssociations();
                        //if ( isset($associations['emails']) ) $emails= count($associations['emails']['results']); else $emails=0;
                        //if ( isset($associations['meetings']) ) $meetings= count($associations['meetings']['results']); else $meetings=0;
                        
                        // Handle nullable date fields
                        $closedate = null;
                        if (!empty($properties['closedate'])) {
                            try {
                                $closedate = Carbon::parse($properties['closedate'])->toDateTimeString();
                            } catch (\Exception $e) {
                                \Log::warning('Failed to parse closedate', ['value' => $properties['closedate'], 'deal_id' => $deal->getId()]);
                            }
                        }
                        
                        $createdate = null;
                        if (!empty($properties['createdate'])) {
                            try {
                                $createdate = Carbon::parse($properties['createdate'])->toDateTimeString();
                            } catch (\Exception $e) {
                                \Log::warning('Failed to parse createdate', ['value' => $properties['createdate'], 'deal_id' => $deal->getId()]);
                            }
                        }
                        
                        $hs_date_entered = null;
                        $dateEnteredKey = 'hs_date_entered_' . $properties['dealstage'];
                        if (!empty($properties[$dateEnteredKey])) {
                            try {
                                $hs_date_entered = Carbon::parse($properties[$dateEnteredKey])->toDateTimeString();
                            } catch (\Exception $e) {
                                \Log::warning('Failed to parse hs_date_entered', ['key' => $dateEnteredKey, 'value' => $properties[$dateEnteredKey] ?? null, 'deal_id' => $deal->getId()]);
                            }
                        }
                        
                        // Fallback to createdate if hs_date_entered is not available (required NOT NULL column)
                        if (!$hs_date_entered) {
                            $hs_date_entered = $createdate ?: Carbon::parse($deal->getCreatedAt())->toDateTimeString();
                        }
                        
                        $deal_db_record = Deal::updateOrCreate(
                            ['hubspot_id' => $deal->getId()],
                            [
                                'name' => $properties['dealname'] ?? 'Unnamed Deal',
                                'amount' => $properties['amount'] ?? 0,
                                'closedate' => $closedate,
                                'createdate' => $createdate,
                                'hubspot_createdAt' => Carbon::parse($deal->getCreatedAt())->toDateTimeString(),
                                'hubspot_updatedAt' => Carbon::parse($deal->getUpdatedAt())->toDateTimeString(),
                                'hubspot_pipeline_id' => $properties['pipeline'],
                                'hubspot_stage_id' => $properties['dealstage'],
                                'hubspot_owner_id' => $properties['hubspot_owner_id'] ?? null,
                                'member_id' => ($member) ? $member->id : null,
                                'pipeline_id' => $pipeline ? $pipeline->pipeline_id : null,
                                'stage_id' => $pipeline ? $pipeline->stage_id : null,
                                'hs_date_entered' => $hs_date_entered,
                                'hs_is_closed' => filter_var($properties['hs_is_closed'] ?? false, FILTER_VALIDATE_BOOLEAN),
                                'hs_is_closed_won' => filter_var($properties['hs_is_closed_won'] ?? false, FILTER_VALIDATE_BOOLEAN),
                                'hs_next_step' => $properties['hs_next_step'] ?? null,
                                'hs_manual_forecast_category' => $properties['hs_manual_forecast_category'] ?? null,
                            ]
                        );
                        $deals_ids[] = $deal_db_record->id;
                        if (isset($associations['tasks'])) {
                            $tasks = $associations['tasks']['results'];
                            self::importTasks($hubspot, $organization_id, $deal_db_record->id, $tasks);
                        }
                        if (isset($associations['calls'])) {
                            $calls = $associations['calls']['results'];
                            self::importCalls($hubspot, $organization_id, $deal_db_record->id, $calls);
                        }
                    }
                }

                if (isset($apiResponse['paging'])) {
                    $paging = $apiResponse['paging'];
                    $after = $paging->getNext()['after'];
                } else {
                    $after = null;
                }

            } while (! empty($after));
            Deal::whereHas('pipeline', function ($q) use ($organization_id) {
                $q->where('organization_id', '=', $organization_id);
            })->whereNotIn('id', $deals_ids)->delete();
        } catch (\HubSpot\Client\Crm\Deals\ApiException $e) {
            \Log::error('HubSpot API Exception in sync_with_hubspot', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; // Re-throw so controller can handle it
        } catch (\Exception $e) {
            \Log::error('General Exception in sync_with_hubspot', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; // Re-throw so controller can handle it
        }
    }

    public static function importTasks($client, $organization_id, $dealid, $tasks)
    {
        $task_to_search = [];
        foreach ($tasks as $task) {
            $task_to_search[] = $task->getId();
        }
        try {
            $after = null;
            $tasks_ids = [];

            $filter1 = new \HubSpot\Client\Crm\Objects\Tasks\Model\Filter([
                'values' => $task_to_search,
                'property_name' => 'hs_object_id',
                'operator' => 'IN',
            ]);
            $filterGroup1 = new \HubSpot\Client\Crm\Objects\Tasks\Model\FilterGroup([
                'filters' => [$filter1],
            ]);
            $publicObjectSearchRequest = new \HubSpot\Client\Crm\Objects\Tasks\Model\PublicObjectSearchRequest([
                'filter_groups' => [$filterGroup1],
                'properties' => ['hubspot_owner_id,hs_task_status,hs_timestamp,hs_task_completion_date,hs_task_body,hs_task_subject,hs_task_type,hs_task_priority'],
                'limit' => 10,
                'after' => $after,
            ]);

            do {
                //$apiResponse = $hubspot->crm()->objects()->tasks()->basicApi()->getPage(10, $after, 'hubspot_owner_id,hs_task_status,hs_task_priority', false);
                $apiResponse = $client->crm()->objects()->tasks()->searchApi()->doSearch($publicObjectSearchRequest);
                //ray($apiResponse);
                $tasks = $apiResponse['results'];

                foreach ($tasks as $task) {
                    $properties = $task->getProperties();
                    $member = isset($properties['hubspot_owner_id']) ? Member::where('organization_id', $organization_id)->where('hubspot_id', $properties['hubspot_owner_id'])->first() : null;
                    $task_db_record = Activity::updateOrCreate(
                        ['deal_id' => $dealid, 'hubspot_id' => $task->getId(), 'type' => GoalType::TASK],
                        [
                            'hubspot_createdAt' => Carbon::parse($task->getCreatedAt())->toDateTimeString(),
                            'hubspot_updatedAt' => Carbon::parse($task->getUpdatedAt())->toDateTimeString(),
                            'hubspot_owner_id' => $properties['hubspot_owner_id'] ?? '',
                            'member_id' => ($member) ? $member->id : null,
                            'hubspot_status' => $properties['hs_task_status'] ?? '',
                            'hubspot_timestamp' => $properties['hs_timestamp'] ?? null,
                            'hubspot_task_completion_date' => ! empty($properties['hs_task_completion_date']) ? Carbon::parse($properties['hs_task_completion_date'])->toDateTimeString() : null,
                            'hubspot_task_body' => $properties['hs_task_body'] ?? '',
                            'hubspot_task_subject' => $properties['hs_task_subject'] ?? '',
                            'hubspot_task_type' => $properties['hs_task_type'] ?? '',
                            'hubspot_task_priority' => $properties['hs_task_priority'] ?? '',
                            //'hubspot_deal_id'

                        ]
                    );
                    $tasks_ids[] = $task_db_record->id;
                }

                if (isset($apiResponse['paging'])) {
                    $paging = $apiResponse['paging'];
                    $after = $paging->getNext()['after'];
                    $publicObjectSearchRequest->setAfter($after);
                } else {
                    $after = null;
                }

            } while (! empty($after));
            Activity::where('deal_id', $dealid)->where('type', GoalType::TASK)->whereNotIn('id', $tasks_ids)->delete();
        } catch (\HubSpot\Client\Crm\Objects\Tasks\ApiException $e) {
            echo 'Exception when calling basic_api->get_page: ', $e->getMessage();
        }
    }

    public static function importCalls($client, $organization_id, $dealid, $calls)
    {
        $calls_to_search = [];
        foreach ($calls as $call) {
            $calls_to_search[] = $call->getId();
        }
        try {
            $after = null;
            $calls_ids = [];

            $filter1 = new \HubSpot\Client\Crm\Objects\Calls\Model\Filter([
                'values' => $calls_to_search,
                'property_name' => 'hs_object_id',
                'operator' => 'IN',
            ]);
            $filterGroup1 = new \HubSpot\Client\Crm\Objects\Calls\Model\FilterGroup([
                'filters' => [$filter1],
            ]);
            $publicObjectSearchRequest = new \HubSpot\Client\Crm\Objects\Calls\Model\PublicObjectSearchRequest([
                'filter_groups' => [$filterGroup1],
                'properties' => ['hubspot_owner_id,hs_call_status,hs_timestamp'],
                'limit' => 10,
                'after' => $after,
            ]);

            do {
                //$apiResponse = $hubspot->crm()->objects()->tasks()->basicApi()->getPage(10, $after, 'hubspot_owner_id,hs_task_status,hs_task_priority', false);
                $apiResponse = $client->crm()->objects()->calls()->searchApi()->doSearch($publicObjectSearchRequest);
                //ray($apiResponse);
                $calls = $apiResponse['results'];

                foreach ($calls as $call) {
                    $properties = $call->getProperties();
                    $member = Member::where('organization_id', $organization_id)->where('hubspot_id', $properties['hubspot_owner_id'])->first();
                    $call_db_record = Activity::updateOrCreate(
                        ['deal_id' => $dealid, 'hubspot_id' => $call->getId(), 'type' => GoalType::CALL],
                        [
                            'hubspot_createdAt' => Carbon::parse($call->getCreatedAt())->toDateTimeString(),
                            'hubspot_updatedAt' => Carbon::parse($call->getUpdatedAt())->toDateTimeString(),
                            'hubspot_owner_id' => $properties['hubspot_owner_id'],
                            'member_id' => ($member) ? $member->id : null,
                            'hubspot_status' => $properties['hs_call_status'],
                            'hubspot_timestamp' => $properties['hs_timestamp'],
                            //'hubspot_deal_id'

                        ]
                    );
                    $calls_ids[] = $call_db_record->id;
                }

                if (isset($apiResponse['paging'])) {
                    $paging = $apiResponse['paging'];
                    $after = $paging->getNext()['after'];
                    $publicObjectSearchRequest->setAfter($after);
                } else {
                    $after = null;
                }

            } while (! empty($after));
            Activity::where('deal_id', $dealid)->where('type', GoalType::CALL)->whereNotIn('id', $calls_ids)->delete();
        } catch (\HubSpot\Client\Crm\Objects\Calls\ApiException $e) {
            echo 'Exception when calling basic_api->get_page: ', $e->getMessage();
        }
    }
    /*
    function searchStageTime($client, $dealid, $stageid){

        $filter1 = new Filter([
            'value' => 'id',
            'values' => [$dealid],
            'property_name' => 'string',
            'operator' => 'EQ'
        ]);
        $filterGroup1 = new FilterGroup([
            'filters' => [$filter1]
        ]);
        $publicObjectSearchRequest = new PublicObjectSearchRequest([
            'filter_groups' => [$filterGroup1],
            'sorts' => ['string'],
            'query' => 'string',
            'properties' => ["hs_date_entered_$stageid","hs_time_in_$stageid"],
            'limit' => 1,
            'after' => 0,
        ]);
        try {
            $apiResponse = $client->crm()->deals()->searchApi()->doSearch($publicObjectSearchRequest);
            // Debug output removed for production
        } catch (ApiException $e) {
            echo "Exception when calling search_api->do_search: ", $e->getMessage();
        }

    }*/

    public static function updateDeal($hubspot, User $user, $dealid, array $properties): bool
    {
        if (! $hubspot) {
            $hubspot = HubspotClientHelper::createFactory($user);
        }

        //$properties = [
        //    'amount' => '1500.00',
        //    'closedate' => '2019-12-07T16 =>50 =>06.678Z',
        //    'dealname' => 'Custom data integrations',
        //    'dealstage' => 'presentationscheduled',
        //    'hubspot_owner_id' => '910901',
        //    'pipeline' => 'default'
        //];

        $simplePublicObjectInput = new SimplePublicObjectInput([
            'properties' => $properties,
        ]);

        try {
            $apiResponse = $hubspot->crm()->deals()->basicApi()->update($dealid, $simplePublicObjectInput);

            return true;
            //var_dump($apiResponse);
        } catch (\HubSpot\Client\Crm\Deals\ApiException $e) {
            //echo "Exception when calling basic_api->update: ", $e->getMessage();
            return false;
        }

    }
}
