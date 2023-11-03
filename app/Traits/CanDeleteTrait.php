<?php

namespace App\Traits;

trait CanDeleteTrait
{

    public function destroy($id)
    {
        if (!$this->canDeleteModel($id)) {
            return responseJson(400, __("this item has children and can't be deleted remove it's children first"));
        }
        $this->repository->delete($id);
        return responseJson(200, __('deleted'));
    }

    public function canDeleteModel($id)
    {
        $model = $this->repository->find($id);
        try {
            if (hasChildren($model)) {
                return false;
            }
        } catch (\Exception$exception) {

        }

        return true;
    }

}
