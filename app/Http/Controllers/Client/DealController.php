<?php

namespace App\Http\Controllers\Client;

use App\Enum\DealWarning;
use App\Helpers\Periods;
use App\Http\Controllers\Controller;
use App\Imports\HubspotDeals;
use App\Imports\HubspotForecastCategories;
use App\Imports\HubspotOwners;
use App\Imports\HubspotPipelines;
use App\Models\Deal;
use App\Models\ForecastCategory;
use App\Models\Stage;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;

class DealController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('client.deal.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Deal  $deal
     * @return \Illuminate\Http\Response
     */
    public function show(Deal $deal)
    {
        return view('client.deal.index');
    }

    private function makeDatatable(?int $id){
        //ray()->showQueries();
	    if ( isset($_POST['filters']) ) {
			foreach ($_POST['filters'] as $filter){
				if (trim($filter)){
					$pieces = explode("=", substr($filter, 2));
					if ($pieces[0] != 'teams') $_POST[$pieces[0]] = $pieces[1];
				}
			}
	    }
        $organization = Auth::user()->organization();
        if (!$organization) {
            return DataTables::of(collect([]))
                         ->addIndexColumn()
                         ->setRowId('id');
        }
        $deals = Deal::select('deals.*')
                     ->join('pipelines', 'deals.pipeline_id', '=', 'pipelines.id')
                            ->where('pipelines.organization_id', $organization->id)
                            ->where('pipelines.active', 1)
                     ->with(['member','stage']);//use select to allow datatables to make queries
        if ($id) $deals->where('deals.id', $id);
        
        if ( !empty($_POST['teams']) && $_POST['teams'] != ['None'] && $_POST['teams'] != ['N']) {
            $teams = $_POST['teams'];
            $deals->whereHas( 'member', function ( $q ) use ( $teams ) {
                $q->whereHas( 'teams', function ( $q2 ) use ( $teams ) {
                    $q2->whereIn( 'teams.id', $teams );
                } );
            } );
        }

        if ( !empty($_POST['pipelines']) ) $deals->whereIn('pipelines.id', [$_POST['pipelines']]);
        if ( !empty($_POST['members']) ) $deals->whereHas('member', function($q){
                                                                    $q->whereIn('members.id', $_POST['members']);
                                                                });
        if ( isset($_POST['probability']) ) {
            $probability = $_POST['probability'];
            if ( !is_array($probability) )
                $deals->whereHas( 'stage', function ( $q ) use ($probability) {
                                            $q->where( 'probability', $probability );
                                        });
            else
                $deals->whereHas('stage', function($q) use ($probability) {
                                            $q->whereBetween('probability', $probability);
                                        });
        }

        if ( isset($_POST['isClosed']) ) {
            $deals->whereHas( 'stage', function ( $q ) {
                $q->where( 'isClosed', $_POST['isClosed'] );
            });
        };

        if ( isset($_POST['closedate']) ) {
            $dates=Periods::get(str_replace('\'','',$_POST['closedate']));
            if ($dates)
                $deals->whereBetween( 'closedate', [ $dates['from'], $dates['to'] ] );
        }

        if ( isset($_POST['createdate']) ) {
            $dates=Periods::get($_POST['createdate']);
            if ($dates)
                $deals->whereBetween( 'createdate', [ $dates['from'], $dates['to'] ] );
        }

        if ( !empty($_POST['warnings'])) {
            $deals->whereHas('stage', function($q) {
                $q->whereBetween('probability', [0.01, 0.99]);
            })->whereHas('pipeline', function($q){
                $q->where('active',1);
            });
            //$deals_with_warnings=[];
            // MySQL-compatible date difference queries (DATEDIFF instead of SQLite's julianday)
            if ( in_array( DealWarning::LAST_ACTIVITY->value, $_POST['warnings'] ) ) {
                $deals->whereRaw("DATEDIFF(NOW(), deals.hubspot_updatedAt) > ?", [DealWarning::LAST_ACTIVITY->days()]);
            }
            if ( in_array( DealWarning::CLOSE_DATE->value, $_POST['warnings'] ) ) {
                $deals->whereRaw("DATEDIFF(NOW(), deals.closedate) > ?", [0]);
            }
            if ( in_array( DealWarning::STAGE_TIME_SPEND->value, $_POST['warnings'] ) ) {
                $deals->whereRaw("DATEDIFF(NOW(), deals.hs_date_entered) > ?", [DealWarning::STAGE_TIME_SPEND->days()]);
            }
            if ( in_array( DealWarning::CREATION_DATE->value, $_POST['warnings'] ) ) {
                $deals->whereRaw("DATEDIFF(NOW(), deals.createdate) > ?", [DealWarning::CREATION_DATE->days()]);
            }
            if ( in_array( 'AllWarnings', $_POST['warnings'] ) ) {
                $deals->whereRaw("((DATEDIFF(NOW(), deals.hubspot_updatedAt) > ?) OR
                                   (DATEDIFF(NOW(), deals.closedate) > ?) OR
                                   (DATEDIFF(NOW(), deals.hs_date_entered) > ?) OR
                                   (DATEDIFF(NOW(), deals.createdate) > ?)
                                   )",
                                 [DealWarning::LAST_ACTIVITY->days(), 0, DealWarning::STAGE_TIME_SPEND->days(), DealWarning::CREATION_DATE->days()]);
            }
            //$deals=$deals_with_warnings[0];
            //if (count($deals_with_warnings)>1)
            //    for($x=1;$x<count($deals_with_warnings);$x++ ){
            //        $deals->union($deals_with_warnings[$x]);
            //    }
        }
        //$currency = Auth::user()->currency();
        return DataTables::of($deals)
                         ->addIndexColumn() //DT_RowID
                         ->setRowId('id')
                        ->editColumn('createdate', function($row) {
                           return $row->createdate ? $row->createdate->toFormattedDateString() : '-';
                        })
                        ->editColumn('closedate', function($row) {
                           return $row->closedate ? $row->closedate->toFormattedDateString() : '-';
                        })
                         ->editColumn('amount', function($row) {
                            if($row->amount)
                                //return currency_format($row->amount, $currency);
                                return $row->amount;
                            else return '';
                         })
                         ->addColumn('owner_name',function($row){
                            if($row->member)
                                return $row->member->firstName.' '.$row->member->lastName ;
                            else return '';
                         })
                         ->addColumn('owner_firstName',function($row){
                             if($row->member)
                                 return $row->member->firstName;
                             else return '';
                         })
                         ->addColumn('owner_lastName',function($row){
                             if($row->member)
                                 return $row->member->lastName;
                             else return '';
                         })
                         ->addColumn('owner_id',function($row){
                             if($row->member)
                                 return $row->member->id ;
                             else return '';
                         })
                         ->addColumn('status',function($row){
                            return $row->stage->label;
                         })
                         ->addColumn('probability',function($row){
                            return ($row->stage->probability*100) . '%';
                         })
                         ->addColumn('warnings',function($row){
                             $warnings=[];
                             $now = Carbon::now();

                             $updated = new Carbon($row->hubspot_updatedAt);
                             if ( $updated->diffInDays($now, false) > DealWarning::LAST_ACTIVITY->days()) $warnings[]=DealWarning::LAST_ACTIVITY;

                             $close = new Carbon($row->closedate);
                             if ( $close->diffInDays($now, false) > 0) $warnings[]=DealWarning::CLOSE_DATE;

                             $stage_enter = new Carbon($row->hs_date_entered);
                             if ( $stage_enter->diffInDays($now, false) > DealWarning::STAGE_TIME_SPEND->days()) $warnings[]=DealWarning::STAGE_TIME_SPEND;

                             $create = new Carbon($row->createdate);
                             if ( $create->diffInDays($now, false) > DealWarning::CREATION_DATE->days()) $warnings[]=DealWarning::CREATION_DATE;

                             return $warnings;
                         });
                         //->make();
    }

    public function datatable(){
        return $this->makeDatatable(null)->make();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Deal  $deal
     * @return \Illuminate\Http\Response
     */
    public function edit(Deal $deal)
    {
        if ($this->authorize('update', $deal)){
            $organization = Auth::user()->organization();
            if (!$organization) {
                abort(404);
            }
            $stages = $deal->pipeline->stages()->orderBy('probability')->pluck('label')->toArray();
            $forecast_categories = ForecastCategory::where('organization_id', $organization->id)->orderBy('display_order')->get();
            $index = array_search($deal->stage->label, $stages);
            return view( 'client.deal.edit' )->with( [
                'deal'   => $deal,
                'stages' => $stages,
                'index' => $index,
                'forecast_categories' => $forecast_categories,
            ]);
        }else abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Deal  $deal
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Deal $deal)
    {
//        $validated = $request->validate([
//            'kara_probability' => 'numeric|min:0|max:100',
//        ]);
        $input = $request->all();
        if ( array_key_exists('data', $input) ) $input = $input['data'][$deal->id];

        $properties = [];
        if (isset($input['amount'])) $properties['amount'] = $input['amount'];
        if (isset($input['stage_name'])) {
            $stage=Stage::where('pipeline_id', $deal->pipeline_id)->where('label', $input['stage_name'])->first();
            if ($stage) $input['stage_id'] = $stage->id;
            unset($input['stage_name']);
        }
        if (isset($input['stage_id'])) {
            $stage=Stage::find($input['stage_id']);
            $properties['dealstage'] = $stage->hubspot_id;
        }
        if (isset($input['closedate'])) {
            $date = Carbon::parse($input['closedate']);
            $properties['closedate'] = $date->format('Y-m-d');
        }
        if (isset($input['kara_probability'])) $input['kara_probability'] = $input['kara_probability']/100;
        $properties['hs_manual_forecast_category'] = is_null($input['hs_manual_forecast_category']) ? "" : $input['hs_manual_forecast_category'];
        if (isset($input['hs_next_step'])) $properties['hs_next_step'] = $input['hs_next_step'];
        $hubspot_deal_id = $deal->hubspot_id;
        if ( !empty($properties) ){
            if  (HubspotDeals::updateDeal(null, Auth::user(), $hubspot_deal_id, $properties)) {
                $deal->update( $input );
            }
        }else{
            $deal->update( $input );
        }

        $datatableResponse = $this->makeDatatable($deal->id)->make();
        $data = $datatableResponse->getData();
        if (isset($data->data) && count($data->data) > 0) {
            $data = array( 'data' => array( $data->data[0] ) );
        } else {
            $data = array( 'data' => array() );
        }

        die(json_encode($data));
        //$this->datatable();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Deal  $deal
     * @return \Illuminate\Http\Response
     */
    public function destroy(Deal $deal)
    {
        //
    }

    public function sync_hubspot_deals(){
        try {
            $user = Auth::user();
            
            // Check if user has HubSpot refresh token
            if (!$user->hubspot_refreshToken) {
                return response()->json([
                    'success' => false,
                    'error' => 'HubSpot not connected',
                    'message' => 'Please connect your HubSpot account first. Go to Settings > HubSpot Sync to authenticate.'
                ], 400);
            }
            
            $organization = $user->organization();
            if (!$organization) {
                return response()->json([
                    'success' => false,
                    'error' => 'No organization found',
                    'message' => 'Please ensure you are assigned to an organization.'
                ], 400);
            }
            
            $hubspot = \App\Helpers\HubspotClientHelper::createFactory($user);
            $time_start = microtime(true);//seconds
            
            // Check if pipelines exist - deals require pipelines to be synced first
            $pipelinesCount = \App\Models\Pipeline::where('organization_id', $organization->id)->count();
            $syncedItems = [];
            
            if ($pipelinesCount == 0) {
                // Sync pipelines first (required for deals)
                \Log::info('No pipelines found, syncing pipelines first', ['organization_id' => $organization->id]);
                HubspotPipelines::sync_with_hubspot($hubspot, $user, $organization->id);
                $syncedItems[] = 'pipelines';
                
                // Also sync forecast categories and owners (often needed)
                HubspotForecastCategories::sync_with_hubspot($hubspot, $user, $organization->id);
                HubspotOwners::sync_with_hubspot($hubspot, $user, $organization->id);
                $syncedItems[] = 'forecast categories';
                $syncedItems[] = 'owners';
            }
            
            // Now sync deals
            HubspotDeals::sync_with_hubspot($hubspot, $user, $organization->id);
            $syncedItems[] = 'deals';
            
            $time_end = microtime(true);//seconds
            $duration = round($time_end - $time_start, 2);
            
            $message = 'Successfully synchronized: ' . implode(', ', $syncedItems) . ' (' . $duration . ' seconds)';
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'duration' => $duration . ' seconds',
                'synced_items' => $syncedItems
            ], 200);
            
        } catch (\HubSpot\Client\Crm\Deals\ApiException $e) {
            \Log::error('HubSpot API Exception in deal sync', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'user_id' => Auth::id(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ]);
            
            $message = config('app.debug')
                ? 'Failed to sync with HubSpot: ' . $e->getMessage() . ' (Code: ' . $e->getCode() . ')'
                : 'Failed to sync with HubSpot. Please try again later.';
            
            return response()->json([
                'success' => false,
                'error' => 'HubSpot API Error',
                'message' => $message
            ], 500);
            
        } catch (\Exception $e) {
            \Log::error('Deal sync error: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ]);
            
            $message = config('app.debug')
                ? 'An error occurred while synchronizing deals: ' . $e->getMessage()
                : 'An error occurred while synchronizing deals. Please try again later.';
            
            return response()->json([
                'success' => false,
                'error' => 'Sync failed',
                'message' => $message
            ], 500);
        }
    }

    public function stages(Deal $deal){
        //return $deal->pipeline->stages->pluck('id','label');
        $stages = $deal->pipeline->stages()->get();
        $data = array(
            'data'=> array(),
            'options' => array(
                'stages' => array()
            )
        );

        foreach ($stages as $stage){
            $data['options']['stages'][]= array(
                'label' => $stage->label,
                'value' => $stage->id
            );
        }

        return json_encode($data);
    }
}
