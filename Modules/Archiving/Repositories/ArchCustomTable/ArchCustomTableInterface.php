<?php

namespace Modules\Archiving\Repositories\ArchCustomTable;

interface ArchCustomTableInterface
{

    public function all($request);
    public function find($id);
    public function create($request);
    public function update($request);
    public function logs($id);
    public function delete($id);

}
