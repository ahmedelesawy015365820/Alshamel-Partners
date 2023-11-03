<?php

namespace Modules\Stock\Repositories;

interface OpenningBalanceInterface
{

    public function all($request);
    public function getAll($request);

    public function find($id);

    public function logs($id);

    public function create($request);

    public function rowUpdate($request);

    public function checkDate($id);

    public function getWalletEntier($id);

    public function delete($id);
}
