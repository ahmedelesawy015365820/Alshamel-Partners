<?php

namespace App\Repositories\RoleScreenHotfield;

interface RoleScreenHotfieldRepositoryInterface
{

    public function getAllRoleScreenHotfields($request);

    public function find($id);

    public function create($request);

    public function update($request, $id);

    public function delete($id);

    public function logs($id);

    public function setting($request);

    public function getSetting($user_id , $screen_id);


}
