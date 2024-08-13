<?php

namespace App\Http\Controllers;

use App\Models\ReconcileReport;
use App\Http\Requests\StoreReconcileReportRequest;
use App\Http\Requests\UpdateReconcileReportRequest;

class ReconcileReportController extends Controller
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
     * @param  \App\Http\Requests\StoreReconcileReportRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreReconcileReportRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ReconcileReport  $reconcileReport
     * @return \Illuminate\Http\Response
     */
    public function show(ReconcileReport $reconcileReport)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ReconcileReport  $reconcileReport
     * @return \Illuminate\Http\Response
     */
    public function edit(ReconcileReport $reconcileReport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateReconcileReportRequest  $request
     * @param  \App\Models\ReconcileReport  $reconcileReport
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateReconcileReportRequest $request, ReconcileReport $reconcileReport)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ReconcileReport  $reconcileReport
     * @return \Illuminate\Http\Response
     */
    public function destroy(ReconcileReport $reconcileReport)
    {
        //
    }
}
