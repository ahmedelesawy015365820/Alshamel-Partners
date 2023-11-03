<?php

namespace Modules\RealEstate\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\RealEstate\Entities\RlstBuildingPolicy;
use Modules\RealEstate\Entities\RlstPolicy;
use Illuminate\Support\Facades\Artisan;
use Modules\RealEstate\Http\Requests\RlstBuildingPolicyRequest;
use Modules\RealEstate\Transformers\RlstBuildingPolicyResource;
use Modules\RealEstate\Http\Requests\RlstDivideOwnerCompanyShareRequest;

class RlstBuildingPolicyController extends Controller
{
    public function __construct(private RlstBuildingPolicy $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        $model = $this->model->data()->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new RlstBuildingPolicyResource($model));
    }

    public function all(Request $request)
    {
        $models = $this->model->data()->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', RlstBuildingPolicyResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }


    public function create(RlstBuildingPolicyRequest $request)
    {
       //return $request['building-policy'][0];
        
        foreach ($request['building-policy'] as $building_wallet) {
            $model = $this->model->create($building_wallet);
        }

        return responseJson(200, 'success', new RlstBuildingPolicyResource($model));
    }

    public function delete($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }
        $model->delete();
        return responseJson(200, 'deleted');
    }


    public function update($id, RlstBuildingPolicyRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }
        
        if ($request['building-policy']) {
       
            foreach ($request['building-policy'] as $building_policy) {
               $model->update($building_policy);
            }

        }
        return responseJson(200, 'updated', new RlstBuildingPolicyResource($model));
    }

    public function bulkDelete()
    {

        $ids = request()->ids;
        if (!$ids) {
            return responseJson(400, 'ids is required');
        }
        $models = $this->model->whereIn('id', $ids)->get();
        if ($models->count() != count($ids)) {
            return responseJson(404, 'not found');
        }
        $models->each(function ($model) {
            $model->delete();
        });
        return responseJson(200, 'deleted');
    }

    public function logs($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $logs = $model->activities()->orderBy('created_at', 'DESC')->get();
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));
    }

    // RlstDivideOwnerCompanyShareRequest

    public function divideOwnerCompanyShare(Request $request)
    {
    
        Artisan::call('db:seed', [
            '--class' => \Modules\RealEstate\Database\Seeders\RlstPoliciesTableTableSeeder::class,
        ]);


    // building policy is required

        if(!$request->building_id){
            return responseJson(400, 'building id is required');
        }

        if(!$request->year){
            return responseJson(400, 'year is required');
        }

        if(!$request->month){
            return responseJson(400, 'month is required');
        }

        // which building policy record to use?
        // selecting near policy record to the selected date
        // year is compared first, then month only if year is equal

        $buildingPolicy = RlstBuildingPolicy::where('building_id', $request->building_id)
                                    ->where('year', '<', $request->year)
                                    ->orWhere(function($query) use ($request){
                                        $query->where('year', $request->year)
                                        ->where('month', '<=', $request->month);
                                    })
                                    ->orderBy('year', 'DESC')
                                    ->orderBy('month', 'DESC')
                                    ->first();

        return $buildingPolicy;
        
        if (!$buildingPolicy) {
            return responseJson(404, 'no suitable building policy found');
        }


        $policyId = $buildingPolicy['policy_id'];


        // actual or accrued revenues?

        if($buildingPolicy['collected_rent_type'] == 'accrued' && !isset($request->accrued_revenues)){
            return responseJson(400, 'accrued revenues is required');
        }

        if($buildingPolicy['collected_rent_type'] == 'actual' && !isset($request->actual_revenues)){
            return responseJson(400, 'actual revenues is required');
        }

        if($buildingPolicy['collected_rent_type'] == 'accrued'){
            $revenues = $request->accrued_revenues;
        }else{
            $revenues = $request->actual_revenues;
        }

        // Expenses deducted ?

        if($buildingPolicy['after_expenses'] == 1 && !isset($request->expenses_amount)){
            return responseJson(400, 'expenses amount is required');
        }

        if($buildingPolicy['after_expenses'] == 1)
            $revenues = $revenues - $request->expenses_amount;

        // Extra revenues added ?

        if($buildingPolicy['plus_extra_revenues'] == 1 && !isset($request->extra_revenues)){
            return responseJson(400, 'extra revenues is required');
        }

        if($buildingPolicy['plus_extra_revenues'] == 'yes')
            $revenues = $revenues + $request->extra_revenues;


        $companyPaysRest = $buildingPolicy['company_pays_rest'];
        $ownerPaysRest = $buildingPolicy['owner_pays_rest'];


        $revenuesDivided = [];
        $revenuesDivided['company_takes'] = 0;
        $revenuesDivided['owner_takes'] = 0;
        $revenuesDivided['company_pays'] = 0;
        $revenuesDivided['owner_pays'] = 0;

        switch ($policyId){
            
            case 1: // Owner has a fixed amount
                
                $revenuesDivided['owner_takes'] = $buildingPolicy['amount'];
                
                if($revenues >= $buildingPolicy['amount'])
                    $revenuesDivided['company_takes'] = $revenues - $buildingPolicy['amount'];
                else{
                    if($companyPaysRest == 'yes')
                        $revenuesDivided['company_pays'] = $buildingPolicy['amount'] - $revenues;
                }
                break;
                
            case 2: // Company has a fixed amount OR percent
                
                if($revenues * $buildingPolicy['percent'] / 100 >= $buildingPolicy['amount']){
               
                    $revenuesDivided['company_takes'] = $revenues * $buildingPolicy['percent'] / 100;
                    $revenuesDivided['owner_takes'] = $revenues - $revenuesDivided['company_takes'];
               
                }else{ // company takes a fixed amount
                    
                    $revenuesDivided['company_takes'] = $buildingPolicy['amount'];

                    if($revenues > $buildingPolicy['amount']){
                        $revenuesDivided['owner_takes'] = $revenues - $buildingPolicy['amount'];
                    }else{ 
                        if($ownerPaysRest == 'yes'){
                            $revenuesDivided['owner_pays'] = $buildingPolicy['amount'] - $revenues;
                        }else{
                            $revenuesDivided['company_takes'] = $revenues;
                        }

                    } // owner pays the rest?

                } // company takes a fixed amount
                
                break;

            case 3: //Company has a fixed percent
                
                $revenuesDivided['company_takes'] = $revenues * $buildingPolicy['percent'] / 100;
                $revenuesDivided['owner_takes'] = $revenues - $revenuesDivided['company_takes'];
                break;

            case 4: // Company has a fixed amount 

                if($revenues > $buildingPolicy['amount']){
                    
                    $revenuesDivided['company_takes'] = $buildingPolicy['amount'];
                    $revenuesDivided['owner_takes'] = $revenues - $buildingPolicy['amount'];
              
                }else{ // company takes a fixed amount

                    if($ownerPaysRest == 'yes'){

                        $revenuesDivided['company_takes'] = $buildingPolicy['amount'];
                        $revenuesDivided['owner_pays'] = $buildingPolicy['amount'] - $revenues;
                  
                    }else{ // company takes the whole amount

                        $revenuesDivided['company_takes'] = $revenues;
                    }

                } // company takes a fixed amount
                    
                break;
            
            case 5: //Company has a fixed amount plus a percent if the amount increased beyond a certain value

                if($revenues > $buildingPolicy['amount']){

                    $extra = $revenues - $buildingPolicy['amount'];

                    if($extra > $buildingPolicy['percent_amount'])
                        $revenuesDivided['company_takes'] = $buildingPolicy['amount'] + $extra * $buildingPolicy['percent'] / 100;

                    else
                        $revenuesDivided['company_takes'] = $buildingPolicy['amount'];

                    $revenuesDivided['owner_takes'] = $revenues - $revenuesDivided['company_takes'];

                }else{ // company takes a fixed amount

                    if($ownerPaysRest == 'yes'){

                        $revenuesDivided['company_takes'] = $buildingPolicy['amount'];
                        $revenuesDivided['owner_pays'] = $buildingPolicy['amount'] - $revenues;
                    }    
                } // company takes a fixed amount

                break;

            default:
                return responseJson(400, 'valid policy id is required');
                break;
        }

        return responseJson(200, 'success', $revenuesDivided);
    }


}
