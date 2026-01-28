<?php

namespace App\Imports;

use App\Enum\GoalType;
use App\Helpers\HubspotClientHelper;
use App\Models\Activity;
use App\Models\Deal;
use App\Models\Member;
use App\Models\User;
use Auth;
use HubSpot\Client\Crm\Objects\Tasks\Model\Filter;
use HubSpot\Client\Crm\Objects\Tasks\Model\FilterGroup;
use Illuminate\Support\Carbon;

class HubspotTasks
{
    public static function createTask($hubspot, User $user, $organization_id, $hubspot_deal_id, array $properties)
    {
        if (! $hubspot) {
            $hubspot = HubspotClientHelper::createFactory($user);
        }

        /*
        $properties = [
            'hs_timestamp'     => Carbon::now()->addMonth()->format('Y-m-d'),//'2023-05-30T03 =>30 =>17.883Z',
            'hs_task_body'     => 'Send Proposal',
            //'hubspot_owner_id' => '64492917',
            'hs_task_subject'  => 'Follow-up for Brian Buyer',
            'hs_task_status'   => 'NOT_STARTED', //COMPLETED or NOT_STARTED.
            'hs_task_priority' => 'HIGH', //HIGH, MEDIUM, or LOW.
            'hs_task_type'     => 'TODO', //CALL, EMAIL, or TODO.
        ];*/

        $toObjectType = 'deals';
        $associationSpec = new \HubSpot\Client\Crm\Objects\Tasks\Model\AssociationSpec([
            'association_category' => 'HUBSPOT_DEFINED',
            'association_type_id' => 216, //11 deal to engagement //12 Engagement to deal //216 tasks_to_deals //215 deals_to_tasks
        ]);
        $simplePublicObject = new \HubSpot\Client\Crm\Objects\Tasks\Model\SimplePublicObjectInput(['properties' => $properties]);

        try {
            $apiResponse = $hubspot->crm()->objects()->tasks()->basicApi()->create($simplePublicObject);
            $productId = $apiResponse->getId();
            if ($hubspot_deal_id) {
                $taskAssociationCreateApiResponse = $hubspot->crm()->objects()->tasks()->associationsApi()->create(
                    $productId,
                    $toObjectType,
                    $hubspot_deal_id,
                    [$associationSpec],
                );
                $deal = Deal::where('hubspot_id', $hubspot_deal_id)->first();

                return self::importTask($apiResponse, $deal->id, $organization_id);
            }
            //return ( $apiResponse );
        } catch (\HubSpot\Client\Crm\Objects\Tasks\ApiException $e) {
            echo 'Exception when calling basic_api->create: ', $e->getMessage();
        }
    }

    public static function importTask($task, $dealid, $organization_id)
    {
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

        return $task_db_record;
    }

    public static function removeTask($hubspot, User $user, $hubspot_task_id)
    {
        if (! $hubspot) {
            $hubspot = HubspotClientHelper::createFactory($user);
        }

        try {
            $hubspot->crm()->objects()->tasks()->basicApi()->archive($hubspot_task_id);
            Activity::where('hubspot_id', $hubspot_task_id)->first()->delete();

            return true;
            //var_dump( $apiResponse );
        } catch (\HubSpot\Client\Crm\Objects\Tasks\ApiException $e) {
            echo 'Exception when calling basic_api->archive: ', $e->getMessage();

            return false;
        }
    }

    public static function updateTask($hubspot, User $user, $hubspot_task_id, $properties)
    {
        if (! $hubspot) {
            $hubspot = HubspotClientHelper::createFactory($user);
        }

        $simplePublicObjectInput = new \HubSpot\Client\Crm\Objects\Tasks\Model\SimplePublicObjectInput(['properties' => $properties]);

        try {
            $apiResponse = $hubspot->crm()->objects()->tasks()->basicApi()->update($hubspot_task_id, $simplePublicObjectInput);

            return true; //$apiResponse->getProperties();
        } catch (\HubSpot\Client\Crm\Objects\Tasks\ApiException $e) {
            echo 'Exception when calling basic_api->update: ', $e->getMessage();

            return false;
        }
    }

    /*
    public static function sync_with_hubspot($hubspot=null){
        if (!$hubspot) $hubspot = HubspotClientHelper::createFactory();
        try {
            $after=null;
            $tasks_ids=[];

            $filter1 = new Filter([
                'value' => 'COMPLETED',
                'property_name' => 'hs_task_status',
                'operator' => 'EQ'
            ]);
            $filterGroup1 = new FilterGroup([
                'filters' => [$filter1]
            ]);
            $publicObjectSearchRequest = new PublicObjectSearchRequest([
                'filter_groups' => [$filterGroup1],
                'properties' => ['hubspot_owner_id','hs_task_status','hs_task_priority'],
                'limit' => 10,
                'after' => $after,
            ]);

            do {
                //$apiResponse = $hubspot->crm()->objects()->tasks()->basicApi()->getPage(10, $after, 'hubspot_owner_id,hs_task_status,hs_task_priority', false);
                $apiResponse = $hubspot->crm()->objects()->tasks()->searchApi()->doSearch($publicObjectSearchRequest);
                //ray($apiResponse);
                $tasks = $apiResponse['results'];

                foreach ($tasks as $task) {
                    $properties = $task->getProperties();
                    $organization = Auth::user()->organization();
                    if (!$organization) {
                        continue; // Skip if no organization
                    }
                    $member = Member::where('organization_id', $organization->id)->where('hubspot_id', $properties['hubspot_owner_id'])->first();
                    $task_db_record = Task::updateOrCreate(
                        [ 'organization_id' => $organization->id, 'hubspot_id' => $task->getId() ],
                        [
                            'hubspot_createdAt' => Carbon::parse($task->getCreatedAt())->toDateTimeString(),
                            'hubspot_updatedAt' => Carbon::parse($task->getUpdatedAt())->toDateTimeString(),
                            'hubspot_owner_id' => $properties['hubspot_owner_id'],
                            'member_id' => ($member)?$member->id:null,
                            'hubspot_status' => $properties['hs_task_status'],
                            'hubspot_task_priority' => $properties['hs_task_priority']
                        ]
                    );
                    $tasks_ids[]=$task_db_record->id;
                }

                if ( isset( $apiResponse['paging']) ) {
                    $paging = $apiResponse['paging'];
                    $after= $paging->getNext()['after'];
                    $publicObjectSearchRequest->setAfter($after);
                }else $after=null;

            } while ( !empty($after) );
            $organization = Auth::user()->organization();
            if ($organization) {
                Task::where('organization_id', $organization->id)->whereNotIn('id', $tasks_ids)->delete();
            }
        } catch (ApiException $e) {
            echo "Exception when calling basic_api->get_page: ", $e->getMessage();
        }
    }
    */

}
