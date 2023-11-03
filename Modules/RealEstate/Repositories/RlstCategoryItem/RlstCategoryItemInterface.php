<?php

namespace Modules\RealEstate\Repositories\RlstCategoryItem;

interface RlstCategoryItemInterface
{

    public function all($request);

    public function getRootNodes();

    public function getChildNodes($id);

    public function find($id);

    public function create($request);

    public function update($request, $id);

    public function logs($id);

    public function delete($id);





}
