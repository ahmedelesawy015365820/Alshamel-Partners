<?php

namespace Modules\BoardsRent\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderTaskRequest extends FormRequest
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
            "date" => "required|array",
            "date.*" => "required|array",
            "date.*.start" => "required|date",
            "date.*.end" => "required|date",
            "data.*.order_id" => "required|exists:boards_rent_orders,id",
            "data.*.task_id" => "required|exists:boards_rent_tasks,id",
            "data.*.customer_id" => "nullable|exists:general_customers,id",
            "data.*.user_id" => "nullable|exists:general_users,id",
            "data.*.department_id" => "nullable|exists:general_departments,id",
            "data.*.note" => "nullable|string",
            "data.*.must_completed" => "nullable|in:true,false",
            "company_id"=>'nullable',

        ];
    }

}
