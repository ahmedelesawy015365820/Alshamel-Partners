<?php

namespace App\Repositories\Document;



interface DocumentInterface
{
    public function all($request);
    public function find($id);
    public function findWhereIsAdmin($id);
    public function create($request);
    public function createFromAdmin($request);
    public function update($request,$id);
    public function logs($id);
    public function delete($id);
    public function getName($request);
    public function documentMoney($request);

}
