<?php


namespace App\Repositories\RoleType;


interface RoleTypeRepositoryInterface
{
    public function getAll($request);

    public function logs($id);
}
