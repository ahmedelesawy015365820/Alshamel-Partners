<?php

namespace Modules\Stock\Repositories;

interface StockSalePurchaseDetailInterface
{

    public function all($request);

    public function find($id);

    public function logs($id);

    public function create($request);

    public function updataData($request);

    public function update($request, $id);

    public function delete($id);
}
