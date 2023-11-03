<?php

namespace Modules\Booking\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Booking\Entities\Setting;
use Modules\Booking\Transformers\SettingResource;

class SettingController extends Controller
{
    public function __construct(private Setting $model)
    {
        $this->model = $model;
    }

    public function all(Request $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        // Assuming `UnitResource::collection()` transforms the data into a specific resource format.
        return responseJson(200, 'success', SettingResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function update(Request $request)
    {
        $data = $request->all();

        foreach ($data as $key => $value) {
            $setting = $this->model->where('key', $key)->first();
            if ($setting) {
                $setting->update(['value' => $value]);
            }
        }

        return responseJson(200, 'Updated successfully', SettingResource::collection(Setting::all())->resolve());
    }

}
