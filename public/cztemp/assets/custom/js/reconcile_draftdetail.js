"use strict";

var startDate = "";
var endDate = "";
var selectedChannel = "";
var selectedStatus = "";
var regex = /\/reconcile-list\/detail\/([^\/]+)/;
const Toast = Swal.mixin({
  toast: true,
  position: "top-end",
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.onmouseenter = Swal.stopTimer;
    toast.onmouseleave = Swal.resumeTimer;
  },
});

var KTDatatablesServerUnmatch = (function () {
  var dt;
  var token = getTokenFromUrl(regex);

  // var filterValue = $("#status").val();

  var url = `${baseUrl}/reconcile-list/detaildata/${token}`;

  var initDatatable = function () {
    dt = $("#bank_statement_detail_table").DataTable({
      searchDelay: 200,
      processing: true,
      serverSide: true,
      order: [[1, "asc"]],
      stateSave: true,
      autoWidth: true,
      select: {
        style: "os",
        selector: "td:first-child",
        className: "row-selected",
      },
      ajax: {
        url,
        data: function (d) {
          d.status = selectedStatus;
          d.startDate = startDate;
          d.endDate = endDate;
        },
      },
      columns: [
        { data: null },
        { data: "id" },
        { data: "mid" },
        { data: "transfer_amount" },
        { data: "total_sales" },
        { data: "bank_transfer" },
        { data: "bank_settlement_amount" },
        { data: "status_parnert" },
        { data: "variance" },
        { data: "status" },
        { data: "status_reconcile" },
        { data: "status_manual" },
        { data: "token_applicant" },
      ],
      columnDefs: [
        {
          targets: 0,
          orderable: false,
          className: "text-start w-10px dt-body-center",
          width: "10px",
          render: function (data, type, row, meta) {
            if (row.status_manual == true) {
              return `<div></div>`;
            } else if (row.status == "NOT_MATCH") {
              return '<input type="checkbox" class="dt-checkboxes form-check-input">';
            } else {
              return `<div></div>`;
            }
          },
        },
        {
          targets: 1,
          orderable: true,
          className: "text-start w-10px",
          width: "10px",
          render: function (data, type, row, meta) {
            return meta.row + 1;
          },
        },
        {
          targets: 2,
          orderable: true,
          className: "text-start w-100px",
          width: "100px",
          render: function (data, type, row, meta) {
            return ` 
                        <div class="d-flex justify-content-center mb-1">
                                <div class="text-bold fs-7">
                                ${data}
                                </div>
                        </div>
                        `;
            // <div class="d-flex justify-content-center">
            //             <a href="#" class="btn btn-sm btn-light-primary me-3 rounded-sm"
            //             data-bs-toggle="modal" data-bs-target="#kt_modal_detail"
            //             onclick="draftDetail('${row.id}')"
            //             >
            //             ${'Detail'}
            //             </a>
            // </div>
          },
        },
        {
          targets: 3,
          orderable: true,
          searchable: false,
          className: "text-start w-50px",
          width: "50px",
          render: function (data, type, row) {
            return ` 
                            <div class="text-bold fs-7 text-uppercase">${
                              !data ? "0" : to_rupiah(parseInt(data))
                            }</div>
                        `;
          },
        },
        {
          targets: 4,
          orderable: true,
          className: "text-start w-200px",
          width: "200px",
          render: function (data, type, row) {
            return ` 
                            <div class="text-bold fs-7 text-uppercase">${
                              !data ? "0" : to_rupiah(parseInt(data))
                            }</div>
                        `;
          },
        },
        {
          targets: 5,
          orderable: true,
          className: "text-start w-200px",
          width: "200px",
          render: function (data, type, row) {
            return ` 
                            <div class="text-bold fs-7 text-uppercase">${
                              !data ? "0" : to_rupiah(parseInt(data))
                            }</div>
                        `;
          },
        },
        {
          targets: 6,
          orderable: true,
          className: "text-start w-100px",
          width: "100px",
          render: function (data, type, row) {
            if (data == "-") {
              return `0`;
            } else {
              return `
                                <div class="text-bold fs-7 text-uppercase">${
                                  !data ? "0" : to_rupiah(parseInt(data))
                                }</div>
                            `;
            }
          },
        },
        {
          targets: 7,
          orderable: true,
          className: "text-start w-100px",
          width: "100px",
          render: function (data, type, row) {
            if (data == 1) {
              return ` 
                                <div class="text-center">
                                    <i class="fa-solid fa-circle-check fa-xl text-primary "></i>
                                </div>
                            `;
            } else {
              return ` 
                                <div class="text-center">
                                    <i class="fa-solid fa-circle-xmark fa-xl text-danger"></i>
                                </div>
                            `;
            }
          },
        },
        {
          targets: 8,
          orderable: true,
          className: "text-start w-100px",
          width: "100px",
          render: function (data, type, row) {
            return ` 
                            <div class="text-bold fs-7 text-uppercase">${
                              !data ? "0" : to_rupiah(parseInt(data))
                            }</div>
                        `;
          },
        },
        {
          targets: 9,
          orderable: true,
          className: "text-start w-100px",
          width: "100px",
          render: function (data, type, row) {
            if (data == "MATCH") {
              return ` 
                                <div class="text-center">
                                    <span class="badge badge-success">Match</span>
                                </div>
                            `;
            } else if (data == "NOT_MATCH") {
              return ` 
                                <div class="text-center">
                                    <span class="badge badge-danger">Not Match</span>
                                </div>
                            `;
            } else {
              return ` 
                                <div class="text-center">
                                    <span class="badge badge-warning">Onhold</span>
                                </div>
                            `;
            }
          },
        },
        {
          targets: 10,
          orderable: true,
          className: "text-start w-100px",
          width: "100px",
          render: function (data, type, row) {
            if (data == "draft") {
              return ` 
                                <div class="text-center">
                                    <span class="badge badge-warning">DRAFT</span>
                                </div>
                            `;
            } else if (data == "unmatch") {
              return ` 
                                <div class="text-center">
                                    <span class="badge badge-primary">UNMATCH</span>
                                </div>
                            `;
            } else {
              return ` 
                                <div class="text-center">
                                    <span class="badge badge-success">APPROVED</span>
                                </div>
                            `;
            }
          },
        },
        {
          targets: 11,
          orderable: true,
          className: "text-start w-100px",
          width: "100px",
          render: function (data, type, row) {
            if (data == true) {
              return ` 
                  <div class="text-center">
                      <i class="fa-solid fa-circle-check fa-xl text-primary "></i>
                  </div>
              `;
            } else {
              return ` 
                  <div class="text-center">
                      <i class="fa-solid fa-circle-xmark fa-xl text-danger"></i>
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
            if (row.status == "NOT_MATCH") {
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
                                      <a href="javascript:void()" data-bs-toggle="modal" data-bs-target="#kt_modal_detail" 
                                      onclick="draftDetail('${row.id}')"
                                      class="menu-link px-3">
                                          View Details
                                      </a>
                                  </div>
                                  <!--end::Menu item-->
  
                                  <!--begin::Menu item-->
                                  <div class="menu-item px-3">
                                  <a href="javascript:void()" onclick="goUnmatch('${row.id}')" class="menu-link px-3" data-kt-docs-table-filter="delete_row">
                                  Go Unmatch
                                  </a>
                                  </div>
                                  <!--end::Menu item-->
                              </div>
                              <!--end::Menu-->
                          `;
            } else if(row.status_reconcile == "reconciled" || row.status_reconcile == "checker"){
              return '';
            } else {
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
                                      <a href="javascript:void()" data-bs-toggle="modal" data-bs-target="#kt_modal_detail" 
                                      onclick="draftDetail('${row.id}')"
                                      class="menu-link px-3">
                                          View Details
                                      </a>
                                  </div>
                                  <!--end::Menu item-->
  
                                  <!--begin::Menu item-->
                                  <div class="menu-item px-3">
                                  <a href="javascript:void()" onclick="goReport('${row.id}')" class="menu-link px-3" data-kt-docs-table-filter="delete_row">
                                  Go Report
                                  </a>
                                  </div>
                                  <!--end::Menu item-->
                              </div>
                              <!--end::Menu-->
                          `;
            }
          },
        },
      ],

      createdRow: function (row, data, dataIndex) {
        $(row).find("td:eq(4)").attr("data-filter", data.status);
      },
    });

    $("#status").on("change", function () {
      // console.log("ganti");
      dt.ajax.reload();
    });

    dt.on("draw", function () {
      KTMenu.createInstances();
    });
  };

  var element = document.getElementById("bulkUnmatch");

  $(document).ready(function () {
    var dt = $("#bank_statement_detail_table").DataTable();

    $("#bank_statement_detail_table tbody").on(
      "change",
      "input.dt-checkboxes",
      function () {
        logSelectedIds();
      }
    );

    $("#checkAll").on("change", function () {
      var rows = dt.rows({ search: "applied" }).nodes();
      $("input.dt-checkboxes", rows).prop("checked", this.checked);
      logSelectedIds();
    });

    function logSelectedIds() {
      var selectedMIDs = [];
      dt.$("input.dt-checkboxes:checked").each(function () {
        var data = dt.row($(this).closest("tr")).data();
        console.log(selectedMIDs); // Debug log to check data
        if (data) {
          selectedMIDs.push(data.id); // Mengakses kolom MID
        }
      });

      if (selectedMIDs.length != 0) {
        element.style.display = "block";
      } else {
        element.style.display = "none";
      }

      // Unbind previous click handler to avoid multiple bindings
      $("#bulkUnmatch").off("click");

      $("#bulkUnmatch").on("click", function () {
        if (selectedMIDs.length === 0) {
          window.location.reload();
          return;
        }

        let promises = selectedMIDs.map(function (id) {
          return new Promise(function (resolve, reject) {
            var token = $('meta[name="csrf-token"]').attr("content");
            $.ajax({
              url: baseUrl + "/reconcile-unmatch/store/" + id,
              headers: {
                "X-CSRF-TOKEN": token, // Menyertakan token CSRF di header permintaan
              },
              type: "POST",
              success: function (response) {
                Toast.fire({
                  icon: "success",
                  title: "Data Have Been Remove",
                });
                resolve(response);
              },
              error: function (xhr, status, error) {
                Swal.fire({
                  text: "error",
                  icon: "error",
                  buttonsStyling: false,
                  confirmButtonText: "Ok, got it!",
                  customClass: {
                    confirmButton: "btn fw-bold btn-primary",
                  },
                });
                reject(error);
              },
            });
          });
        });

        Promise.all(promises)
          .then(function () {
            window.location.reload();
          })
          .catch(function (error) {
            console.error("Error:", error);
          });
      });
    }

    // Initialize logSelectedIds on page load
    logSelectedIds();
  });

  var reloadDatatable = function () {
    dt.ajax.reload();
  };

  var handleSearchDatatable = function () {
    const filterSearch = document.querySelector(
      '[data-kt-docs-table-filter="searchBo"]'
    );
    filterSearch.addEventListener("keyup", function (e) {
      dt.search(e.target.value).draw();
    });
  };

  var handleChannelSelect = function () {
    const channelSelect = document.getElementById("status");
    channelSelect.addEventListener("change", function (e) {
      selectedStatus = e.target.value;
      console.log(selectedStatus);
      dt.ajax.reload();
      // reloadDatatable();
    });
  };

  var initDateRangePicker = function () {
    $("#kt_daterangepicker_2").daterangepicker(
      {
        opens: "left",
        startDate: moment().startOf("month"),
        endDate: moment().endOf("month"),
        locale: {
          format: 'YYYY-MM-DD'
        },
      },
      function (start, end, label) {
        startDate = start.format("YYYY-MM-DD");
        endDate = end.format("YYYY-MM-DD");
        reloadDatatable();
      }
    );
  };

  var clearFilter = function () {
    const clear = document.getElementById("clearingSearch");
    clear.addEventListener("click", function (e) {
      const filterSearch = document.querySelector(
        '[data-kt-docs-table-filter="searchBo"]'
      );
      filterSearch.value = "";
      dt.search("").draw();

      const channelSelect = document.getElementById("status");
      channelSelect.value = "";
      selectedStatus = "";

      $("#kt_daterangepicker_2")
        .data("daterangepicker")
        .setStartDate(moment().startOf("month"));
      $("#kt_daterangepicker_2")
        .data("daterangepicker")
        .setEndDate(moment().endOf("month"));
      startDate = "";
      endDate = "";
      reloadDatatable();
    });
  };

  return {
    init: function () {
      initDatatable();
      handleSearchDatatable();
      handleChannelSelect();
      initDateRangePicker();
      clearFilter();
    },
    reload: function () {
      reloadDatatable();
    },
    setDates: function (start, end) {
      startDate = start;
      endDate = end;
    },
  };
})();

var totalBankSettlement = 0;
var selectedBanks = [];

var totalBankPayment = 0;
var selectedBo = [];

function draftDetail(id) {
  $.ajax({
    url: baseUrl + "/draft/" + id + "/detail",
    type: "GET",
    success: function (response) {
      console.log(response);
      var data = response.data;
      document.getElementById("DetsettlementDate").innerHTML = to_date_time(
        data.settlement_date
      );
      document.getElementById("Detname").innerHTML = data.merchant_name;
      document.getElementById("Detmid").innerHTML = data.mid;
      document.getElementById("Dettrx").innerHTML = data.trx_counts + " Trx";
      document.getElementById("Detsales").innerHTML = to_rupiah(
        data.total_sales
      );
      document.getElementById("Detbanktransfer").innerHTML = to_rupiah(
        data.bank_transfer
      );
      document.getElementById("Detbankmov").innerHTML = to_rupiah(
        data.bank_settlement_amount
      );
      document.getElementById("Detbankvar").innerHTML = to_rupiah(
        data.variance
      );

      if (data.status == "NOT_MATCH") {
        document.getElementById("Detstat").innerHTML =
          "<div class='badge badge-danger'>NOT MATCH</div>";
      } else {
        document.getElementById("Detstat").innerHTML =
          "<div class='badge badge-success'>MATCH</div>";
      }
      if (data.status_reconcile == "draft") {
        document.getElementById("Detdatastat").innerHTML =
          "<div class='badge badge-warning'>DRAFT</div>";
      } else {
        document.getElementById("Detdatastat").innerHTML =
          "<div class='badge badge-success'>MATCH</div>";
      }
    },
    error: function (xhr, status, error) {
      Swal.fire({
        text: "Failed to delete the record.",
        icon: "error",
        buttonsStyling: false,
        confirmButtonText: "Ok, got it!",
        customClass: {
          confirmButton: "btn fw-bold btn-primary",
        },
      });
    },
  });
}
function goUnmatch(id) {
  var token = $('meta[name="csrf-token"]').attr("content");
  $.ajax({
    url: baseUrl + "/reconcile-unmatch/store/" + id,
    headers: {
      "X-CSRF-TOKEN": token, // Menyertakan token CSRF di header permintaan
    },
    type: "POST",
    success: function (response) {
      // console.log(response);
      const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.onmouseenter = Swal.stopTimer;
          toast.onmouseleave = Swal.resumeTimer;
        },
      });
      Toast.fire({
        icon: "success",
        title: "Data Have Been Remove",
      });
    },
    error: function (xhr, status, error) {
      // toast('Data Have Been Remove!','success');
      Swal.fire({
        text: "error",
        icon: "error",
        buttonsStyling: false,
        confirmButtonText: "Ok, got it!",
        customClass: {
          confirmButton: "btn fw-bold btn-primary",
        },
      });
    },
  }).then(function () {
    // window.location.reload();
  });
}
function goReport(id) {
  var token = $('meta[name="csrf-token"]').attr("content");
  $.ajax({
    url: baseUrl + "/reconcilereport/store/" + id,
    headers: {
      "X-CSRF-TOKEN": token, // Menyertakan token CSRF di header permintaan
    },
    type: "POST",
    success: function (response) {
      // console.log(response);
      const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.onmouseenter = Swal.stopTimer;
          toast.onmouseleave = Swal.resumeTimer;
        },
      });
      Toast.fire({
        icon: "success",
        title: "Data Have Been Remove",
      });
    },
    error: function (xhr, status, error) {
      // toast('Data Have Been Remove!','success');
      Swal.fire({
        text: "error",
        icon: "error",
        buttonsStyling: false,
        confirmButtonText: "Ok, got it!",
        customClass: {
          confirmButton: "btn fw-bold btn-primary",
        },
      });
    },
  }).then(function () {
    window.location.reload();
  });
}
// Fungsi autoMove
function autoMove() {
  var tokenA = getTokenFromUrl(regex); // Asumsikan regex sudah didefinisikan di tempat lain
  var token = $('meta[name="csrf-token"]').attr("content");

  $.ajax({
    url: baseUrl + "/reconciledraft/move/" + tokenA,
    headers: {
      "X-CSRF-TOKEN": token, // Menyertakan token CSRF di header permintaan
    },
    type: "POST",
    beforeSend: function () {
      swal.fire({
        html: "<h5>Loading...</h5>",
        showConfirmButton: false,
        onRender: function () {
          // there will only ever be one sweet alert open.
          $(".swal2-content").prepend(sweet_loader);
        },
      });
    },
    success: function (response) {
      swal.hideLoading();
      console.log(response);
      Toast.fire({
        icon: "success",
        title: "Data Have Been Removed",
      });
    },
    error: function (xhr, status, error) {
      console.log(error);
      Swal.fire({
        text: error,
        icon: "error",
        buttonsStyling: false,
        confirmButtonText: "Ok, got it!",
        customClass: {
          confirmButton: "btn fw-bold btn-primary",
        },
      });
    },
  }).then(function () {
    window.location.reload();
  });
}

// Menambahkan event listener pada tombol dengan id="autoMove"
$(document).ready(function () {
  $("#autoButton").on("click", function () {
    Swal.fire({
      title: "Do you want to move data?",
      icon: "info",
      showDenyButton: true,
      // showCancelButton: true,
      confirmButtonText: "Yes",
      denyButtonText: `No`,
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        autoMove();
      } else if (result.isDenied) {
        Swal.fire("Service is Canceled", "", "info");
      }
    });
  });
});
// $("#autoButton").on("click", auto());

$("#tabrefreshButton").on("click", function () {
  var tbodyBank = document.querySelector("#bank_selected_items tbody");
  var tfootBank = document.querySelector("#bank_selected_items tfoot");
  var tbodyBo = document.querySelector("#bo_selected_items tbody");
  var tfootBo = document.querySelector("#bo_selected_items tfoot");

  tbodyBank.innerHTML = "";
  tfootBank.innerHTML = "";
  tbodyBo.innerHTML = "";
  tfootBo.innerHTML = "";

  $('input[type="checkbox"]').prop("checked", false);
  totalBankPayment = 0;
  totalBankSettlement = 0;
});

$("#kt_daterangepicker_3").daterangepicker();
$("#kt_daterangepicker_9").daterangepicker();

KTUtil.onDOMContentLoaded(function () {
  KTDatatablesServerUnmatch.init();
});
