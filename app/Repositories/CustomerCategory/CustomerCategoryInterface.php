<?php

namespace App\Repositories\CustomerCategory;



interface CustomerCategoryInterface
{
    public function all($request);
    public function find($id);
    public function create($request);
    public function update($request,$id);
    public function logs($id);
    public function delete($id);

    public function processJsonData(array $data);
    public function getName($request);


}
