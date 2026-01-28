<?php

namespace App\Imports;

use App\Helpers\HubspotClientHelper;
use App\Models\ForecastCategory;
use HubSpot\Client\Crm\Pipelines\ApiException;

class HubspotForecastCategories
{
    public static function sync_with_hubspot($hubspot, $user, $organization_id)
    {
        if (! $hubspot) {
            $hubspot = HubspotClientHelper::createFactory($user);
        }
        try {
            $property = $hubspot->crm()->properties()->coreApi()->getByName('deals', 'hs_manual_forecast_category');

            $forecastCategories_ids = [];
            $options = $property->getOptions();
            foreach ($options as $option) {
                $forecastCategory_db_record = ForecastCategory::updateOrCreate(
                    [
                        'organization_id' => $organization_id,
                        'label' => $option->getLabel(),
                        'internal_value' => $option->getValue(),
                        'display_order' => $option->getDisplayOrder(),
                    ]
                );
                $forecastCategories_ids[] = $forecastCategory_db_record->id;
            }
            ForecastCategory::where('organization_id', $organization_id)->whereNotIn('id', $forecastCategories_ids)->delete();
        } catch (ApiException $e) {
            echo 'Exception when calling properties->coreApi->getByName: ', $e->getMessage();
        }
    }
}
