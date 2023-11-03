<?php


namespace App\Traits;


use Illuminate\Http\Request;

trait BulkDeleteTrait
{
    public $repo;
    public function bulkDelete(Request $request)
    {
        $this->repo = @$this->repository?:$this->modelInterface;
        foreach ($request->ids as $id) {
            $model = $this->repo->find($id);
            $arr = [];
            try {
                if ($model){
                    if ($model->hasChildren()) {
                        $arr[] = $id;
                        continue;
                    }
                }
            }catch (\Exception $exception){

            }
            if ($model){
                $this->repo->delete($id);
            }
        }
        if (count($arr) > 0) {
            return responseJson(400, __('some items has relation cant delete'));
        }
        return responseJson(200, __('Done'));
    }
}
