<?php

namespace Modules\ClubMembers\Repositories\CmMemberRequest;

use Illuminate\Support\Facades\DB;
use Modules\ClubMembers\Entities\CmHistoryTransform;
use Modules\ClubMembers\Entities\CmMember;
use Modules\ClubMembers\Entities\CmMemberPermission;
use Modules\ClubMembers\Entities\CmMemberRequest;
use Modules\ClubMembers\Entities\CmTypePermission;

class CmMemberRequestRepository implements CmMemberRequestInterface
{
    public function __construct(private CmMemberRequest $model)
    {
        $this->model = $model;

    }

    public function all($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->member_type_id) {
            $models->where('member_type_id', $request->member_type_id);
        }
        if ($request->member_types) {
            $models->whereIn('member_type_id', [1,2]);
        }

        if ($request->member_id){
            $models->where('id', $request->member_id);
        }
        if ($request->doesNotHaveTransaction)
        {
            $models->whereDoesntHave('cmTransaction');
        }
        if ($request->hasTransaction)
        {
            $models->whereHas('cmTransaction');
        }

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } elseif ($request->limet){
            return ['data' => $models->take($request->limet)->get(), 'paginate' => false];
        }else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }



    public function find($id)
    {
        return $this->model->find($id);
    }



    public function create($request)
    {

        return DB::transaction(function () use ($request) {
            $full_name = ($request['first_name'] ?? '') . ' ' .
                ($request['second_name'] ?? '') . ' ' .
                ($request['third_name'] ?? '') . ' ' .
                ($request['last_name'] ?? '') . ' ' .
                ($request['family_name'] ?? '') ;
            $request['applying_number'] = $this->incrementApplyingNumber();
            return $this->model->create(array_merge($request,['full_name' => $full_name]));
        });
    }

    public function incrementApplyingNumber()
    {
        $model = $this->model->get()->last();
        if ($model)
        {
            return $model['applying_number'] + 1;
        }
        return 1 ;
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $full_name = ($request['first_name'] ?? '') . ' ' .
                ($request['second_name'] ?? '') . ' ' .
                ($request['third_name'] ?? '') . ' ' .
                ($request['last_name'] ?? '') . ' ' .
                ($request['family_name'] ?? '') ;
            $this->model->where("id", $id)->update(array_merge($request,['full_name' => $full_name]));
        });
        $model = $this->model->find($id);
        return $model;
        //
    }


    public function updateDecline($request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $this->model->where("id", $id)->update(array_merge($request->all(), ['acceptance' => 2]));
        });

        $model = $this->model->find($id);
        return $model;
    }


    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }

    public function delete($id)
    {
        $model = $this->find($id);
        $model->delete();
    }

    public function checkNationalId($request)
    {
        $member = CmMember::where('national_id',$request->national_id)->with(['status','lastCmTransaction','financialStatus'])->first();
        if ($member)
        {
            return $member;
        }
        return '';
    }


}
