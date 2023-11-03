<?php

namespace App\Repositories\Currency;

interface CurrencyRepositoryInterface
{
    public function getAll($request);

    // public function create($data);

    public function logs($id);

    public function find($id);

    public function update($request, $id);

    public function delete($id);

}
