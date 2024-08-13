<div class="modal fade" id="kt_modal_detail" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px rounded-sm">
        <!--begin::Modal content-->
        <div class="modal-content rounded-sm">
            <!--begin::Modal header-->
            <div class="modal-header pb-0 border-0 justify-content-end">
                <!--begin::Close-->
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                transform="rotate(-45 6 17.3137)" fill="black" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                transform="rotate(45 7.41422 6)" fill="black" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                </div>
                <!--end::Close-->
            </div>
            <!--begin::Modal header-->
            <!--begin::Modal body-->
            <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                <!--begin::Heading-->
                <div class="mb-5 text-center">
                    <!--begin::Title-->
                    <h1 class="mb-1">Detail Settlement</h1>
                    <!--end::Title-->
                </div>
                <!--end::Heading-->
                <div class="separator my-3"></div>

                <!--begin::Input group-->
                <div class="d-flex flex-column mb-2 fv-row">
                    <!--begin::Label-->
                    <label class="d-flex align-items-center mb-2 fs-4 ">
                        <span class="w-150px text-start mx-4 fw-bold">Settlement Date</span>
                        <span class="w-10px text-start mx-4">:</span>
                        <span class="text-start" id="DetsettlementDate"></span>
                    </label>
                </div>
                <!--end::Input group-->

                <!--begin::Input group-->
                <div class="d-flex flex-column mb-2 fv-row">
                    <!--begin::Label-->
                    <label class="d-flex align-items-center mb-2 fs-4 ">
                        <span class="w-150px text-start mx-4 fw-bold">Merchant Name</span>
                        <span class="w-10px text-start mx-4">:</span>
                        <span class="text-start" id="Detname"></span>
                    </label>
                </div>
                <!--end::Input group-->
                <!--begin::Input group-->
                <div class="d-flex flex-column mb-2 fv-row">
                    <!--begin::Label-->
                    <label class="d-flex align-items-center mb-2 fs-4 ">
                        <span class="w-150px text-start mx-4 fw-bold">MID</span>
                        <span class="w-10px text-start mx-4">:</span>
                        <span class="text-start" id="Detmid"></span>
                    </label>
                </div>
                <!--end::Input group-->
                <!--begin::Input group-->
                <div class="d-flex flex-column mb-2 fv-row">
                    <!--begin::Label-->
                    <label class="d-flex align-items-center mb-2 fs-4 ">
                        <span class="w-150px text-start mx-4 fw-bold">Total Transaction</span>
                        <span class="w-10px text-start mx-4">:</span>
                        <span class="text-start" id="Dettrx"></span>
                    </label>
                </div>
                <!--end::Input group-->
                <!--begin::Input group-->
                <div class="d-flex flex-column mb-2 fv-row">
                    <!--begin::Label-->
                    <label class="d-flex align-items-center mb-2 fs-4 ">
                        <span class="w-150px text-start mx-4 fw-bold">Sales Amount</span>
                        <span class="w-10px text-start mx-4">:</span>
                        <span class="text-start" id="Detsales"></span>
                    </label>
                </div>
                <!--end::Input group-->
                <!--begin::Input group-->
                <div class="d-flex flex-column mb-2 fv-row">
                    <!--begin::Label-->
                    <label class="d-flex align-items-center mb-2 fs-4 ">
                        <span class="w-150px text-start mx-4 fw-bold">Bank Transfer</span>
                        <span class="w-10px text-start mx-4">:</span>
                        <span class="text-start" id="Detbanktransfer"></span>
                    </label>
                </div>
                <!--end::Input group-->
                <!--begin::Input group-->
                <div class="d-flex flex-column mb-2 fv-row">
                    <!--begin::Label-->
                    <label class="d-flex align-items-center mb-2 fs-4 ">
                        <span class="w-150px text-start mx-4 fw-bold">Bank Movement</span>
                        <span class="w-10px text-start mx-4">:</span>
                        <span class="text-start" id="Detbankmov"></span>
                    </label>
                </div>
                <!--end::Input group-->
                <!--begin::Input group-->
                <div class="d-flex flex-column mb-2 fv-row">
                    <!--begin::Label-->
                    <label class="d-flex align-items-center mb-2 fs-4 ">
                        <span class="w-150px text-start mx-4 fw-bold">Variance</span>
                        <span class="w-10px text-start mx-4">:</span>
                        <span class="text-start" id="Detbankvar"></span>
                    </label>
                </div>
                <!--end::Input group-->
                <div class="d-flex flex-column mb-2 fv-row">
                    <!--begin::Label-->
                    <label class="d-flex align-items-center mb-2 fs-4 ">
                        <span class="w-150px text-start mx-4 fw-bold">Reconcile Status</span>
                        <span class="w-10px text-start mx-4">:</span>
                        <span class="text-start" id="Detstat"></span>
                    </label>
                </div>
                <!--end::Input group-->
                <!--end::Input group-->
                <div class="d-flex flex-column mb-2 fv-row">
                    <!--begin::Label-->
                    <label class="d-flex align-items-center mb-2 fs-4 ">
                        <span class="w-150px text-start mx-4 fw-bold">Data Status</span>
                        <span class="w-10px text-start mx-4">:</span>
                        <span class="text-start" id="Detdatastat"></span>
                    </label>
                </div>
                <!--end::Input group-->

            </div>
            <!--end::Modal body-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>
