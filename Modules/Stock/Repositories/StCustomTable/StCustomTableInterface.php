<?php

namespace Modules\Stock\Repositories\StCustomTable;

interface StCustomTableInterface
{

    public function all($request);
    public function find($id);
    public function create($request);
    public function update($request);
    public function logs($id);
    public function delete($id);

}
