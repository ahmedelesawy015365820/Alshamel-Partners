<?php


namespace App\Repositories\DocumentModuleType;


interface DocumentModuleTypeInterface
{
    public function getAll($request);

    public function logs($id);

    public function find($id);

    public function create($request);

    public function update($request, $id);

    public function delete($id);

    public function getName($request);

}
