"use strict";
$("#kt_daterangepicker_1").daterangepicker();
$("#kt_daterangepicker_2").daterangepicker();
$("#kt_daterangepicker_3").daterangepicker();

var KTDatatablesServerSide = (function () {
    var dt;

    var initDatatable = function () {
        dt = $("#kt_datatable_example_1").DataTable({
            searchDelay: 200,
            processing: true,
            serverSide: true,
            order: [[1, "desc"]],
            stateSave: true,
            select: {
                style: "os",
                selector: "td:first-child",
                className: "row-selected",
            },
            ajax: {
                url: `${baseUrl}/settlement/data`,
            },
            columns: [
                { data: "id" },
                { data: "created_at" },
                // { data: "start_date" },
                { data: "name" },
                { data: "url" },
                // { data: "debit_total" },
                { data: "debit_sum" },
                // { data: "credit_total" },
                { data: "credit_sum" },
                { data: "is_reconcile" },
                { data: "is_parnert" },
                { data: "token_applicant" },
            ],
            columnDefs: [
                {
                    targets: 0,
                    orderable: true,
                    className: "text-start",
                    width: "50px",
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    },
                },
                {
                    targets: 1,
                    orderable: true,
                    className: "text-start",
                    width: "120px",
                    render: function (data, type, row) {
                        return ` 
                            <div class="text-bold fs-7 text-uppercase">${to_date(data)}</div>
                            <div class="text-bold fs-7 ">Upload By : ${row.created_by}</div>
                        `;
                    },
                },
                {
                    targets: 2,
                    orderable: true,
                    className: "text-center",
                    width: "100px",
                    render: function (data, type, row) {
                        return `
                        <div class="text-center">
                            <div class="text-bold fs-7">${data}</div>
                        </div>
                        `;
                    },
                },
                {
                    targets: 3,
                    orderable: true,
                    className: "text-center",
                    width: "100px",
                    render: function (data, type, row) {
                        return `<a href='${data}' class="text-bold fs-7">Dowload File</a>`;
                    },
                },
                {
                    targets: 4,
                    orderable: true,
                    className: "text-start",
                    width: "150px",
                    render: function (data, type, row) {
                        return ` 
                            <div class="text-center">
                            <div class="text-bold fs-7">${to_rupiah(Math.round(data))}</div>
                            <div class="text-bold fs-7">${row.debit_total} Trx</div>
                            </div>
                        `;
                    },
                },
                {
                    targets: 5,
                    orderable: true,
                    className: "text-start",
                    width: "150px",
                    render: function (data, type, row) {
                        return ` 
                        <div class="text-center">
                            <div class="text-bold fs-7">${to_rupiah(Math.round(data))}</div>
                            <div class="text-bold fs-7">${row.credit_total} Trx</div>
                        </div>
                        `;
                    },
                },
                {
                    targets: 6,
                    orderable: true,
                    className: "text-start",
                    width: "150px",
                    render: function (data, type, row) {
                        if(data == 1){
                            return ` 
                                <div class="text-center">
                                    <span class="badge badge-success">Success</span>
                                </div>
                            `;
                        } else if(data == 3){
                            return ` 
                                <div class="text-center">
                                    <span class="badge badge-warning">Draft</span>
                                </div>
                            `;
                        } else {
                            return ` 
                                <div class="text-center">
                                    <span class="badge badge-danger">Not Reconciled</span>
                                </div>
                            `;
                        }
                    },
                },
                {
                    targets: 7,
                    orderable: true,
                    className: "text-start",
                    width: "150px",
                    render: function (data, type, row) {
                        if(data == 1){
                            return ` 
                                <div class="text-center">
                                    <span class="badge badge-success">Have</span>
                                </div>
                            `;
                        } else {
                            return ` 
                                <div class="text-center">
                                    <span class="badge badge-danger">Not Have</span>
                                </div>
                            `;
                        }
                    },
                },
                {
                    targets: -1,
                    orderable: false,
                    className: "text-center",
                    width: "200px",
                    render: function (data, type, row) {
                        return `
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                                Actions
                                <span class="svg-icon svg-icon-5 m-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                            <path d="M6.70710678,15.7071068 C6.31658249,16.0976311 5.68341751,16.0976311 5.29289322,15.7071068 C4.90236893,15.3165825 4.90236893,14.6834175 5.29289322,14.2928932 L11.2928932,8.29289322 C11.6714722,7.91431428 12.2810586,7.90106866 12.6757246,8.26284586 L18.6757246,13.7628459 C19.0828436,14.1360383 19.1103465,14.7686056 18.7371541,15.1757246 C18.3639617,15.5828436 17.7313944,15.6103465 17.3242754,15.2371541 L12.0300757,10.3841378 L6.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000003, 11.999999) rotate(-180.000000) translate(-12.000003, -11.999999)"></path>
                                        </g>
                                    </svg>
                                </span>
                            </a>
                            <!--begin::Menu-->
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <a href="${baseUrl}/settlement/detail/${data}" class="menu-link px-3" data-kt-docs-table-filter="edit_row">
                                        View Details
                                    </a>
                                </div>
                                <!--end::Menu item-->

                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <a href="javascript:void()" onclick="deleteRow('${data}')" class="menu-link px-3" data-kt-docs-table-filter="delete_row">
                                        Delete
                                    </a>
                                </div>
                                <!--end::Menu item-->
                            </div>
                            <!--end::Menu-->
                        `;
                    },
                },
            ],

            createdRow: function (row, data, dataIndex) {
                $(row)
                    .find("td:eq(4)")
                    .attr("data-filter", data.name);
            },
        })

        dt.on("draw", function () {
            KTMenu.createInstances();
        });
    };

    var handleSearchDatatable = function () {
        const filterSearch = document.querySelector(
            '[data-kt-docs-table-filter="search"]'
        );
        filterSearch.addEventListener("keyup", function (e) {
            dt.search(e.target.value).draw();
        });
    };

    return {
        init: function () {
            initDatatable();
            handleSearchDatatable();
        },
    };
})();

