<?php

namespace Modules\ClubMembers\Repositories\CmMembershipRenewal;

use Illuminate\Support\Facades\DB;
use Modules\ClubMembers\Entities\CmMembershipRenewal;

class CmMembershipRenewalRepository implements CmMembershipRenewalInterface
{
    public function __construct(private CmMembershipRenewal $model)
    {
        $this->model = $model;
    }

    public function all($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->date_search) {
            $models->whereDate('from', "<=", $request->date_search);
            $models->whereDate('to', ">=", $request->date_search);
        }

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create($request)
    {
        //return $request->memberships_renewals;

        //foreach ($request->memberships_renewals as $membership_renewal){
        //    return $membership_renewal;
        //}

        // creation in database happens here !!
        DB::transaction(function () use ($request) {
            foreach ($request['memberships_renewals'] as $membership_renewal) {
                $this->model->create($membership_renewal);
            }

        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $this->model->where("id", $id)->update($request);
        });

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

}
