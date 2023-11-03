<?php

namespace App\Repositories\GeneralCustomer;

interface GeneralCustomerRepositoryInterface
{

    public function all($request);

    public function logs($id);

    public function find($id);

    public function create($request);

    public function update($request, $id);

    public function delete($id);

    public function checkSupplier($request);

    public function getName($request);


}
