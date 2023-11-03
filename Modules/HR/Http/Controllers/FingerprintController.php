<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HR\Entities\Fingerprint;
use Modules\HR\Transformers\FingerprintResource;

class FingerprintController extends Controller
{

    public function __construct(private Fingerprint $model)
    {
        $this->model = $model;

    }

    public function all(Request $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->att_code) {
            $models->where('att_code', $request->att_code);
        }

        if ($request->att_type) {
            $models->where('att_type', $request->att_type);
        }

        if ($request->vdate) {
            $models->whereDate('vdate', $request->vdate);
        }


        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', FingerprintResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(Request $request)
    {
        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);

        foreach ($data['data'] as $item) {
            $this->model->create([
                'att_code' => $item['att_code'],
                'vdate' => $item['vdate'],
                'att_type' => $item['att_type'],
                'machine_id' => $item['machine_id'],
            ]);
        }

        return responseJson(200, 'success');

    }

}
