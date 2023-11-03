<?php

namespace App\Http\Controllers\RoleWorkflow;

use App\Http\Requests\RoleWorkflow\RoleWorkflowRequest;
use App\Http\Requests\RoleWorkflow\EditRoleWorkflowRequest;
use App\Http\Resources\RoleWorkflow\RoleWorkflowResource;
use App\Repositories\RoleWorkflow\RoleWorkflowRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class RoleWorkflowController extends Controller
{
    public $repository;
    public function __construct(RoleWorkflowRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     * @return \response
     */
    public function index(Request $request)
    {

   
        $data = $this->repository->getAll($request);
        return responseJson(200, 'success', RoleWorkflowResource::collection($data['data']), $data['paginate'] ? getPaginates($data['data']) : null);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \response
     */
    public function store(RoleWorkflowRequest $request)
    {

        $this->repository->create($request->validated());
        return responseJson(200, __('done'));

    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return \response
     */
    public function show($id)
    {
        if ($data = $this->repository->find($id)) {
            return responseJson(200, __('Done'), new RoleWorkflowResource($data), 200);
        }
        return responseJson(404, __('not found'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return \response
     */
    public function update(RoleWorkflowRequest $request, $id)
    {
        $data = [];

        if ($request->name) {
            $data['name'] = $request->name;
        }

        if ($request->name_e) {
            $data['name_e'] = $request->name_e;
        }

        $this->repository->update($data, $id);
        return responseJson(200, __('updated'));

    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \response
     */
    public function destroy($id)
    {
        $this->repository->delete($id);
        return responseJson(200, __('deleted'));
    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $this->repository->delete($id);
        }
        return responseJson(200, __('Done'));
    }

    public function logs($id)
    {
        $model = $this->repository->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

        $logs = $this->repository->logs($id);
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));
    }
}
