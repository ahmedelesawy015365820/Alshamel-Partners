<?php

namespace Modules\ClubMembers\Http\Controllers;

use App\Models\Serial;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ClubMembers\Entities\CmMember;
use Modules\ClubMembers\Entities\CmMemberReject;
use Modules\ClubMembers\Http\Requests\CmMemberRejectRequest;
use Modules\ClubMembers\Transformers\CmMemberRejectResource;


class CmMemberRejectController extends Controller
{
    public function __construct(private CmMemberReject $model,private CmMember $modelMember)
    {
        $this->model = $model;
        $this->modelMember = $modelMember;
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
        } //

        return responseJson(200, 'success', CmMemberRejectResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);


    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create(CmMemberRejectRequest $request)
    {
        if (!$request['serial_id'])
        {
            $serial = Serial::where([['branch_id',$request['branch_id']],['document_id',$request['document_id']]])->first();
            $request['serial_id'] = $serial ? $serial->id:null;
        }
        $model = $this->model->create($request->validated());
        $member = $this->modelMember->find($request->validated()['cm_member_id']);
        $member->update([
            'discharge_reson_id'=>$request['discharge_reson_id'],
            'member_status_id'=>2
            ]);
        if ($request['serial_id'])
        {
            $serials = generalSerialWithIdCreate($model,$request['serial_id']);
        }else{
            $serials = generalSerial($model);
        }
        $model->update([
            "serial_number" => $serials['serial_number'],
            "prefix" => $serials['prefix'],
        ]);

        return responseJson(200, 'success', new CmMemberRejectResource($model));
    }

    public function logs($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $logs = $model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('clubmembers::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('clubmembers::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
