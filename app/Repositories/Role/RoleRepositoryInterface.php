<?php

namespace App\Repositories\Role;

interface RoleRepositoryInterface
{
    public function getAll($request);

    public function logs($id);
}
