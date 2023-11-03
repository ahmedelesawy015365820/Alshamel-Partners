<?php

namespace App\Repositories\Employee;

use App\Models\Employee;
use App\Models\GeneralEmployeeManager;
use Illuminate\Support\Facades\DB;

class EmployeeRepository implements EmployeeInterface
{

    public function __construct(private Employee $model)
    {
        $this->model = $model;

    }

    public function all($request)
    {
        $models = $this->model->data()->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');
        if ($request->customer_handel) {
            $models->where('customer_handel', '!=', 'non_customer');
        }
        if ($request->is_salesman) {
            $models->where('is_salesman', 'true');
        }

        if ($request->manager_id) {
            $models->where('manager_id', $request->manager_id);
        }
        if ($request->manage_others) {
            $models->where('manage_others', $request->manage_others);
        }
        if ($request->id) {
            $models->where(function ($query) use ($request) {
                $query->where('id', $request->id)
                    ->orWhere('manager_id', $request->id);
            });
        }
        if ($request->manage) {
            if ($request->employee_id) {
                $models->where(function ($query) use ($request) {
                    $query->where('id', '!=', $request->employee_id);
                });
            }
        }

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } elseif ($request->limet) {
            return ['data' => $models->take($request->limet)->get(), 'paginate' => false];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function find($id)
    {
        return $this->model->data()->find($id);
    }

    public function create($request)
    {
        DB::transaction(function () use ($request) {
            $model = $this->model->create($request->all());
            if ($request->plans) {
                $model->plans()->sync($request->plans);
            }

            if ($request->manager_ids) {
                $model->managers()->attach($request->manager_ids);
            }

            return $model;
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $model = $this->model->find($id);
            $model->update($request->except(["media"]));
            if ($request->plans) {
                $model->plans()->sync($request->plans);
            }

            if ($request->manager_ids) {
                $model->managers()->sync($request->manager_ids);
            }
        });

    }

    public function delete($id)
    {
        $model = $this->find($id);
        $managers = GeneralEmployeeManager::where('employee_id', $model->id)->get();
        foreach ($managers as $manager) {
            $manager->delete();
        }
        $model->delete();
    }

    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }

    public function processJsonData(array $data)
    {
        $maxId = $this->model->max('id') ?? 0;

        $messages = [];
        foreach ($data['data'] as $item) {
            switch ($item['op']) {
                case 'ADD':
                    $id = $item['id'] ?? ++$maxId;
                    if ($this->model->where('id', $id)->exists()) {
                        $messages[] = ['id' => $id, 'status' => 'id already exists'];
                    } else {
                        $this->model->insert([
                            'id' => $id,
                            'name' => $item['name'],
                            'name_e' => $item['name_e'],
                            'department_id' => $item['department_id'] ?? null,
                            'job_id' => $item['job_id'] ?? null,
                            'manager_id' => $item['manager_id'] ?? null,
                            'branch_id' => $item['branch_id'] ?? null,
                            'manage_others' => $item['manage_others'] ?? 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $messages[] = ['id' => $id, 'status' => 'added successfully'];
                    }
                    break;
                case 'UPD':
                    $id = $item['id'];
                    $model = $this->model->find($id);

                    if ($model) {
                        $this->model->where('id', $id)->update([
                            'name' => $item['name'],
                            'name_e' => $item['name_e'],
                            'department_id' => $item['department_id'] ?? null,
                            'job_id' => $item['job_id'] ?? null,
                            'manager_id' => $item['manager_id'] ?? null,
                            'branch_id' => $item['branch_id'] ?? null,
                            'manage_others' => $item['manage_others'] ?? 1,
                            'updated_at' => now(),
                        ]);
                        $messages[] = ['id' => $id, 'status' => 'updated successfully'];
                    } else {
                        $messages[] = ['id' => $id, 'status' => 'record not found'];
                    }
                    break;
                case 'DEL':
                    $id = $item['id'];
                    $model = $this->model->find($item['id']);
                    if ($model) {

                        $model->delete();
                        $messages[] = ['id' => $id, 'status' => 'deleted successfully'];
                    } else {
                        $messages[] = ['id' => $id, 'status' => 'record not found'];
                    }
                    break;
            }
        }

        return $messages;
    }

    public function getName($request)
    {
        $models = $this->model->select('id', 'name', 'name_e');

        if ($request->customer_handel) {
            $models->where('customer_handel', '!=', 'non_customer');
        }
        if ($request->is_salesman) {
            $models->where('is_salesman', 'true');
        }

        if ($request->manager_id) {
            $models->where('manager_id', $request->manager_id);
        }

        // if ($request->department_id) {
        //     $models->where('department_id', $request->department_id);
        // }
        if ($request->manage_others) {
            $models->where('manage_others', $request->manage_others);
        }
        if ($request->department_id) {
            $models->where('department_id', $request->department_id);
        }
        if ($request->id) {
            $models->where(function ($query) use ($request) {
                $query->where('id', $request->id)
                    ->orWhere('manager_id', $request->id);
            });
        }
        if ($request->manage) {
            if ($request->employee_id) {
                $models->where(function ($query) use ($request) {
                    $query->where('id', '!=', $request->employee_id);
                });
            }
        }

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } elseif ($request->limet) {
            return ['data' => $models->take($request->limet)->get(), 'paginate' => false];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

}
