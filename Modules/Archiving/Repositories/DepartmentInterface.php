<?php

namespace Modules\Archiving\Repositories;

interface DepartmentInterface
{

    public function all($request);
    public function parentDepartment($request);

    public function find($id);

    public function logs($id);

    public function create($request);

    public function update($request, $id);

    public function delete($id);

}
