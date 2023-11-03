<?php

namespace Modules\BoardsRent\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderTaskRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "start_date" => "required|date",
            "end_date" => "required|date",
            "order_id" => "required|exists:boards_rent_orders,id",
            "task_id" => "required|exists:boards_rent_tasks,id",
            "customer_id" => "nullable|exists:general_customers,id",
            "user_id" => "nullable|exists:general_users,id",
            "department_id" => "nullable|exists:general_departments,id",
            "note" => "nullable|string",
            "must_completed" => "nullable|in:true,false",


        ];
    }

}
