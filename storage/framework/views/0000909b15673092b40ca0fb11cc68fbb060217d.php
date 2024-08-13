<div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">
    <div class="card-header border-0 py-5 rounded-sm mb-5">
        <h3 class="card-title fw-bolder">Advance Search</h3>
        <div class="row gy-3 g-xl-8">
            <div class="col-xl-6">
                <h5 class="fw-bold text-gray-600">BANK Settlement</h5>
                <div class="d-flex mb-2">
                    <div class="mb-0 w-50 me-1">
                        <input class="form-control form-control-solid" placeholder="Pick date rage"
                            id="kt_daterangepicker_2" />
                    </div>
                    <div class="mb-0 w-50 ms-1">
                        <select id="status" data-placeholder="Select a Status..."
                            class="form-select form-select-solid fw-bolder">
                            <option value="">Select a Status...</option>
                            <option value="MATCH">Match</option>
                            <option value="NOT_MATCH">Unmatch</option>
                            
                        </select>
                    </div>
                </div>
                <div class="d-flex mb-2 align-items-center">
                    <div class="mb-0 w-75 me-1">
                        <div class="d-flex align-items-center position-relative w-100">
                            <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                            <span class="svg-icon svg-icon-1 position-absolute ms-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2"
                                        rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                    <path
                                        d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                        fill="black" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                            <input type="text" data-kt-docs-table-filter="searchBo"
                                class="form-control form-control-solid ps-14" placeholder="Search Table" />
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-0 w-250">
                        <button id="clearingSearch" class="btn btn-sm btn-light-warning w-100">Clear
                            Search</button>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                    <h1 class="fw-bold text-dark text-start w-50">Total Data = <?php echo e($countdata); ?> Data
                    </h1>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <?php if($countdata != 0): ?>
                        <button class="btn btn-success rounded-sm" id="autoButton" >
                            Auto
                        </button>
                        <?php endif; ?>
                        <button class="btn btn-primary rounded-sm" id="bulkUnmatch" style="display: none">
                            Bulk Unmatch
                        </button>
                    </div>
                </div>
                <div class="d-flex flex-wrap row">
                    <div class="col-6 bg-success rounded py-1 px-2">
                        <span class="card-body p-0 d-flex justify-content-between flex-column overflow-hidden">
                            <!--begin::Hidden-->
                            <div class="d-flex justify-content-center flex-stack flex-wrap flex-grow-1 px-2 pt-2 pb-3">
                                <div class="me-2">
                                    <span class="fw-bolder text-gray-800 d-block fs-3">Match
                                        
                                        <div class="fw-bolder fs-5 text-light"><?php echo e($match); ?> Data</div>
                                </div>
                            </div>
                            <!--end::Hidden-->
                        </span>
                    </div>
                    <div class="col-6 bg-danger rounded py-1 px-2">
                        <span class="card-body p-0 d-flex justify-content-between flex-column overflow-hidden">
                            <!--begin::Hidden-->
                            <div class="d-flex justify-content-center flex-stack flex-wrap flex-grow-1 px-2 pt-2 pb-3">
                                <div class="me-2">
                                    <span class="fw-bolder text-gray-800 d-block fs-3">Not Match
                                        
                                        <div class="fw-bolder fs-5 text-light"><?php echo e($unmatch); ?> Data
                                        </div>
                                </div>
                            </div>
                            <!--end::Hidden-->
                        </span>
                    </div>
                    
                </div>
            </div>
            <div class="col-xl-12">
                <div class="row">

                    <div class="col-3 bg-secondary border border-gray-300 border-dashed rounded py-1 px-2 mb-3">
                        <span class="card-body p-0 d-flex justify-content-between flex-column overflow-hidden">
                            <!--begin::Hidden-->
                            <div class="d-flex justify-content-center flex-stack flex-wrap flex-grow-1 px-2 pt-2 pb-3">
                                <div class="me-2">
                                    <span class="fw-bolder text-gray-800 d-block fs-3">Transfer Amount</span>
                                    <div class="fw-bolder fs-5 text-primary">Rp. <?php echo e(number_format($totalTransfer)); ?>

                                    </div>
                                </div>
                            </div>
                            <!--end::Hidden-->
                        </span>
                    </div>
                    <div class="col-3 bg-secondary border border-gray-300 border-dashed rounded py-1 px-2 mb-3">
                        <span class="card-body p-0 d-flex justify-content-between flex-column overflow-hidden">
                            <!--begin::Hidden-->
                            <div class="d-flex justify-content-center flex-stack flex-wrap flex-grow-1 px-2 pt-2 pb-3">
                                <div class="me-2">
                                    <span class="fw-bolder text-gray-800 d-block fs-3">Bank Transfer</span>
                                    <div class="fw-bolder fs-5 text-primary">Rp. <?php echo e(number_format($totalBankTransfer)); ?>

                                    </div>
                                </div>
                            </div>
                            <!--end::Hidden-->
                        </span>
                    </div>
                    <div class="col-3 bg-secondary border border-gray-300 border-dashed rounded py-1 px-2 mb-3">
                        <span class="card-body p-0 d-flex justify-content-between flex-column overflow-hidden">
                            <!--begin::Hidden-->
                            <div class="d-flex justify-content-center flex-stack flex-wrap flex-grow-1 px-2 pt-2 pb-3">
                                <div class="me-2">
                                    <span class="fw-bolder text-gray-800 d-block fs-3">Variance</span>
                                    <div class="fw-bolder fs-5 text-primary">Rp. <?php echo e(number_format($variance)); ?>

                                    </div>
                                </div>
                            </div>
                            <!--end::Hidden-->
                        </span>
                    </div>
                    <div class="col-3 bg-secondary border border-gray-300 border-dashed rounded py-1 px-2 mb-3">
                        <span class="card-body p-0 d-flex justify-content-between flex-column overflow-hidden">
                            <!--begin::Hidden-->
                            <div class="d-flex justify-content-center flex-stack flex-wrap flex-grow-1 px-2 pt-2 pb-3">
                                <div class="me-2">
                                    <span class="fw-bolder text-gray-800 d-block fs-3">Sales Amount</span>
                                    <div class="fw-bolder fs-5 text-primary">Rp. <?php echo e(number_format($sales)); ?>

                                    </div>
                                </div>
                            </div>
                            <!--end::Hidden-->
                        </span>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="row gy-3 g-xl-8">
        <!--begin::Col-->
        <div class="col-xl-12">
            <!--begin::Mixed Widget 2-->
            <div class="card card-xl-stretch">
                <!--begin::Header-->
                <div class="card-header border-0 bg-danger py-5">
                    <h3 class="card-title fw-bolder text-white">Bank Settlement</h3>
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body p-0">

                    <div class="card card-flush px-10 py-6 rounded-sm">
                        <!--begin::Datatable-->

                        <table id="bank_statement_detail_table" class="table align-middle table-row-dashed fs-6 gy-5">
                            <thead>
                                <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                    <th><input type="checkbox" class="form-check-input" id="checkAll"></th>
                                    <th>No</th>
                                    <th>MID</th>
                                    <th>Transfer Amount</th>
                                    <th>Sales Amount</th>
                                    <th>Bank Transfer</th>
                                    <th>Bank Movement</th>
                                    <th>Partnert Report</th>
                                    <th>Variance</th>
                                    <th>Status</th>
                                    <th>Status Reconcile</th>
                                    <th>Manual</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                            </tbody>
                        </table>
                        <!--end::Datatable-->
                    </div>
                </div>
                <!--end::Body-->
            </div>
            <!--end::Mixed Widget 2-->
        </div>
        <!--end::Col-->
    </div>
</div>


<?php /**PATH C:\laragon\www\finance-server\resources\views/modules/reconcile/list/tabs/tabdetail.blade.php ENDPATH**/ ?>