<?php

namespace App\Traits;

use Illuminate\Support\Facades\Validator;

trait ApiResponser
{
    protected function successResponse($data, $message = null, $code = 200)
    {
        return response()->json([
            'status' => $code,
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function errorResponse($message = null, $code = 422)
    {
        return response()->json([
            'status' => $code,
            'success' => false,
            'message' => $message,
            'data' => null,
        ], $code);
    }
}
