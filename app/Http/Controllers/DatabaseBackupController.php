<?php

namespace App\Http\Controllers;

use App\Http\Requests\AllRequest;
use App\Http\Resources\DatabaseBackupResource;
use App\Models\DatabaseBackup;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DatabaseBackupController extends Controller
{

    public function __construct(private DatabaseBackup $model)
    {
        $this->model = $model;
    }

    public function all(AllRequest $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', DatabaseBackupResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(Request $request)
    {
        // check if folder uploads exists
        if (!\Illuminate\Support\Facades\File::exists(public_path('uploads'))) {
            // if not create it
            \Illuminate\Support\Facades\File::makeDirectory(public_path('uploads'));
        }
        // check if folder Laravel exists
        if (!\Illuminate\Support\Facades\File::exists(public_path('uploads/' . env('APP_NAME')))) {
            // if not create it
            \Illuminate\Support\Facades\File::makeDirectory(public_path('uploads/' . env('APP_NAME')));
        }
        \Illuminate\Support\Facades\Artisan::call("backup:run --only-db --disable-notifications");

        $files = \Illuminate\Support\Facades\File::files(public_path('uploads/' . env('APP_NAME')));
        foreach ($files as $file) {
            $path = $file->getFilename();
            $model = $this->model->where(
                'path',
                $path
            )->first();
            if (!$model) {
                $this->model->create([
                    'path' => $path,
                ]);
            }
        }

        return responseJson(200, 'created');

    }

}
