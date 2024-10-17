"use strict";
$("#kt_daterangepicker_1").daterangepicker();
$("#kt_daterangepicker_99").daterangepicker();

var token = $('meta[name="csrf-token"]').attr("content");
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

var KTDatatablesServerSideRes = (function () {
  var dt;
  var uuid = "";
  var url = "";
  var status = "";
  var startDate = "";
  var endDate = "";
  var channel = "";
  const queryParams = new URLSearchParams(window.location.search);

  var parUuid = queryParams.get("token");
  var parUstatus = queryParams.get("status");

  if (parUuid) {
    uuid = `token=${parUuid}`;
  }

  if (parUstatus) {
    status = `status=${parUstatus}`;
  }

  url = `${baseUrl}/reconcile/data/result`;

  var initDatatable = function () {
    dt = $("#kt_datatable_example_99").DataTable({
      searchDelay: 200,
      processing: true,
      serverSide: true,
      order: [[1, "desc"]],
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
          d.startDate = startDate;
          d.endDate = endDate;
          d.channel = channel;
        },
        dataSrc: function (json) {
          // console.log(json);

          if (
            typeof json.recordsTotal === "undefined" ||
            typeof json.recordsFiltered === "undefined"
          ) {
            console.error(
              "Missing recordsTotal or recordsFiltered in response"
            );
            return []; // Kembalikan array kosong jika properti tidak ada
          }

          // Jika data lain seperti resmatch atau resdispute perlu ditampilkan
          document.getElementById("resmatch").innerText =
            (json.resmatch || 0) + " Trx";
          document.getElementById("ressumMatch").innerText =
            to_rupiah(json.ressumMatch) || 0;
          document.getElementById("resdispute").innerText =
            (json.resdispute || 0) + " Trx";
          document.getElementById("ressumDispute").innerText =
            to_rupiah(json.ressumDispute) || 0;

          // Ensure the data is returned for DataTables
          // console.log(json.data);
          return json.data || [];
        },
      },
      columnDefs: [
        {
          targets: -1,
          orderable: false,
          className: "text-center",
          width: "200px",
          render: function (data, type, row) {
            // console.log(row);
            if (authUserCanApprove == false && authUserCanCancel == false) {
              return "";
            } else if (row.status_reconcile == "approved") {
              return `
              <div class="badge badge-success">
                APPROVED
              </div>
              `;
            } else if (row.category_report == "manual") {
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
                          ${
                            authUserCanCancel
                              ? `
                          <div class="menu-item px-3">
      <a href="javascript:void()" 
      onclick="goManual('${row.id}')"
      class="menu-link px-3">
          Manual
      </a>
                          </div>`
                              : ""
                          }
                          <!--end::Menu item-->
            
                          <!--begin::Menu item-->
                          ${
                            authUserCanApprove
                              ? `
                          <div class="menu-item px-3">
                          <a href="javascript:void()" onclick="approveReport(${row.id})" class="menu-link px-3" data-kt-docs-table-filter="delete_row">
                          Approved
                          </a>
                          </div>`
                              : ""
                          }
                          <!--end::Menu item-->
                      </div>
                      <!--end::Menu-->
                                    `;
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
                          ${
                            authUserCanCancel
                              ? `
                          <div class="menu-item px-3">
      <a href="javascript:void()"
      onclick="goDraft('${row.id}')"
      class="menu-link px-3">
          Cancel
      </a>
                          </div>`
                              : ""
                          }
                          <!--end::Menu item-->
            
                          <!--begin::Menu item-->
                          ${
                            authUserCanApprove
                              ? `
                          <div class="menu-item px-3">
                          <a href="javascript:void()" onclick="approveReport(${row.id})" class="menu-link px-3" data-kt-docs-table-filter="delete_row">
                          Approved
                          </a>
                          </div>`
                              : ""
                          }
                          <!--end::Menu item-->
                      </div>
                      <!--end::Menu-->
                                    `;
            }
          },
        },
        {
          targets: 0,
          orderable: false,
          className: "text-start w-10px dt-body-center",
          width: "10px",
          render: function (data, type, row, meta) {
            if (row.status_reconcile == "approved") {
              return `<div></div>`;
            } else if (row.status_reconcile == "pending") {
              return '<input type="checkbox" class="dt-checkboxes form-check-input">';
            } else {
              return `<div></div>`;
            }
          },
        },
        {
          targets: 1,
          orderable: false,
          className: "text-center",
          width: "50px",
          render: function (data, type, row) {
            // console.log(row);
            return row.mid
              ? `
                      <div class="d-flex justify-content-center mb-1">
                          ${row.mid}
                      </div>
                      <div class="d-flex justify-content-center">
                          <p class="badge badge-lg ${
                            row.merchant ? "badge-primary" : "badge-danger"
                          }">
                              ${
                                row.merchant
                                  ? row.merchant.reference_code
                                  : "NO MRC"
                              }
                          </p>
                      </div>
                  `
              : "VLOOKUP";
          },
        },
        {
          targets: 2,
          orderable: true,
          className: "text-center",
          render: function (data, type, row) {
            return row.bank_account ? row.bank_account.bank_code : "VLOOKUP";
          },
        },
        {
          targets: 3,
          orderable: true,
          className: "text-center",
          render: function (data, type, row) {
            return row.merchant ? row.merchant.name : "VLOOKUP";
          },
        },
        {
          targets: 4,
          orderable: true,
          className: "text-center",
          render: function (data, type, row) {
            return row.bank_account
              ? row.bank_account.account_number
              : "VLOOKUP";
          },
        },
        {
          targets: 5,
          orderable: true,
          className: "text-center",
          render: function (data, type, row) {
            return row.bank_account
              ? row.bank_account.account_number.substr(0, 5)
              : "VLOOKUP";
          },
        },
        {
          targets: 6,
          orderable: true,
          className: "text-center",
          render: function (data, type, row) {
            return row.bank_account &&
              row.bank_account.account_number.substr(0, 5) == "88939"
              ? "VIRTUAL ACCOUNT"
              : "REGULER";
          },
        },
        {
          targets: 7,
          orderable: true,
          className: "text-center",
          render: function (data, type, row) {
            return row.bank_account
              ? row.bank_account.account_holder
              : "VLOOKUP";
          },
        },
        {
          targets: 8,
          orderable: true,
          searchable: false,
          className: "text-start",
          render: function (data, type, row) {
            return to_rupiah(row.transfer_amount);
          },
        },
        {
          targets: 9,
          orderable: true,
          className: "text-start",
          render: function (data, type, row) {
            return to_rupiah(row.total_sales);
          },
        },
        {
          targets: 10,
          orderable: true,
          className: "text-start",
          render: function (data, type, row) {
            return to_rupiah(row.bank_transfer);
          },
        },
        {
          targets: 11,
          orderable: true,
          className: "text-start",
          render: function (data, type, row) {
            return to_rupiah(row.bank_settlement_amount);
          },
        },
        {
          targets: 12,
          orderable: true,
          className: "text-start",
          render: function (data, type, row) {
            return to_rupiah(row.variance);
          },
        },
        {
          targets: 13,
          orderable: true,
          className: "text-center",
          render: function (data, type, row) {
            let badge =
              row.status == "MATCH"
                ? "badge-light-success"
                : row.status == "NOT_MATCH" || data == "NOT_FOUND"
                ? "badge-light-danger"
                : "badge-light-warning";
            return `<span class="badge ${badge}">${row.status.toUpperCase()}</span>`;
          },
        },
        {
          targets: 14,
          orderable: true,
          className: "text-center",
          render: function (data, type, row) {
            return `<div class="badge badge-${
              row.category_report == "system" ? "primary" : "warning"
            }">
                              ${row.category_report.toUpperCase()}
                          </div>`;
          },
        },
        {
          targets: 15,
          orderable: true,
          className: "text-center",
          render: function (data, type, row) {
            return to_date(row.created_at);
          },
        },
      ],
      createdRow: function (row, data, dataIndex) {
        $(row).find("td:eq(4)").attr("data-filter", data.name);
      },
    });

    dt.on("draw", function () {
      KTMenu.createInstances();
    });
  };

  var reloadDatatable = function () {
    dt.ajax.reload();
  };

  var handleSearchDatatable = function () {
    const filterSearch = document.querySelector(
      '[data-kt-docs-table-filter="search"]'
    );
    filterSearch.addEventListener("keyup", function (e) {
      dt.search(e.target.value).draw();
    });
  };

  var initDateRangePicker = function () {
    $("#kt_daterangepicker_1").daterangepicker(
      {
        opens: "left",
        // startDate: moment().startOf("month"),
        startDate: moment(),
        // endDate: moment().endOf("month"),
        endDate: moment(),
        locale: {
          format: "YYYY-MM-DD",
        },
      },
      function (start, end, label) {
        startDate = start.format("YYYY-MM-DD");
        endDate = end.format("YYYY-MM-DD");
        reloadDatatable();
      }
    );
  };

  var handleChannelSearch = function () {
    const selectChannel = document.getElementById("channelId");
    selectChannel.addEventListener("change", function (e) {
      channel = e.target.value;
      reloadDatatable();
    });
  };

  var handleRefreshTable = function () {
    const refreshButton = document.getElementById("resrefreshButton");
    const searchTable = document.getElementById("searchTable");
    const selectChannel = document.getElementById("channelId");

    refreshButton.addEventListener("click", function (e) {
      startDate = null;
      endDate = null;
      channel = "";
      searchTable.value = "";
      selectChannel.value = "";
      dt.search("").draw();
      reloadDatatable();
    });
  };

  var element = document.getElementById("bulking");
  var element2 = document.getElementById("canceling");
  $(document).ready(function () {
    var dt = $("#kt_datatable_example_99").DataTable();

    $("#kt_datatable_example_99 tbody").on(
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
        // console.log(selectedMIDs); // Debug log to check data
        if (data) {
          selectedMIDs.push(data.id); // Mengakses kolom MID
        }
      });

      if (selectedMIDs.length != 0) {
        element.style.display = "block";
        element2.style.display = "block";
      } else {
        element.style.display = "none";
        element2.style.display = "none";
      }

      // Unbind previous click handler to avoid multiple bindings
      $("#bulking").off("click");

      $("#bulking").on("click", function () {
        if (selectedMIDs.length === 0) {
          window.location.reload();
          return;
        }

        let promises = selectedMIDs.map(function (id) {
          return new Promise(function (resolve, reject) {
            var token = $('meta[name="csrf-token"]').attr("content");
            $.ajax({
              url: baseUrl + "/reconcilereport/approve/" + id,
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

      $("#canceling").off("click");
      $("#canceling").on("click", function () {
        if (selectedMIDs.length === 0) {
          window.location.reload();
          return;
        }

        let promises = selectedMIDs.map(function (id) {
          return new Promise(function (resolve, reject) {
            var token = $('meta[name="csrf-token"]').attr("content");
            $.ajax({
              url: baseUrl + "/reconcilereport/reporttodraft/" + id,
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

  return {
    init: function () {
      initDatatable();
      handleSearchDatatable();
      reloadDatatable();
      initDateRangePicker();
      handleRefreshTable();
      handleChannelSearch();
    },
  };
})();

$("#download_reconcile_form").on("submit", function (event) {
  event.preventDefault();

  var dateRange = document.getElementById(`kt_daterangepicker_99`).value;

  var dates = dateRange.split(" - ");

  var startDateString = dates[0];
  var startDateParts = startDateString.split("/");
  var formattedStartDate =
    startDateParts[2] +
    "-" +
    startDateParts[0].padStart(2, "0") +
    "-" +
    startDateParts[1].padStart(2, "0");

  // Parsing tanggal akhir
  var endDateString = dates[1];
  var endDateParts = endDateString.split("/");
  var formattedEndDate =
    endDateParts[2] +
    "-" +
    endDateParts[0].padStart(2, "0") +
    "-" +
    endDateParts[1].padStart(2, "0");

  return (window.location.href = `${baseUrl}/reconcile/downloaddisburst?&startDate=${formattedStartDate}&endDate=${formattedEndDate}`);
});

function getHead() {
  $.ajax({
    url: baseUrl + "/reconcile/headerapproveddata",
    type: "GET",
    success: function (response) {
      var data = response;
      document.getElementById("resmatch").innerText = data.resmatch + " Trx";
      document.getElementById("ressumMatch").innerText = to_rupiah(
        data.ressumMatch
      );
      document.getElementById("resdispute").innerText =
        data.resdispute + " Trx";
      document.getElementById("ressumDispute").innerText = to_rupiah(
        data.ressumDispute
      );
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

function goDraft(id) {
  $.ajax({
    url: baseUrl + "/reconcilereport/draft/" + id,
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
    // reloadDatatable();
    window.location.reload();
  });
}
function goReport(id) {
  $.ajax({
    url: baseUrl + "/reconcilereport/report/" + id,
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
    // reloadDatatable();
    window.location.reload();
  });
}

function approveReport(id) {
  $.ajax({
    url: baseUrl + "/reconcilereport/approve/" + id,
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
        title: "Data Have Been Approved",
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
    // reloadDatatable();
    // dt.ajax.reload();
  });
}

function goManual(id) {
  $.ajax({
    url: baseUrl + "/reconcilereport/manual/" + id,
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
    // reloadDatatable();
    window.location.reload();
  });
}

function mrcDetail(tokenApplicant, id) {
  $.ajax({
    url: baseUrl + "/reportmrc/" + tokenApplicant + "/detail",
    type: "GET",
    success: function (response) {
      // console.log(response);
      var data = response.data;
      document.getElementById("settlementDate" + id).innerHTML = to_date_time(
        data.settlement_date
      );
      document.getElementById("batch" + id).innerHTML = data.batch_fk;
      document.getElementById("bankType" + id).innerHTML = "-";
      document.getElementById("mrc" + id).innerHTML =
        data.merchant.reference_code;
      document.getElementById("merchantName" + id).innerHTML =
        data.merchant.name;
      document.getElementById("grossTrf" + id).innerHTML = "-";
      document.getElementById("bankAdmin" + id).innerHTML = "-";
      document.getElementById("netTransfer" + id).innerHTML = `${to_rupiah(
        data.transfer_amount
      )} `;
      document.getElementById("accountNumber" + id).innerHTML =
        data.bank_account.account_number;
      document.getElementById("bankCode" + id).innerHTML =
        data.bank_account.bank_code;
      document.getElementById("bankName" + id).innerHTML =
        data.bank_account.bank_name;
      document.getElementById("accounttHolder" + id).innerHTML =
        data.bank_account.account_holder;
      document.getElementById("accountEmail" + id).innerHTML =
        data.merchant.email;
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

$(document).ready(function () {
  $("#approveAll").on("click", function (event) {
    event.preventDefault();

    var datePick = document.getElementById(`kt_daterangepicker_1`).value;

    var dates = datePick.split(" - ");

    var startDateString = dates[0];
    var startDateParts = startDateString.split("-");
    var formattedStartDate =
      startDateParts[0].padStart(2, "0") +
      "-" +
      startDateParts[1].padStart(2, "0") +
      "-" +
      startDateParts[2];

    // Parsing tanggal akhir
    var endDateString = dates[1];
    var endDateParts = endDateString.split("-");
    var formattedEndDate =
      endDateParts[0].padStart(2, "0") +
      "-" +
      endDateParts[1].padStart(2, "0") +
      "-" +
      endDateParts[2];
    // console.log(dates);
    // console.log(formattedStartDate);
    // console.log(formattedEndDate);

    Swal.fire({
      title: "Are you sure?",
      text: "Approve All Data?",
      icon: "warning",
      showCancelButton: true,
      // confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url:
            baseUrl +
            `/reconcilereport/approveall?&startDate=${formattedStartDate}&endDate=${formattedEndDate}`,
          // `/reconcilereport/approveall`,
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
            Toast.fire({
              icon: "success",
              title: "Data Have Been Approve",
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
          // reloadDatatable();
          window.location.reload();
        });
      } else {
        Swal.fire({
          title: "Process Cancelled",
          text: "The Process Has Been Canceled",
          icon: "error",
        });
      }
    });
  });
});

function selectChannel() {}

$(document).ready(function () {
  $("#mrcDetail").on("click", function (event) {
    console.log(event);
  });
});

KTUtil.onDOMContentLoaded(function () {
  KTDatatablesServerSideRes.init();
});
