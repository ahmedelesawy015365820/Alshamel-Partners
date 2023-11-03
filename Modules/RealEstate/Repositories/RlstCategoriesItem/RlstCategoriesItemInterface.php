<?php

namespace Modules\RealEstate\Repositories\RlstCategoriesItem;

interface RlstCategoriesItemInterface
{

    public function all($request);

    public function find($id);

    public function create($request);

    // public function update($request, $id);

    public function logs($id);

    public function checkDelete($category_item_id, $item_id);
    
    public function delete($category_item_id, $item_id);

}
