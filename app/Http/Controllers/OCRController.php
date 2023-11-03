<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use thiagoalessio\TesseractOCR\TesseractOCR;

class OCRController extends Controller
{
    public function upload(Request $request)
    {
        $path = Storage::disk('public')->putFile('images', $request->image);
        $tesseractOCR = new TesseractOCR(public_path("storage/$path"));
        $tesseractOCR->lang('ara');
        $text = $tesseractOCR->run();
        return response()->json(
            [
                'success' => true,
                'data' => $text,
            ],
            Response::HTTP_OK);
    }
}
