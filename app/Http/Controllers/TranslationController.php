<?php

namespace App\Http\Controllers;

use App\Models\Translation;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    public function update(Request $request)
    {
        $com_id = $request->company_id;
        foreach ($request->translations as $key => $translation) {
            Translation::query()->updateOrCreate(
                [
                    'company_id' => $com_id,
                    'key' => $key,
                ],
                [
                    'company_id' => $com_id,
                    'key' => $key,
                    'default_en' => $translation['default_en'],
                    'default_ar' => $translation['default_ar'],
                    'new_ar' => $translation['new_ar'],
                    'new_en' => $translation['new_en'],
                ]
            );
        }
        return responseJson(200, 'success');
    }

    public function delete(Request $request)
    {
        $ids = $request->ids;
        if (!is_array($ids)) {
            $ids = [];
        }
        Translation::query()->where('company_id', '!=', 0)->whereIn('id', $ids)->delete();
        return responseJson(200, 'success');
    }
}
