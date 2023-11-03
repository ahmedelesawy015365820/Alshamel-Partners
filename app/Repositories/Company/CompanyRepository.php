<?php

namespace App\Repositories\Company;

use App\Models\Company;
use Illuminate\Support\Facades\Storage;

class CompanyRepository implements CompanyRepositoryInterface
{
    public $model;
    public function __construct(Company $model)
    {
        $this->model = $model;
    }

    public function getAllCompanies()
    {
        return $this->model->data()->get();
    }

    public function create(array $data)
    {
        if (isset($data["logo"])) {
            $data["logo"]->store('companies');
            $data["logo"] = $data["logo"]->hashName();
        }
        return $this->model->create($data);
    }

    public function show($id)
    {
        return $this->model->data()->find($id);
    }

    public function update($data, $id)
    {
        if (isset($data["logo"])) {
            Storage::disk('companies')->delete($this->model->find($id)->logo);
            $data["logo"]->store('companies');
            $data["logo"] = $data["logo"]->hashName();
        }
        return $this->model->find($id)->update($data);
    }

    public function destroy($id)
    {
        return $this->model->find($id)->delete();
    }

}