function deleteRow($token) {
    if (!$token) {
        console.error("Token is empty.");
        return;
    }
    Swal.fire({
        text: "Are you sure you want to delete this record?",
        icon: "warning",
        showCancelButton: true,
        buttonsStyling: false,
        confirmButtonText: "Yes, delete!",
        cancelButtonText: "No, cancel",
        customClass: {
            confirmButton: "btn fw-bold btn-danger",
            cancelButton: "btn fw-bold btn-active-light-primary",
        },
    }).then(function (result) {
        if (result.value) {
            $.ajax({
                url: baseUrl + "/settlement/destroy/" + $token,
                type: "GET",
                success: function (response) {
                    Swal.fire({
                        text: "You have deleted the record!",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        },
                    }).then(function () {
                        window.location.reload();
                    });
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        // text: "Failed to delete the record.",
                        text: error,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        },
                    });
                },
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire({
                text: "Record was not deleted.",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn fw-bold btn-primary",
                },
            });
        }
    });
}

function reconcile(token) {
    Swal.fire({
        text: "Are you sure you want to reconcile this data?",
        icon: "warning",
        showCancelButton: true,
        buttonsStyling: false,
        confirmButtonText: "Yes, reconcile!",
        cancelButtonText: "No, cancel",
        customClass: {
            confirmButton: "btn fw-bold btn-primary rounded-sm",
            cancelButton: "btn fw-bold btn-active-light-primary rounded-sm",
        },
    }).then(function (result) {
        swal.showLoading();
        if (result.value) {
            $.ajax({
                url: baseUrl + "/reconcile/" + token + '/proceed',
                type: "GET",
                beforeSend: function() {
                    swal.showLoading();
                },
                success: function (response) {
                    swal.hideLoading();
                    Swal.fire({
                        text: "You have reconcile the data!",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary rounded-sm",
                        },
                    }).then(function () {
                        window.location.reload();
                    });
                },
                error: function (xhr, status, error) {
                    swal.hideLoading();
                    Swal.fire({
                        text: "Failed to reconcile the record.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary rounded-sm",
                        },
                    });
                },
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire({
                text: "Reconcile is canceled.",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn fw-bold btn-primary rounded-sm",
                },
            });
        }
    });
}

