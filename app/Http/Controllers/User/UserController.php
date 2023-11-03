<?php

namespace App\Http\Controllers\User;

use Adyen\Model\Checkout\CheckoutPaymentMethod;
use Adyen\Model\Checkout\PaymentRequest;
use App\Http\Requests\AllRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\AllDropListResource;
use App\Http\Resources\User\UserLoginResource;
use App\Http\Resources\User\UserResource;
use App\Repositories\User\UserInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    public function __construct(private UserInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function login(\App\Http\Requests\User\LoginUserRequest $request)
    {
        // login with sanctum using email and password
        $user = \App\Models\User::where('email', $request->email)->first();
        if ($user) {
            if (\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
                $token = $user->createToken('token')->plainTextToken;
                return responseJson(200, 'success', ['token' => $token, 'user' => new UserLoginResource($user)]);
            }
        }
        return responseJson(400, 'invalid credentials');
    }

    public function profile(Request $request)
    {
        $user = $request->user();
        return responseJson(200, 'success', ['user' => new UserLoginResource($user)]);
    }

    public function find($id)
    {

        $model = $this->modelInterface->find($id);
        return responseJson(200, 'success', new UserResource($model));
    }

    public function all(AllRequest $request)
    {

        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', UserResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(StoreUserRequest $request)
    {
        $model = $this->modelInterface->create($request);
        $model->refresh();
        return responseJson(200, 'success', new UserResource($model));

    }

    public function update(UpdateUserRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $this->modelInterface->update($request, $id);
        $model->refresh();
        return responseJson(200, 'success', new UserResource($model));
    }

    public function logs($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

        $logs = $this->modelInterface->logs($id);
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));

    }

    public function delete($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $this->modelInterface->delete($id);

        return responseJson(200, 'success');
    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $this->modelInterface->delete($id);
        }
        return responseJson(200, __('Done'));
    }


    public function getDropDown(Request $request)
    {
        $models = $this->modelInterface->getName($request);
        return responseJson(200, 'success', AllDropListResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

}
