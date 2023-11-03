<?php

namespace Modules\ClubMembers\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\ClubMembers\Entities\CmMember;
use Modules\ClubMembers\Entities\CmTransaction;
use Modules\ClubMembers\Http\Requests\CmTransactionRequest;
use Modules\ClubMembers\Repositories\CmTransaction\CmTransactionInterface;
use Modules\ClubMembers\Transformers\CheckDateMemberTransactionResource;
use Modules\ClubMembers\Transformers\CmMemberTransactionsResource;
use Modules\ClubMembers\Transformers\CmReportTransactionsResource;
use Modules\ClubMembers\Transformers\CmSponsorPaidTransactionsResource;
use Modules\ClubMembers\Transformers\CmTransactionResource;
use Modules\ClubMembers\Transformers\GetMemberVotingResource;
use Modules\ClubMembers\Transformers\UnPaidMembers;

class CmTransactionController extends Controller
{
    public function __construct(private CmTransactionInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function all(Request $request)
    {
        $models = $this->modelInterface->all($request);

        return responseJson(200, 'success', CmTransactionResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function find($id)
    {

        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new CmTransactionResource($model));
    }

    public function findCmMemberLastTransaction($id)
    {
        $model = $this->modelInterface->findCmMemberLastTransaction($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new CmTransactionResource($model));
    }

    public function create(CmTransactionRequest $request)
    {
        $model = $this->modelInterface->create($request);
        return responseJson(200, 'success', CmTransactionResource::collection($model), null);
    }

    public function update(CmTransactionRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->modelInterface->update($request, $id);

        return responseJson(200, 'success', new CmTransactionResource($model));
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

    public function MemberTransactions($member_id)
    {

        $models = CmTransaction::where("cm_member_id", $member_id)->get();

        return responseJson(200, 'success', CmMemberTransactionsResource::collection($models));

    }

    public function checkDateMemberTransaction(Request $request)
    {

        $query = CmMember::whereDate('last_transaction_date', '<', $request->date)
        ->where('last_transaction_year', $request->year)
        ->with(['cmTransaction'=> function ($q) use ($request) {
            $q->where('year', $request->year );
        }]);

        if ($request->member_status_id) {
            $query->where('member_status_id', $request->member_status_id);
        }


        $models['data'] = $query->paginate($request->per_page);
        $models['paginate'] = true;

        return responseJson(200, 'success', CheckDateMemberTransactionResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);

        // return responseJson(200, 'success', $models['data'], $models['paginate'] ? getPaginates($models['data']) : null);

        // $query = CmTransaction::whereDate('date', '<=', $request->date)->where('year', $request->year);

        // if ($request->member_status_id) {
        //     $query->whereHas('member', function ($q) use ($request) {
        //         $q->where('member_status_id', $request->member_status_id);
        //     });
        // }

        // $models['data'] = $query->paginate($request->per_page);

        // $models['paginate'] = true;
        // return responseJson(200, 'success', CheckDateMemberTransactionResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function UpdateMemberTransactionPaid(Request $request)
    {
        $transactionArray = CmTransaction::whereDate('date', '<=', $request->date)->where('year', $request->year)->pluck('cm_member_id')->toArray();
        $models['data'] = CmMember::whereIn('id', $transactionArray)->where('member_kind_id', 1)->update(['financial_status_id' => 3]);
        $models['paginate'] = true;
        return responseJson(200, 'success', 'Updated Successfully');
    }



    public function unpaidMemberTransaction(Request $request)
    {

        $transactionMemberIds = CmTransaction::where('year', $request->year)
            ->pluck('cm_member_id')
            ->toArray();

        $unpaidMembersQuery = CmMember::where('member_status_id', 1)
            ->whereNotIn('id', $transactionMemberIds)
            ->with('lastCmTransaction')
            ->paginate($request->per_page);

        return responseJson(200, 'success', UnPaidMembers::collection($unpaidMembersQuery), getPaginates($unpaidMembersQuery));
    }

    public function updateUnpaidMemberTransaction(Request $request)
    {
        $transactionMemberIds = CmTransaction::where('year', $request->year)
            ->pluck('cm_member_id')
            ->toArray();

        $members = CmMember::where('member_status_id', 1)
            ->whereNotIn('id', $transactionMemberIds)
            ->update(['financial_status_id' => 2]);

        if ($members) {

            return responseJson(200, 'success', 'Updated Successfully');
        } else {
            return 'error';
        }
    }

    public function memberTransactionBeforeAndAfterDate(Request $request)
    {
        $models = DB::table('cm_transactions')->whereDate('date', '>=', $request->start_date)->whereDate('date', '<=', $request->end_date)
            ->select('document_no', DB::raw('count(*) as total'))
            ->groupBy('document_no')
            ->having('total', '>', 1);
        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', $models['data'], $models['paginate'] ? getPaginates($models['data']) : null);

    }

    public function getMemberIsDocument(Request $request)
    {
        $models = CmTransaction::with('member:id,full_name')->where('document_no', $request->document_no)->whereDate('date', '>=', $request->start_date)->whereDate('date', '<', $request->end_date);

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        } //

        return responseJson(200, 'success', $models['data'], $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function memberTransactionPaidAfterDate(Request $request)
    {

        $query = CmMember::whereDate('last_transaction_date', '>', $request->date)
        ->where('last_transaction_year', $request->year)
        ->with(['cmTransaction'=> function ($q) use ($request) {
            $q->where('year', $request->year );
        }]);

        if ($request->member_status_id) {
                $query->where('member_status_id', $request->member_status_id);
        }

        $models['data'] = $query->paginate($request->per_page);
        $models['paginate'] = true;

        return responseJson(200, 'success', CheckDateMemberTransactionResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);

        // return responseJson(200, 'success', $models['data'], $models['paginate'] ? getPaginates($models['data']) : null);



        // $query = CmTransaction::whereDate('date', '>', $request->date)->where('year', $request->year);

        // if ($request->member_status_id) {
        //     $query->whereHas('member', function ($q) use ($request) {
        //         $q->where('member_status_id', $request->member_status_id);
        //     });
        // }

        // $models['data'] = $query->paginate($request->per_page);

        // $models['paginate'] = true;
        // return responseJson(200, 'success', CheckDateMemberTransactionResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);

    }

    public function UpdateMemberTransactionPaidAfterDate(Request $request)
    {
        $transactionArray = CmTransaction::whereDate('date', '>', $request->date)->where('year', $request->year)->pluck('cm_member_id')->toArray();
        $models['data'] = CmMember::whereIn('id', $transactionArray)->where('member_kind_id', 1)->update(['financial_status_id' => 4]);

        $models['paginate'] = true;
        return responseJson(200, 'success', 'Updated Successfully');
    }

    public function getMemberVoting(Request $request)
    {

        $models['data'] = CmMember::where('financial_status_id', $request->financial_status_id)->whereHas('cmTransaction', function ($query) use ($request) {
            $query->where('year', '<=', $request->year - $request->membership_numbers);
        })->paginate($request->per_page);

        $models['paginate'] = true;
        return responseJson(200, 'success', GetMemberVotingResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);

    }

    public function updateMemberVoting(Request $request)
    {

        $models = CmMember::where('financial_status_id', $request->financial_status_id)->whereHas('cmTransaction', function ($query) use ($request) {
            $query->where('year', '<=', $request->year - $request->membership_numbers);
        })->update(
            [
                'status_id' => $request['status_id'],
                'member_type_id' => $request['member_type_id'],
            ]
        );

        return responseJson(200, 'success', 'Updated Successfully');

    }

    public function reportCmTransactions(Request $request)
    {
        $models = $this->modelInterface->reportCmTransactions($request);

        return responseJson(200, 'success', CmReportTransactionsResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);

    }

    public function reportSponsorPaidTransactions(Request $request)
    {
        $models = $this->modelInterface->reportSponsorPaidTransactions($request);

        return responseJson(200, 'success', CmSponsorPaidTransactionsResource::collection($models['data'], $models['paginate'] ? getPaginates($models['data']) : null));

    }

}
