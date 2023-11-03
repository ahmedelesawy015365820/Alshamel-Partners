<?php

namespace Modules\ClubMembers\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ClubMembers\Entities\CmMember;

use Modules\ClubMembers\Http\Requests\CmAcceptMembersRequest;
use Modules\ClubMembers\Http\Requests\CmMemberAcceptRequest;
use Modules\ClubMembers\Http\Requests\CmMemberDeclineRequest;
use Modules\ClubMembers\Http\Requests\CmMemberRequest;
use Modules\ClubMembers\Http\Requests\CmUpdateAcceptedMemberRequest;
use Modules\ClubMembers\Repositories\CmMember\CmMemberInterface;
use Modules\ClubMembers\Transformers\CmMemberPermissionReportResource;
use Modules\ClubMembers\Transformers\CmMemberResource;
use Modules\ClubMembers\Transformers\GetSponsorMembersResource;
use Modules\ClubMembers\Transformers\ReportMembertResource;

class CmMemberController extends Controller
{

    public function __construct(private CmMemberInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function all(Request $request)
    {
        $models = $this->modelInterface->all($request);

        return responseJson(200, 'success', CmMemberResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }



    public function find($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new CmMemberResource($model));
    }

    public function allAcceptancePending(Request $request)
    {
        $models = $this->modelInterface->allAcceptancePending($request);

        return responseJson(200, 'success', CmMemberResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(CmMemberRequest $request)
    {
        $model = $this->modelInterface->create($request->validated());
        return responseJson(200, 'success', new CmMemberResource($model));
    }

    public function update(CmMemberRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->modelInterface->update($request->validated(), $id);

        return responseJson(200, 'success', new CmMemberResource($model));
    }

    public function acceptMember(CmMemberAcceptRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);

        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->modelInterface->updateAcceptance($request, $id);

        return responseJson(200, 'success', new CmMemberResource($model));
    }

    public function declineMember(CmMemberDeclineRequest $request, $id)
    {
        $model = $this->modelInterface->updateDecline($request, $id);

        return responseJson(200, 'success', new CmMemberResource($model));
    }

    public function delete($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        if ($model->haveChildren) {
            return responseJson(400, __('message.parent have children'));
        }
        $this->modelInterface->delete($id);

        return responseJson(200, 'success');
    }

    public function logs($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $logs = $this->modelInterface->logs($id);
        return responseJson(200, 'success', $logs);
    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $model = $this->modelInterface->find($id);
            $arr = [];
            if ($model->have_children) {
                $arr[] = $id;
                continue;
            }
            $this->modelInterface->delete($id);
        }
        if (count($arr) > 0) {
            return responseJson(400, __('some items has relation cant delete'));
        }
        return responseJson(200, __('Done'));
    }

    public function updateSponsor(Request $request, $sponsor_id)
    {
        // return now()->format('d-m-Y H:i:s');

        $model = CmMember::where("sponsor_id", $sponsor_id)->get();

        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->modelInterface->updateSponsor($request, $sponsor_id);

        return responseJson(200, 'success', CmMemberResource::collection($model));

    }

    public function allAcceptance(Request $request)
    {
        $models = $this->modelInterface->allAcceptance($request);

        return responseJson(200, 'success', CmMemberResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function acceptMembers(CmAcceptMembersRequest $request)
    {
        $this->modelInterface->acceptMembers($request->validated());

        return responseJson(200, 'updated successfully');

    }

    public function updateAcceptedMembers(CmUpdateAcceptedMemberRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->modelInterface->update($request->validated(), $id);

        return responseJson(200, 'Updated Successfully', new CmMemberResource($model));
    }


    public function TestTransfer()
    {
        $members = CmMember::where('full_name', 1)->count();
//        $members = CmMember::find(1);
        return $members;
    }
    public function dataMemberFildFullNameTable()
    {
        return  $member =  CmMember::wherehas('transactions')->count();
        ini_set('max_execution_time', 3600); // 3600 seconds = 60 minutes
        set_time_limit(3600);
        ini_set('memory_limit', -1);

        $members = CmMember::get()->chunk(1000);
        foreach ($members as $index => $member):
            foreach ($member as $full_name):
                $names = "$full_name->first_name $full_name->second_name $full_name->third_name $full_name->last_name $full_name->family_name";
                $full_name->update(['full_name' => $names]);

            endforeach;
        endforeach;
        return "Successfully Data Full Name In Table CmMember  ";

    }
    public function dataMemberTable()
    {
       return $members = CmMember::whereHas('transactions')->with('transactions')->where('membership_number','34')->get();
//        foreach ($members as $index => $member):
//            return  $member->transactions['date'];
//            $member->update([
//                'last_transaction_date' => $Last_date,
//                'last_transaction_id' => $member->transactions['id'],
//            ]);
//        endforeach;


        $members = json_decode(file_get_contents(base_path('Modules/ClubMembers/Resources/assets/db/members.json')));
        foreach ($members[2]->data as $data ){
            return          \Carbon\Carbon::parse($data->DOC_DATE)->format('Y-m-d');

        }

//        ini_set('max_execution_time', 3600); // 3600 seconds = 60 minutes
//        set_time_limit(3600);
//        ini_set('memory_limit', -1);

        $ttt = CmMember::all() ;
        $full_name = [];
        foreach ($ttt as $index => $member):
            if ($member->first_name != null){
                $full_name['first_name']  = $member->first_name;
            }
            if ($member->second_name != null){
                $full_name['second_name'] = $member->second_name;
            }
            if ($member->third_name != null){
                $full_name['third_name']  = $member->third_name;
            }
            if ($member->last_name != null){
                $full_name['last_name']  = $member->last_name;
            }
            if ($member->family_name != null){
                $full_name['family_name']  = $member->family_name;
            }
            return   $array = implode(' ', $full_name);

            $member->update(['family_name'=>$full_name]);

        endforeach;

        return "Successfully Data F Table CmMember  ";

    }

    public function getReportCmMember(Request $request)
    {

        $models = $this->modelInterface->reportCmMember($request);
        return responseJson(200, 'success', CmMemberPermissionReportResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);

    }

    public function getUpdateCmMember()
    {

        return $models = $this->modelInterface->updateCmMember();
    }

    public function updateLastTransactionDate()
    {

        return $models = $this->modelInterface->updateLastTransactionDate();
    }

    public function getUpdateFinancialStatusCmMember()
    {
        $members = CmMember::whereNotNull('last_transaction_date')->orWhereIn('member_type_id', [10, 13])->get();

        foreach ($members as $member) {
            if (now()->format('Y') == $member->last_transaction_date->format('Y')) {
                $member->update(['financial_status_id' => 1]);
            }
            if ($member->last_transaction_date == null) {
                $member->update(['financial_status_id' => 1]);
            }
            if (now()->format('Y') != $member->last_transaction_date->format('Y')) {
                $member->update(['financial_status_id' => 2]);
            }
        }

        return "yes";

    }

    public function getSponsors(Request $request)
    {
        $models = $this->modelInterface->getSponsors($request);
        // return $models;

        return responseJson(200,
            'success',
            ['data' => GetSponsorMembersResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null
                , 'memberIds' => $models['memberIds'],
            ]);
    }

    public function updateSponsorID(Request $request)
    {
        // Validate the request data
        $request->validate([
            'member_ids' => 'required|array',
            'sponsor_id' => 'required|integer|exists:cm_sponsers,id',
        ]);

        CmMember::whereIn('id', $request->input('member_ids'))
            ->update(['sponsor_id' => $request->input('sponsor_id')]);

        return responseJson(200, 'success', 'Sponsor updated successfully');

    }

    public function reportToMembers(Request $request)
    {
        $models = $this->modelInterface->reportToMembers($request);
        return responseJson(200, 'success', CmMemberResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function getPublicUpdatePermissionCmMember($id)
    {
       return $models = $this->modelInterface->publicUpdatePermissionCmMember($id);

    }

    public function getMemberForMultiSubscription(Request $request)
    {
        $models = $this->modelInterface->getMemberForMultiSubscription($request);

        return responseJson(200, 'success', CmMemberResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }
}
