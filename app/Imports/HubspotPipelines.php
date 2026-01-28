<?php

namespace App\Imports;

use App\Helpers\HubspotClientHelper;
use App\Models\Pipeline;
use App\Models\Stage;
use HubSpot\Client\Crm\Pipelines\ApiException;
use Illuminate\Support\Carbon;

class HubspotPipelines
{
    public static function sync_with_hubspot($hubspot, $user, $organization_id): void
    {
        if (! $hubspot) {
            $hubspot = HubspotClientHelper::createFactory($user);
        }
        try {
            $apiResponse = $hubspot->crm()->pipelines()->pipelinesApi()->getAll('deals');
            //ray($apiResponse);
            $pipelines = $apiResponse['results'];

            $pipelines_ids = [];
            foreach ($pipelines as $pipeline) {
                $pipeline_db_record = Pipeline::updateOrCreate(
                    ['organization_id' => $organization_id, 'hubspot_id' => $pipeline->getId()],
                    [
                        'label' => $pipeline->getLabel(),
                        'hubspot_createdAt' => Carbon::parse($pipeline->getCreatedAt())->toDateTimeString(),
                        'hubspot_updatedAt' => Carbon::parse($pipeline->getUpdatedAt())->toDateTimeString(),
                        'active' => true, // Pipelines synced from HubSpot should be active
                    ]
                );
                $pipelines_ids[] = $pipeline_db_record->id;

                $stages = $pipeline->getStages();
                $stages_ids = [];
                foreach ($stages as $stage) {
                    $meta = $stage->getMetadata();
                    $stage_db_record = Stage::updateOrCreate(
                        ['pipeline_id' => $pipeline_db_record->id, 'hubspot_id' => $stage->getId(), 'hubspot_pipeline_id' => $pipeline->getId()],
                        [
                            'label' => $stage->getLabel(),
                            'display_order' => $stage->getDisplayOrder(),
                            'hubspot_createdAt' => Carbon::parse($stage->getCreatedAt())->toDateTimeString(),
                            'hubspot_updatedAt' => Carbon::parse($stage->getUpdatedAt())->toDateTimeString(),
                            'isClosed' => filter_var($meta['isClosed'], FILTER_VALIDATE_BOOLEAN),
                            'probability' => $meta['probability'],
                        ]
                    );
                    $stages_ids[] = $stage_db_record->id;
                }
                Stage::where('pipeline_id', $pipeline_db_record->id)->whereNotIn('id', $stages_ids)->delete();
            }
            Pipeline::where('organization_id', $organization_id)->whereNotIn('id', $pipelines_ids)->delete();
        } catch (ApiException $e) {
            echo 'Exception when calling pipelines_api->get_all: ', $e->getMessage();
        }
    }
}
