<?php

namespace Modules\ClubMembers\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ClubMembers\Entities\CmMember;
use Modules\ClubMembers\Entities\CmMemberReject;
use Modules\ClubMembers\Entities\CmMemberStatus;
use Modules\ClubMembers\Transformers\CmMemberRejectResource;
use Modules\ClubMembers\Transformers\CmNewMemberStatusResource;

class CmNewMemberStatusController extends Controller
{
    public function __construct(private CmMemberStatus $model, CmMember $modelMember)
    {
        $this->model = $model;
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', CmNewMemberStatusResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);


    }
}
