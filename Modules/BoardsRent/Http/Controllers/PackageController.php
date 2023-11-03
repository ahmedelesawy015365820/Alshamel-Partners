<?php

namespace Modules\BoardsRent\Http\Controllers;

use App\Http\Requests\AllRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\BoardsRent\Entities\Package;
use Modules\BoardsRent\Http\Requests\PackageRequest;
use Modules\BoardsRent\Transformers\Package\AllBRentPackageResource;
use Modules\BoardsRent\Transformers\Package\RelationBRentPackagePanelResourceResource;
use Modules\BoardsRent\Transformers\PackageResource;
use Modules\BoardsRent\Transformers\PanelResource;

class PackageController extends Controller
{
    public function __construct(private Package $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new AllBRentPackageResource($model));
    }
    public function relationPackagePanel($id)
    {
        $model = $this->model->find($id);

        return responseJson(200, 'success', new RelationBRentPackagePanelResourceResource($model));
    }

    public function all(AllRequest $request)
    {
        $models = $this->model->filter($request)->data()->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', AllBRentPackageResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(PackageRequest $request)
    {
        $model = $this->model->create($request->validated());
        $model->panels()->sync($request->panels);
        $model->refresh();
        return responseJson(200, 'created', new AllBRentPackageResource($model));

    }

    public function update($id, PackageRequest $request)
    {

        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->update($request->validated());
        if($request->panels){$model->panels()->sync($request->panels);}
        $model->refresh();

        return responseJson(200, 'updated', new AllBRentPackageResource($model));
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

    public function delete($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }
        if ($model->hasChildren()) {
            return responseJson(400, 'can not delete this package because it related data');
        }

        $model->delete();
        return responseJson(200, 'deleted');
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
            if ($model->hasChildren()) {
                return responseJson(400, 'can not delete this package because it related data');
            }
            $model->delete();
        });
        return responseJson(200, 'deleted');
    }

    public function panels(Request $request, $id)
    {
        $model = $this->model->find($id);

        if (!$model) {
            return responseJson(404, 'not found');
        }

        $busy_panels = $model->panels()->filter($request)->orderBy('created_at', 'DESC');

        $free_panels = \Modules\BoardsRent\Entities\Panel::WhereDoesntHave('packages')->filter($request);

        if ($request->per_page) {        $model->panels()->sync($request->panels);

            $busy_panels = ['data' => $busy_panels->paginate($request->per_page), 'paginate' => true];
            $free_panels = ['data' => $free_panels->paginate($request->per_page), 'paginate' => true];

        } else {
            $busy_panels = ['data' => $busy_panels->get(), 'paginate' => false];
            $free_panels = ['data' => $free_panels->get(), 'paginate' => false];
        }
        return responseJson(200, 'success', [
            'free' => ['data' => PanelResource::collection($free_panels['data']), 'pagination' => $free_panels['paginate'] ? getPaginates($free_panels['data']) : null],
            'busy' => ['data' => PanelResource::collection($busy_panels['data']), 'pagination' => $busy_panels['paginate'] ? getPaginates($busy_panels['data']) : null],
        ]);

    }

    public function toggle($panel_id, $package_id)
    {
        $panel = \Modules\BoardsRent\Entities\Panel::find($panel_id);
        if (!$panel) {
            return responseJson(404, 'not found');
        }
        $package = $this->model->find($package_id);
        if (!$package) {
            return responseJson(404, 'not found');
        }

        if ($panel->packages()->where('package_id', $package_id)->count() > 0) {
            $panel->packages()->detach($package_id);
            return responseJson(200, 'success', 'removed');
        } else {
            $panel->packages()->attach($package_id);
            return responseJson(200, 'success', 'added');
        }
    }

}
