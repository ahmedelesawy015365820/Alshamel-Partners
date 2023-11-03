<?php

namespace Modules\Stock\Repositories;

interface ClosingBalanceInterface
{

    public function all($request);

    public function find($id);

    public function logs($id);

    public function create($request);

    public function getAllEntier($date);

    public function rowUpdate($request);

    public function delete($date);
}
