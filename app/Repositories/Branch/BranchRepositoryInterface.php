<?php


namespace App\Repositories\Branch;


interface BranchRepositoryInterface
{
    public function getAllBranches($request);
    public function find($id);
    public function create($request);
    public function update($request, $id);
    public function delete($id);

    public function logs($id);
    public function processJsonData(array $data);
    public function getName($request);

}
