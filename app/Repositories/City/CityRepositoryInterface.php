<?php


namespace App\Repositories\City;


interface CityRepositoryInterface
{
    public function getAll($request);

    public function logs($id);
    public function getName($request);

}
