<?php

namespace App\Repositories\Governorate;

interface GovernorateInterface
{

    public function all($request);

    public function find($id);

    public function create($request);

    public function update($request, $id);

    public function delete($id);

    public function logs($id);
    
    public function getName($request);



}
