<?php

namespace App\Http\Controllers;

use App\Models\DraftBackOffice;
use App\Http\Requests\StoreDraftBackOfficeRequest;
use App\Http\Requests\UpdateDraftBackOfficeRequest;

class DraftBackOfficeController extends Controller
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
     * @param  \App\Http\Requests\StoreDraftBackOfficeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDraftBackOfficeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DraftBackOffice  $draftBackOffice
     * @return \Illuminate\Http\Response
     */
    public function show(DraftBackOffice $draftBackOffice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DraftBackOffice  $draftBackOffice
     * @return \Illuminate\Http\Response
     */
    public function edit(DraftBackOffice $draftBackOffice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateDraftBackOfficeRequest  $request
     * @param  \App\Models\DraftBackOffice  $draftBackOffice
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDraftBackOfficeRequest $request, DraftBackOffice $draftBackOffice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DraftBackOffice  $draftBackOffice
     * @return \Illuminate\Http\Response
     */
    public function destroy(DraftBackOffice $draftBackOffice)
    {
        //
    }
}
