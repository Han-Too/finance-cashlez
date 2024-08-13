<?php

namespace App\Http\Controllers;

use App\Models\ReconcileList;
use App\Http\Requests\StoreReconcileListRequest;
use App\Http\Requests\UpdateReconcileListRequest;

class ReconcileListController extends Controller
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
     * @param  \App\Http\Requests\StoreReconcileListRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreReconcileListRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ReconcileList  $reconcileList
     * @return \Illuminate\Http\Response
     */
    public function show(ReconcileList $reconcileList)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ReconcileList  $reconcileList
     * @return \Illuminate\Http\Response
     */
    public function edit(ReconcileList $reconcileList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateReconcileListRequest  $request
     * @param  \App\Models\ReconcileList  $reconcileList
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateReconcileListRequest $request, ReconcileList $reconcileList)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ReconcileList  $reconcileList
     * @return \Illuminate\Http\Response
     */
    public function destroy(ReconcileList $reconcileList)
    {
        //
    }
}
