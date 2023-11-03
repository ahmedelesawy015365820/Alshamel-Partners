<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Status;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Priority;
use App\Models\Depertment;
use App\Models\GeneralCustomer;
use App\Http\Requests\AllRequest;
use App\Http\Requests\TaskRequest;
use Illuminate\Routing\Controller;
use App\Http\Resources\TaskResource;
use App\Http\Resources\TaskLogResource;
use App\Http\Requests\AllTaskPostRequest;
use Illuminate\Contracts\Support\Renderable;
use Modules\BoardsRent\Entities\EmployeeTask;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TaskController extends Controller
{
    public function __construct(private Task $model, private Media $media)
    {
    }

    public function find($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new TaskResource($model));
    }

    public function all(AllRequest $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->employee_id) {
            $models->where('employee_id', $request->employee_id);
        }

        if ($request->customer_id) {
            $models->where('customer_id', $request->customer_id);
        }


        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', $models['data'], $models['paginate'] ? getPaginates($models['data']) : null);
    }


    public function allPost(AllTaskPostRequest $request)
    {
        $models = $this->model->where(function ($q) use ($request) {
            if ($request->customer_ids) {
                $q->whereIn('customer_id', $request->customer_ids);
            }
            if ($request->status_ids) {
                $q->whereIn('status_id', $request->status_ids);
            }
            if ($request->department_ids) {
                $q->whereIn('department_id', $request->department_ids);
            }
            if ($request->department_task_ids) {
                $q->whereIn('department_task_id', $request->department_task_ids);
            }
            if ($request->employee_ids) {
                $q->whereIn('employee_id', $request->employee_ids);
            }

            if ($request->year) {
                $q->whereYear('execution_date', $request->year);
            }

            if ($request->months_number) {
                $q->whereMonth('execution_date', $request->months_number);
            }
        })->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', TaskResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(TaskRequest $request)
    {
        $type = request()->header('type');
        if ($type == 'admin') {
            $user_name = request()->header('admin_name');
            $user_id = null;
        } else {
            $user = \App\Models\User::find($request->header('user_id'));
            if ($user) {
                $user_name = $user->name;
                $user_id = $user->id;
            } else {
                $user_name = null;
                $user_id = null;
            }
        }

        $model = $this->model->create($request->all());

        if ($request->supervisors) {
            $model->supervisors()->attach($request->supervisors, [
                'type' => 'supervisor'
            ]);
        }

        if ($request->attentions) {
            $model->attentions()->attach($request->attentions, [
                'type' => 'attention'
            ]);
        }


        $model->logs()->create([
            'action' => "create",
            'message' => "قام $user_name بانشاء مهمة في " . now()->format('Y-m-d H:i:s'),
            'message_e' => "$user_name has created a task in " . now()->format('Y-m-d H:i:s'),
            'user_id' => $user_id,
            'data' => $model
        ]);

        $model->refresh();
        return responseJson(200, 'created', new TaskResource($model));
    }

    public function update($id, TaskRequest $request)
    {
        $type = request()->header('type');
        if ($type == 'admin') {
            $user_name = request()->header('admin_name');
            $user_id = null;
        } else {
            $user = \App\Models\User::find($request->header('user_id'));
            if ($user) {
                $user_name = $user->name;
                $user_id = $user->id;
            } else {
                $user_name = null;
                $user_id = null;
            }
        }
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }
        $model->update($request->validated());

        // in every record update make message for what old and what new

        if ($request->contact_person != $model->contact_person) {
            $model->logs()->create([
                'action' => "update",
                'message_e' => "$user_name has updated the contact person from $model->contact_person to $request->contact_person in "
                    . now()->format('Y-m-d H:i:s'),
                'message' => "قام $user_name بتعديل معلومات التواصل من $model->contact_person إلى $request->contact_person في "
                    . now()->format('Y-m-d H:i:s'),
                'user_id' => $user_id,
            ]);
        }

        if ($request->contact_person_phone != $model->contact_person_phone) {
            $model->logs()->create([
                'action' => "update",
                'message_e' => "$user_name has updated the contact person phone from $model->contact_person_phone to $request->contact_person_phone in "
                    . now()->format('Y-m-d H:i:s'),
                'message' => "قام $user_name بتعديل رقم هاتف المسؤول من $model->contact_person_phone إلى $request->contact_person_phone في "
                    . now()->format('Y-m-d H:i:s'),


                'user_id' => $user_id,
            ]);
        }

        if ($request->task_title != $model->task_title) {
            $model->logs()->create([
                'action' => "update",
                'message' => "قام $user_name بتعديل عنوان المهمه من $model->task_title الى $request->task_title",
                'message_e' => "$user_name has updated the task title from $model->task_title to $request->task_title",
                'user_id' => $user_id,
            ]);
        }

        if ($request->execution_date != $model->execution_date) {
            $model->logs()->create([
                'action' => "update",
                'message' => "قام $user_name بتعديل تاريخ التنفيذ من $model->execution_date الى $request->execution_date في "
                    . now()->format('Y-m-d H:i:s'),
                'message_e' => "$user_name has updated the execution date from $model->execution_date to $request->execution_date in "
                    . now()->format('Y-m-d H:i:s'),
                'user_id' => $user_id,
            ]);
        }

        if ($request->execution_duration != $model->execution_duration) {
            $model->logs()->create([
                'action' => "update",
                'message_e' => "$user_name has updated the execution duration from $model->execution_duration to $request->execution_duration in "
                    . now()->format('Y-m-d H:i:s'),
                'message' => "قام $user_name بتعديل مدة التنفيذ من $model->execution_duration الى $request->execution_duration في "
                    . now()->format('Y-m-d H:i:s'),
                'user_id' => $user_id,
            ]);
        }

        if ($request->execution_end_date != $model->execution_end_date) {

            $model->logs()->create([
                'action' => "update",
                'message_e' => "$user_name has updated the execution end date from $model->execution_end_date to $request->execution_end_date in "
                    . now()->format('Y-m-d H:i:s'),
                'message' => "قام $user_name بتعديل تاريخ انتهاء التنفيذ من $model->execution_end_date الى $request->execution_end_date في "
                    . now()->format('Y-m-d H:i:s'),
                'user_id' => $user_id,
            ]);
        }


        if ($request->notification_date != $model->notification_date) {

            $model->logs()->create([
                'action' => "update",
                'message_e' => "$user_name has updated the notification date from $model->notification_date to $request->notification_date in "
                    . now()->format('Y-m-d H:i:s'),
                'message' => "قام $user_name بتعديل تاريخ التنبيه من $model->notification_date الى $request->notification_date في "
                    . now()->format('Y-m-d H:i:s'),
                'user_id' => $user_id,
            ]);
        }

        if ($request->start_time != $model->start_time) {

            $model->logs()->create([
                'action' => "update",
                'message_e' => "$user_name has updated the start time from $model->start_time to $request->start_time in "
                    . now()->format('Y-m-d H:i:s'),
                'message' => "قام $user_name بتعديل وقت البدء من $model->start_time الى $request->start_time في "
                    . now()->format('Y-m-d H:i:s'),
                'user_id' => $user_id,
            ]);
        }

        if ($request->end_time != $model->end_time) {

            $model->logs()->create([
                'action' => "update",
                'message_e' => "$user_name has updated the end time from $model->end_time to $request->end_time in "
                    . now()->format('Y-m-d H:i:s'),
                'message' => "قام $user_name بتعديل وقت الانتهاء من $model->end_time الى $request->end_time في "
                    . now()->format('Y-m-d H:i:s'),
                'user_id' => $user_id,
            ]);
        }

        if ($request->department_task_id != $model->department_task_id) {
            $old = \App\Models\DepertmentTask::find($model->department_task_id);
            $new = \App\Models\DepertmentTask::find($request->department_task_id);
            $model->logs()->create([
                'action' => "update",
                'message_e' => "$user_name has updated the task from $old->name to $new->name in "
                    . now()->format('Y-m-d H:i:s'),
                'message' => "قام $user_name بتعديل المهمه من $old->name الى $new->name في "
                    . now()->format('Y-m-d H:i:s'),
                'user_id' => $user_id,
            ]);
        }

        if ($request->employee_id != $model->employee_id) {
            $old = \App\Models\Employee::find($model->employee_id);
            $new = \App\Models\Employee::find($request->employee_id);
            $model->logs()->create([
                'action' => "update",
                'message_e' => "$user_name has updated the responsible employee from $old->name to $new->name in "
                    . now()->format('Y-m-d H:i:s'),
                'message' => "قام $user_name بتعديل الموظف المسؤول من $old->name الى $new->name في "
                    . now()->format('Y-m-d H:i:s'),
                'user_id' => $user_id,
            ]);
        }

        if ($request->customer_id != $model->customer_id) {
            $old = GeneralCustomer::find($model->customer_id);
            $new = GeneralCustomer::find($request->customer_id);
            $model->logs()->create([
                'action' => "update",
                'message_e' => "$user_name has updated the customer from $old->name to $new->name in "
                    . now()->format('Y-m-d H:i:s'),
                'message' => "قام $user_name بتعديل العميل من $old->name الى $new->name في "
                    . now()->format('Y-m-d H:i:s'),
                'user_id' => $user_id,
            ]);
        }

        if ($request->department_id != $model->department_id) {
            $old = Depertment::find($model->department_id);
            $new = Depertment::find($request->department_id);
            $model->logs()->create([
                'action' => "update",
                'message_e' => "$user_name has updated the department from $old->name to $new->name in "
                    . now()->format('Y-m-d H:i:s'),
                'message' => "قام $user_name بتعديل القسم من $old->name الى $new->name في "
                    . now()->format('Y-m-d H:i:s'),
                'user_id' => $user_id,
            ]);
        }

        if ($request->status_id != $model->status_id) {
            $old = Status::find($model->status_id);
            $new = Status::find($request->status_id);
            $model->logs()->create([
                'action' => "update",
                'message_e' => "$user_name has updated the status from $old->name to $new->name in "
                    . now()->format('Y-m-d H:i:s'),
                'message' => "قام $user_name بتعديل الحالة من $old->name الى $new->name في "
                    . now()->format('Y-m-d H:i:s'),
                'user_id' => $user_id,
            ]);
        }

        if ($request->note != $model->note) {

            $model->logs()->create([
                'action' => "update",
                'message_e' => "$user_name has updated the note from $model->note to $request->note in "
                    . now()->format('Y-m-d H:i:s'),
                'message' => "قام $user_name بتعديل الملاحظات من $model->note الى $request->note في "
                    . now()->format('Y-m-d H:i:s'),
                'user_id' => $user_id,
            ]);
        }

        if ($request->location_id != $model->location_id) {
            $old = Location::find($model->location_id);
            $new = Location::find($request->location_id);
            $model->logs()->create([
                'action' => "update",
                'message_e' => "$user_name has updated the location from $old->name to $new->name in "
                    . now()->format('Y-m-d H:i:s'),
                'message' => "قام $user_name بتعديل الموقع من $old->name الى $new->name في "
                    . now()->format('Y-m-d H:i:s'),
                'user_id' => $user_id,
            ]);
        }

        if ($request->priority_id != $model->priority_id) {
            $old = Priority::find($model->priority_id);
            $new = Priority::find($request->priority_id);
            $model->logs()->create([
                'action' => "update",
                'message_e' => "$user_name has updated the priority from $old->name to $new->name in "
                    . now()->format('Y-m-d H:i:s'),
                'message' => "قام $user_name بتعديل الأولوية من $old->name الى $new->name في "
                    . now()->format('Y-m-d H:i:s'),
                'user_id' => $user_id,
            ]);
        }
        // supervisors

        if ($request->supervisors) {
            $old = $model->supervisor()->pluck('id')->toArray();
            $new = $request->supervisors;
            $old = Employee::whereIn('id', $old)->pluck('name')->toArray();
            $new = Employee::whereIn('id', $new)->pluck('name')->toArray();
            $old = implode(',', $old);
            $new = implode(',', $new);
            $model->logs()->create([
                'action' => "update",
                'message_e' => "$user_name has updated the supervisors from $old to $new in " .
                    now()->format('Y-m-d H:i:s'),
                'message' => "قام $user_name بتعديل المشرفين من $old الى $new في " .
                    now()->format('Y-m-d H:i:s'),
                'user_id' => $user_id,
            ]);
        }

        if ($request->attentions) {
            $old = $model->attention()->pluck('id')->toArray();
            $new = $request->attentions;
            $old = Employee::whereIn('id', $old)->pluck('name')->toArray();
            $new = Employee::whereIn('id', $new)->pluck('name')->toArray();
            $old = implode(',', $old);
            $new = implode(',', $new);
            $model->logs()->create([
                'action' => "update",
                'message_e' => "$user_name has updated the attentions from $old to $new in "
                    . now()->format('Y-m-d H:i:s'),
                'message' => "قام $user_name بتعديل المهتمين من $old الى $new في "
                    . now()->format('Y-m-d H:i:s'),

                'user_id' => $user_id,
            ]);
        }

        if ($request->supervisors) {
            $model->supervisors()->delete();
            $model->supervisors()->attach($request->supervisors, [
                'type' => 'supervisor'
            ]);
        }

        if ($request->attentions) {
            $model->supervisors()->delete();
            $model->supervisors()->attach($request->attentions, [
                'type' => 'attention'
            ]);
        }

        if (request()->media && !request()->old_media) { // if there is new media and no old media
            $model->clearMediaCollection('media');
            foreach (request()->media as $media) {
                uploadImage($media, [
                    'model_id' => $model->id,
                    'model_type' => get_class($this->model),
                ]);
            }
        }

        if (request()->old_media && !request()->media) { // if there is old media and no new media
            $model->media->whereNotIn('id', request()->old_media)->each(function (Media $media) {
                $media->delete();
            });
        }

        if (request()->old_media && request()->media) { // if there is old media and new media
            $model->media->whereNotIn('id', request()->old_media)->each(function (Media $media) {
                $media->delete();
            });
            foreach (request()->media as $image) {
                uploadImage($image, [
                    'model_id' => $model->id,
                    'model_type' => get_class($this->model),
                ]);
            }
        }

        if (!request()->old_media && !request()->media) { // if this is no old media and new media
            $model->clearMediaCollection('media');
        }
        $model->refresh();


        return responseJson(200, 'updated', new TaskResource($model));
    }

    public function logs($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }
        $logs = $model->logs()->orderBy('created_at', 'DESC')->get();
        return responseJson(200, 'success', TaskLogResource::collection($logs));
    }

    public function delete($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }
        $type = request()->header('type');
        if ($type == 'admin') {
            $user_name = request()->header('admin_name');
            $user_id = null;
        } else {
            $user = \App\Models\User::find(request()->header('user_id'));
            if ($user) {
                $user_name = $user->name;
                $user_id = $user->id;
            } else {
                $user_name = null;
                $user_id = null;
            }
        }
        $model->logs()->create([
            'action' => "delete",
            'message_e' => "$user_name has deleted the task in "
                . now()->format('Y-m-d H:i:s'),
            'message' => "قام $user_name بحذف المهمة في "
                . now()->format('Y-m-d H:i:s'),
            'user_id' => $user_id,
        ]);
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
            $type = request()->header('type');
            if ($type == 'admin') {
                $user_name = request()->header('admin_name');
                $user_id = null;
            } else {
                $user = \App\Models\User::find(request()->header('user_id'));
                if ($user) {
                    $user_name = $user->name;
                    $user_id = $user->id;
                } else {
                    $user_name = null;
                    $user_id = null;
                }
            }
            $model->logs()->create([
                'action' => "delete",
                'message' => "قام $user_name بحذف المهمة",
                'message_e' => "$user_name has deleted the task",
                'user_id' => $user_id,
            ]);
            $model->delete();
        });
        return responseJson(200, 'deleted');
    }

    public function getDropDown($request)
    {
        $models = $this->model->select('id', 'task_title');

        if ($request->per_page) {
            $modelData = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $modelData = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', $modelData['data'], $modelData['paginate'] ? getPaginates($modelData['data']) : null);
    }
}
