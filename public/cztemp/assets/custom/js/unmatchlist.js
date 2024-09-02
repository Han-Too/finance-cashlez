"use strict";
$("#kt_daterangepicker_1").daterangepicker();
$("#kt_daterangepicker_99").daterangepicker();

var token = $('meta[name="csrf-token"]').attr("content");

var KTDatatablesServerSideRes = (function () {
  var dt;
  var uuid = "";
  var url = "";
  var status = "";
  var startDate = "";
  var endDate = "";
  var channel = "";
  const queryParams = new URLSearchParams(window.location.search);
  var regex = /\/reconcile\/unmatch-list\/([^\/]+)/;
  var token = getTokenFromUrl(regex);

  var parUuid = queryParams.get("token");
  var parUstatus = queryParams.get("status");

  if (parUuid) {
    uuid = `token=${parUuid}`;
  }

  if (parUstatus) {
    status = `status=${parUstatus}`;
  }

  // url = `${baseUrl}/reconcilereport/data?${uuid}&${status}`;
  url = `${baseUrl}/reconcile/unmatch-listdata`;

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
      },
      // columns: [
      //   // { data: "settlement_date" },
      //   // { data: "batch_fk" },
      //   { data: "id" },
      //   { data: "processor_payment" },
      //   { data: "id" },
      //   { data: "id" },
      //   { data: "status" },
      //   { data: "bank_transfer" },
      //   { data: "transfer_amount" },
      //   { data: "tax_payment" },
      //   { data: "fee_mdr_merchant" },
      //   { data: "fee_bank_merchant" },
      //   { data: "id" },
      //   { data: "id" },
      //   { data: "id" },
      //   { data: "id" },
      //   { data: "category_report" },
      // ],
      columnDefs: [
        // {
        //   targets: -1,
        //   orderable: false,
        //   className: "text-center",
        //   width: "200px",
        //   render: function (data, type, row) {
        //     return '';
        //   },
        // },
        {
          targets: 0,
          orderable: true,
          className: "text-center",
          width: "50px",
          render: function (data, type, row) {
            if (row.mid) {
              return `
                                  <div class="d-flex justify-content-center mb-1">
                                  ${row.mid}
                                  </div>
                                  <div class="d-flex justify-content-center">
                                      <p class="badge badge-lg 
                                      ${
                                        row.merchant
                                          ? "badge-primary"
                                          : "badge-danger"
                                      }">
                                      ${
                                        row.merchant
                                          ? row.merchant.reference_code
                                          : "NO MRC"
                                      }</p>
                                  </div>
                              `;
            } else {
              return "VLOOKUP";
            }
          },
        },
        {
          targets: 1,
          orderable: true,
          className: "text-center",
          width: "150px",
          render: function (data, type, row) {
            return to_date_time(row.settlement_date);
          },
        },
        {
          targets: 2,
          orderable: true,
          className: "text-center",
          width: "30px",
          render: function (data, type, row) {
            if (row.bank_account) {
              return row.bank_account.bank_code;
            } else {
              return "VLOOKUP";
            }
          },
        },
        {
          targets: 3,
          orderable: true,
          className: "text-center",
          width: "30px",
          render: function (data, type, row) {
            if (row.merchant) {
              return row.merchant.name;
            } else {
              return "VLOOKUP";
            }
          },
        },
        {
          targets: 4,
          orderable: true,
          className: "text-center",
          width: "30px",
          render: function (data, type, row) {
            if (row.bank_account) {
              return row.bank_account.account_number;
            } else {
              return "VLOOKUP";
            }
          },
        },
        {
          targets: 5,
          orderable: true,
          className: "text-center",
          width: "30px",
          render: function (data, type, row) {
            if (row.bank_account) {
              return row.bank_account.account_number.substr(0, 5);
            } else {
              return "VLOOKUP";
            }
          },
        },
        {
          targets: 6,
          orderable: true,
          className: "text-center",
          width: "30px",
          render: function (data, type, row) {
            if (row.bank_account) {
              return row.bank_account.account_number.substr(0, 5) == "88939"
                ? "VIRTUAL ACCOUNT"
                : "REGULER";
            } else {
              return "VLOOKUP";
            }
          },
        },
        {
          targets: 7,
          orderable: true,
          className: "text-center",
          width: "30px",
          render: function (data, type, row) {
            if (row.bank_account) {
              return row.bank_account.account_holder;
            } else {
              return "VLOOKUP";
            }
          },
        },
        {
          targets: 8,
          orderable: true,
          searchable: false,
          className: "text-start",
          width: "150px",
          render: function (data, type, row) {
            return to_rupiah(row.transfer_amount);
          },
        },
        {
          targets: 9,
          orderable: true,
          className: "text-start",
          width: "150px",
          render: function (data, type, row) {
            return to_rupiah(row.total_sales);
          },
        },
        {
          targets: 10,
          orderable: true,
          className: "text-start",
          width: "150px",
          render: function (data, type, row) {
            return to_rupiah(row.bank_transfer);
          },
        },
        {
          targets: 11,
          orderable: true,
          className: "text-start",
          width: "150px",
          render: function (data, type, row) {
            return to_rupiah(row.bank_settlement_amount);
          },
        },
        {
          targets: 12,
          orderable: true,
          className: "text-start",
          width: "150px",
          render: function (data, type, row) {
            return to_rupiah(row.variance);
          },
        },
        {
          targets: 13,
          orderable: true,
          className: "text-center",
          width: "50px",
          render: function (data, type, row) {
            var status = "";
            var badge = "";
            if (row.status == "MATCH") {
              status = "MATCH";
              badge = "badge-light-success";
            } else if (row.status == "deleted") {
              status = "NOT MATCH";
              badge = "badge-light-danger";
            } else {
              status = "on hold";
              badge = "badge-light-warning";
            }
            return `
                              <span class="badge ${badge}">${status.toUpperCase()}</span>
                          `;
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

  // var bank = document.getElementById(`bankInput`).value;
  var bank = "5";
  var status = document.getElementById(`statusInput`).value;
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

  return (window.location.href = `${baseUrl}/reconcile/downloadunmatch`);
});

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

function selectChannel() {}

$(document).ready(function () {
  $("#mrcDetail").on("click", function (event) {
    console.log(event);
  });
});

$(document).ready(function () {
  $("#approveAll").on("click", function (event) {
    Swal.fire({
      title: "Are you sure?",
      text: "Approved data cannot be restored",
      icon: "warning",
      showCancelButton: true,
      // confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: baseUrl + "/reconcilereport/approveall",
          headers: {
            "X-CSRF-TOKEN": token, // Menyertakan token CSRF di header permintaan
          },
          type: "POST",
          beforeSend: function () {
            swal.showLoading();
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

KTUtil.onDOMContentLoaded(function () {
  KTDatatablesServerSideRes.init();
});