var uploadedFile = null;
var fileUrl = null;

var uploadedFilePartner = null;
var fileUrlPartner = null;

var myDropzone = new Dropzone("#kt_dropzonejs_example_1", {
    url: baseUrl + "/api/file/check",
    paramName: "file",
    maxFiles: 1,
    maxFilesize: 10,
    addRemoveLinks: true,
    accept: function(file, done) {
        uploadedFile = file; // Store the uploaded file
        done();
    },
    success: function(file, response) {
        if (response.success) {
            fileUrl = response.data
            Swal.fire({
                title: "Success",
                text: "Success Upload File!",
                icon: "success"
              });
              console.log("File berhasil diunggah:", file);
            } else {
            Swal.fire({
                title: "Error",
                text: response.message,
                icon: "error"
              });
            console.error("Gagal mengunggah file:", response.message);
        }
    }
});

var myDropzone = new Dropzone("#kt_dropzonejs_example_2", {
    url: baseUrl + "/api/file/check",
    paramName: "file",
    maxFiles: 1,
    maxFilesize: 10,
    addRemoveLinks: true,
    accept: function(file, done) {
        uploadedFilePartner = file; // Store the uploaded file
        done();
    },
    success: function(file, response) {
        if (response.success) {
            fileUrl = response.data
            Swal.fire({
                title: "Success",
                text: "Success Upload File!",
                icon: "success"
              });
              console.log("File berhasil diunggah:", file);
            } else {
            Swal.fire({
                title: "Error",
                text: response.message,
                icon: "error"
              });
            console.error("Gagal mengunggah file:", response.message);
        }
    }
});


function addPartnerReport() {
    var checkbox = document.getElementById(`partnerReport`);
    var dropzonePartner = document.getElementById(`dropzonePartnerReport`);
    if (checkbox.checked) {
        dropzonePartner.style.display = 'block';
    } else {
        dropzonePartner.style.display = 'none';
        uploadedFilePartner = null;
        fileUrlPartner = null;
    }
}

$("#store_settlement_form").on("submit", function(event) {
    event.preventDefault();
    var token = $('meta[name="csrf-token"]').attr('content');
    var formData = new FormData(this);
    formData.append('file', uploadedFile);  
    formData.append('url', fileUrl);  
    formData.append('filePartner', uploadedFilePartner);  
    formData.append('urlPartner', fileUrlPartner);  
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': token
        },
        type: 'POST',
        data: formData,
        url: `${baseUrl}/settlement`,
        dataType: 'JSON',
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function() {
            swal.showLoading();
        },
        success: function(data) {
            if (data.status === true) {
                swal.hideLoading();
                swal.fire({
                    text: data.message,
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn font-weight-bold btn-light-primary"
                    }
                }).then(function() {
                    location.href = baseUrl + "/settlement";
                });
            } else {
                var values = '';
                jQuery.each(data.message, function(key, value) {
                    values += value + "<br>";
                });

                swal.fire({
                    text: data.message,
                    html: values,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn font-weight-bold btn-light-primary"
                    }
                }).then(function() {});
            }
        }
    });
}); 

$("#store_reconcile_form").on("submit", function(event) {
    event.preventDefault();
    var token = $('meta[name="csrf-token"]').attr('content');
    var formData = new FormData(this);
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': token
        },
        type: 'POST',
        data: formData,
        url: `${baseUrl}/reconcile`,
        dataType: 'JSON',
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function() {
            swal.showLoading();
        },
        success: function(data) {
            if (data.status === true) {
                swal.hideLoading();
                swal.fire({
                    text: data.message,
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn font-weight-bold btn-light-primary"
                    }
                }).then(function() {
                    location.href = baseUrl + "/reconcile/result";
                });
            } else {
                var values = '';
                jQuery.each(data.message, function(key, value) {
                    values += value + "<br>";
                });

                swal.fire({
                    text: data.message,
                    html: values,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn font-weight-bold btn-light-primary"
                    }
                }).then(function() {});
            }
        }
    });
}); 

KTUtil.onDOMContentLoaded(function () {
    KTDatatablesServerSide.init();
});