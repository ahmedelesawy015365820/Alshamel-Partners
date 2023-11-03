<?php

namespace App\Repositories\GeneralItem;

interface GeneralItemInterface
{

    public function all($request);

    public function find($id);

    public function create($request);

    public function update($request, $id);

    public function delete($id);

    public function logs($id);


}
