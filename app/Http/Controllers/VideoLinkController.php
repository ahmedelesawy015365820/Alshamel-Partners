<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVideoLinkRequest;
use App\Http\Requests\UpdateVideoLinkRequest;
use App\Models\VideoLink;

class VideoLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreVideoLinkRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreVideoLinkRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\VideoLink  $videoLink
     * @return \Illuminate\Http\Response
     */
    public function show(VideoLink $videoLink)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\VideoLink  $videoLink
     * @return \Illuminate\Http\Response
     */
    public function edit(VideoLink $videoLink)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateVideoLinkRequest  $request
     * @param  \App\Models\VideoLink  $videoLink
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateVideoLinkRequest $request, VideoLink $videoLink)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\VideoLink  $videoLink
     * @return \Illuminate\Http\Response
     */
    public function destroy(VideoLink $videoLink)
    {
        //
    }
}
