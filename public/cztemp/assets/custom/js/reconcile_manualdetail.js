"use strict";
var regex = /\/reconcile-list\/detail\/([^\/]+)/;
var tokenA = getTokenFromUrl(regex);

var KTDatatablesServerSide = (function () {
  var dt;
  var startDate = "";
  var endDate = "";
  var selectedBank = "";

  var url = `${baseUrl}/reconcile-unmatch/data`;

  var initDatatable = function () {
    dt = $("#bank_settlement_table").DataTable({
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
          d.bank = selectedBank;
        },
      },
      columns: [
        { data: "settlement_date" },
        { data: "merchant_name" },
        // { data: "description2" },
        { data: "mid" },
        // { data: "bank_transfer" },
        { data: "internal_payment" },
        { data: "id" },
      ],
      columnDefs: [
        // {
        //   targets: -1,
        //   orderable: true,
        //   className: "text-end",
        //   width: "150px",
        //   render: function (data, type, row, meta) {
        //     // return meta.row + 1;
        //     // console.log(row);
        //     // '${row.header.processor}',
        //     return `<div class="form-check form-check-sm form-check-custom form-check-solid text-end" data-bs-toggle="tooltip" data-bs-placement="top" title="Tooltip on top">
        //                             <input onclick="checkBank(
        //                                 ${row.id},
        //                                 '${to_date(row.settlement_date)}',
        //                                 '${row.mid}', '${row.bank_transfer}'
        //                             )" id="checkbox_bank_${row.id}"
        //                             class="form-check-input boCheckbox" name="bo_check[]" type="checkbox"
        //                             value="1" data-kt-check="true" data-kt-check-target=".widget-9-check" />
        //                 </div>`;
        //   },
        // },

        // 19 agus
        {
          targets: -1,
          orderable: true,
          className: "text-end",
          width: "150px",
          render: function (data, type, row, meta) {
            let isChecked = selectedBanks.includes(row.id) ? "checked" : "";
            return `<div class="form-check form-check-sm form-check-custom form-check-solid text-end" data-bs-toggle="tooltip" data-bs-placement="top" title="Tooltip on top">
                          <input onclick="checkBank(${row.id}, '${to_date(
              row.settlement_date
            )}', '${row.mid}', '${row.internal_payment}','${row.total_sales}')"
                              id="checkbox_bank_${row.id}"
                              class="form-check-input boCheckbox"
                              name="bo_check[]"
                              type="checkbox"
                              value="1"
                              ${isChecked}
                              data-kt-check="true"
                              data-kt-check-target=".widget-9-check" />
                      </div>`;
          },
        },

        {
          targets: 0,
          orderable: true,
          searchable: false,
          className: "text-start",
          width: "800px",
          render: function (data, type, row) {
            return to_date(data);
          },
        },
        {
          targets: 4,
          orderable: true,
          className: "text-end",
          width: "250px",
          render: function (data, type, row) {
            return to_rupiah(parseInt(data));
          },
        },
      ],

      createdRow: function (row, data, dataIndex) {
        $(row).find("td:eq(4)").attr("data-filter", data.id);
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
    // const filterSearch2 = document.querySelector(
    //   '[data-kt-docs-table-filter="searchBank"]'
    // );
    const filterSearch2 = document.getElementById("searchBank");
    filterSearch2.addEventListener("keyup", function (e) {
      dt.search(e.target.value).draw();
    });
  };

  var handleBankSelection = function () {
    const bankSelect = document.getElementById("bankSettlementSearch");
    bankSelect.addEventListener("change", function (e) {
      selectedBank = e.target.value;
      reloadDatatable();
    });
  };

  var initDateRangePicker = function () {
    $("#kt_daterangepicker_3").daterangepicker(
      {
        opens: "left",
        locale: {
          format: "YYYY-MM-DD",
        },
        startDate: moment().startOf("month"),
        endDate: moment().endOf("month"),
      },
      function (start, end, label) {
        startDate = start.format("YYYY-MM-DD");
        endDate = end.format("YYYY-MM-DD");
        reloadDatatable();
      }
    );
  };

  var clearFilter = function () {
    const clear = document.getElementById("clearBankSearch");
    clear.addEventListener("click", function (e) {
      const filterSearch = document.getElementById("searchBank");
      filterSearch.value = "";
      dt.search("").draw();

      const bankSelect = document.getElementById("bankSettlementSearch");
      bankSelect.value = "";
      // selectedBank = "";
      // selectedBanks = [];

      $("#kt_daterangepicker_1")
        .data("daterangepicker")
        .setStartDate(moment().startOf("month"));
      $("#kt_daterangepicker_1")
        .data("daterangepicker")
        .setEndDate(moment().endOf("month"));
      startDate = moment().startOf("month").format("YYYY-MM-DD");
      endDate = moment().endOf("month").format("YYYY-MM-DD");
      reloadDatatable();
    });
  };

  return {
    init: function () {
      initDatatable();
      handleSearchDatatable();
      handleBankSelection();
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

var KTDatatablesServerSideBO = (function () {
  var dt;
  var regex = /\/reconcile-list\/detail\/([^\/]+)/;
  var tokenA = getTokenFromUrl(regex);
  var startDate = "";
  var endDate = "";
  var selectedBank = "";
  var url = `${baseUrl}/settlement/bo/datadraft/` + tokenA;

  var initDatatable = function () {
    dt = $("#bo_settlement_table").DataTable({
      searchDelay: 200,
      processing: true,
      serverSide: true,
      order: [[0, "asc"]],
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
          d.bank = selectedBank;
        },
      },
      columns: [
        { data: "created_at" },
        // { data: "batch_fk" },
        { data: "merchant_name" },
        // { data: "processor" },
        { data: "mid" },
        { data: "bank_transfer" },
        { data: "id" },
      ],
      columnDefs: [
        // {
        //   targets: -1,
        //   orderable: true,
        //   className: "text-start",
        //   width: "150px",
        //   render: function (data, type, row, meta) {
        //     // console.log(row);
        //     // return meta.row + 1;
        //     return `
        //                     <div class="form-check form-check-sm form-check-custom form-check-solid">
        //                         <input onclick="checkBo(${row.id}, '${row.created_at}', '${row.processor}',
        //                         '${row.mid}', '${row.bank_transfer}')"
        //                         id="checkbox_bo_${row.id}" class="form-check-input" name="bo_check[]"
        //                         type="checkbox" value="1" data-kt-check="true" data-kt-check-target=".widget-9-check" />
        //                     </div>
        //                 `;
        //   },
        // },

        // 19 AGUS
        {
          targets: -1,
          orderable: true,
          className: "text-start",
          width: "150px",
          render: function (data, type, row, meta) {
            // Check if the current row's ID is in selectedBo and set the checkbox as checked
            let isChecked = selectedBo.includes(row.id) ? "checked" : "";

            return `
                  <div class="form-check form-check-sm form-check-custom form-check-solid">
                      <input onclick="checkBo(${row.id}, '${row.created_at}', '${row.processor}', '${row.mid}', '${row.bank_transfer}')"
                          id="checkbox_bo_${row.id}"
                          class="form-check-input"
                          name="bo_check[]"
                          type="checkbox"
                          value="1"
                          data-kt-check="true"
                          data-kt-check-target=".widget-9-check"
                          ${isChecked} />
                  </div>
              `;
          },
        },

        {
          targets: 0,
          orderable: true,
          searchable: false,
          className: "text-start",
          width: "800px",
          render: function (data, type, row) {
            return to_date(data);
          },
        },
        {
          targets: 2,
          orderable: true,
          className: "text-center",
          width: "30px",
          render: function (data, type, row) {
            return data;
          },
        },
        // {
        //     targets: 3,
        //     orderable: true,
        //     className: "text-center w-100",
        //     width: "50px",
        //     render: function (data, type, row) {
        //         return `
        //             <div class="d-flex justify-content-center mb-1">
        //             ${data}
        //             </div>
        //         `;
        //     },
        // },
        {
          targets: 4,
          orderable: true,
          className: "text-end",
          width: "200px",
          render: function (data, type, row) {
            return to_rupiah(parseInt(data));
          },
        },
      ],

      createdRow: function (row, data, dataIndex) {
        $(row).find("td:eq(4)").attr("data-filter", data.id);
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
    const filterSearch3 = document.getElementById("searchBO");
    // const filterSearch3 = document.querySelector(
    //   '[data-kt-docs-table-filter="searchBo"]'
    // );

    filterSearch3.addEventListener("keyup", function (e) {
      console.log(e.target.value);
      dt.search(e.target.value).draw();
    });
  };
  var handleBankSelection = function () {
    const bankSelect = document.getElementById("bankSettlementBoSearch");
    bankSelect.addEventListener("change", function (e) {
      selectedBank = e.target.value;
      reloadDatatable();
    });
  };

  var initDateRangePicker = function () {
    $("#kt_daterangepicker_9").daterangepicker(
      {
        locale: {
          format: "YYYY-MM-DD",
        },
        opens: "left",
        startDate: moment().startOf("month"),
        endDate: moment().endOf("month"),
      },
      function (start, end, label) {
        startDate = start.format("YYYY-MM-DD");
        endDate = end.format("YYYY-MM-DD");
        reloadDatatable();
      }
    );
  };

  var clearFilter = function () {
    const clear = document.getElementById("clearBoSearch");
    clear.addEventListener("click", function (e) {
      const filterSearch3 = document.getElementById("searchBO");
      filterSearch3.value = "";
      dt.search("").draw();

      const bankSelect = document.getElementById("bankSettlementBoSearch");
      bankSelect.value = "";
      // selectedBo = "";

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
      handleBankSelection();
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
var headMid = [];

var totalBankPayment = 0;
var selectedBo = [];

$("#unrefreshButton").on("click", function () {
  var tbodyTab = document.querySelector("#dataTable tbody");
  var tfootTab = document.querySelector("#dataTable tfoot");
  var tbodyBank = document.querySelector("#bank_selected_items tbody");
  var tfootBank = document.querySelector("#bank_selected_items tfoot");
  var tbodyBo = document.querySelector("#bo_selected_items tbody");
  var tfootBo = document.querySelector("#bo_selected_items tfoot");

  tbodyTab.innerHTML = "";
  tfootTab.innerHTML = "";
  tbodyBank.innerHTML = "";
  tfootBank.innerHTML = "";
  tbodyBo.innerHTML = "";
  tfootBo.innerHTML = "";

  $('input[type="checkbox"]').prop("checked", false);
  totalBankPayment = 0;
  totalBankSettlement = 0;
  data = [];
  selectedBanks = [];
  selectedBo = [];
});

$("#singleReconcile").on("submit", function (event) {
  event.preventDefault();
  var token = $('meta[name="csrf-token"]').attr("content");
  var formData = new FormData(this);
  formData.append("selectedBo", selectedBo);
  formData.append("selectedBank", selectedBanks);
  $.ajax({
    headers: { "X-CSRF-TOKEN": token },
    type: "POST",
    data: formData,
    url: baseUrl + "/reconcile/single/" + tokenA,
    dataType: "JSON",
    cache: false,
    contentType: false,
    processData: false,
    beforeSend: function () {
      swal.showLoading();
    },
    success: function (data) {
      if (data.status === true) {
        swal.hideLoading();
        swal
          .fire({
            text: data.message,
            icon: "success",
            buttonsStyling: false,
            confirmButtonText: "Ok, got it!",
            customClass: {
              confirmButton: "btn font-weight-bold btn-light-primary",
            },
          })
          .then(function () {
            // location.href = baseUrl + "/reconcile/result";
            window.location.reload();
          });
      } else {
        var values = "";
        jQuery.each(data.message, function (key, value) {
          values += value + "<br>";
        });

        swal
          .fire({
            text: data.message,
            html: values,
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "Ok, got it!",
            customClass: {
              confirmButton: "btn font-weight-bold btn-light-primary",
            },
          })
          .then(function () {});
      }
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
  });
});

$("#kt_daterangepicker_1").daterangepicker();
$("#kt_daterangepicker_2").daterangepicker();

// var totalBankSettlement = 0;
// var totalBankPayment = 0;

function updateCombinedTotal() {
  if (data.length == 0) {
    totalBankSettlement = 0;
    totalBankPayment = 0;
  }
  var combinedTotal = totalBankSettlement + totalBankPayment;
  var combinedTotalElement = document.getElementById("combined_total");
  var tfoot = document.querySelector("#dataTable tfoot");

  if (!combinedTotalElement) {
    combinedTotalElement = document.createElement("tr");
    combinedTotalElement.setAttribute("id", "combined_total");
    combinedTotalElement.innerHTML = `
      <td></td>
      <td colspan="2" class="text-end">Bank Settlement : ${to_rupiah(
        totalBankSettlement
      )}</td>
      <td></td>
      <td colspan="2" class="text-end">Bank Movement : ${to_rupiah(
        totalBankPayment
      )}</td>
      <td colspan="2" class="text-end"></td>
      `;
    tfoot.appendChild(combinedTotalElement);
  } else {
    combinedTotalElement.innerHTML = `
      <td></td>
      <td colspan="2" class="text-end">Bank Settlement : ${to_rupiah(
        totalBankSettlement
      )}</td>
      <td></td>
      <td colspan="2" class="text-end">Bank Movement : ${to_rupiah(
        totalBankPayment
      )}</td>
      <td colspan="2" class="text-end"></td>
    `;
  }
}

// Variabel global untuk menyimpan total sales
let totalSales = 0;

function checkBank(id, settlementDate, mid, bankSettlement, total_sales) {
  var checkbox = document.getElementById(`checkbox_bank_${id}`);
  var tbody = document.querySelector("#bank_selected_items tbody");
  var tfoot = document.querySelector("#bank_selected_items tfoot");

  if (checkbox.checked) {
    if (!selectedBanks.includes(id)) {
      selectedBanks.push(id);
    }

    // Tambahkan nilai total_sales ke totalSales
    totalSales += parseInt(total_sales);

    var row = document.getElementById(`row_${mid}`);
    if (!row) {
      row = document.createElement("tr");
      row.setAttribute("id", `row_${mid}`);
      row.innerHTML = `
                <td>${mid}</td>
                <td><ul id="dates_${mid}"></ul></td>
                <td class="text-end"><ul id="amounts_${mid}"></ul></td>
                <td class="text-end total" id="total_${mid}">${to_rupiah(
        bankSettlement
      )}</td>
                <td><button id="remove_${mid}" class="btn btn-danger">x</button></td>
            `;
      tbody.appendChild(row);
    } else {
      document.querySelector(`#total_${mid}`).innerText = to_rupiah(
        parseInt(
          document.querySelector(`#total_${mid}`).innerText.replace(/\D/g, "")
        ) + parseInt(bankSettlement)
      );
    }

    document.querySelector(
      `#dates_${mid}`
    ).innerHTML += `<li>${settlementDate}</li>`;
    document.querySelector(`#amounts_${mid}`).innerHTML += `<li>${to_rupiah(
      bankSettlement
    )}</li>`;

    totalBankSettlement += parseInt(bankSettlement);
    tfoot.innerHTML = `
            <td colspan="2" class="text-start">Total</td>
            <td colspan="2" class="text-end">${to_rupiah(
              totalBankSettlement
            )}</td>
        `;

    document
      .getElementById(`remove_${mid}`)
      .addEventListener("click", function () {
        var rowToRemove = document.getElementById(`row_${mid}`);
        if (rowToRemove) {
          rowToRemove.remove();
          selectedBanks = selectedBanks.filter((bankId) => bankId !== id);
          totalBankSettlement -= parseInt(bankSettlement);
          totalSales -= parseInt(total_sales); // Kurangi totalSales jika dihapus
          tfoot.innerHTML = `
                    <td colspan="2" class="text-start">Total</td>
                    <td colspan="2" class="text-end">${to_rupiah(
                      totalBankSettlement
                    )}</td>
                `;

          checkbox.checked = false;

          handleCheckboxChange(
            mid,
            parseInt(bankSettlement),
            false,
            settlementDate,
            "uang"
          );
          updateCombinedTotal();
        }
      });

    handleCheckboxChange(
      mid,
      parseInt(bankSettlement),
      true,
      settlementDate,
      "uang"
    );
    updateCombinedTotal();
  } else {
    selectedBanks = selectedBanks.filter((bankId) => bankId !== id);

    totalBankSettlement -= parseInt(bankSettlement);
    totalSales -= parseInt(total_sales); // Kurangi totalSales jika checkbox tidak dipilih
    tfoot.innerHTML = `
            <td colspan="2" class="text-start">Total</td>
            <td colspan="2" class="text-end">${to_rupiah(
              totalBankSettlement
            )}</td>
        `;

    var row = document.getElementById(`row_${mid}`);
    if (row) {
      row.remove();
    }

    handleCheckboxChange(
      mid,
      parseInt(bankSettlement),
      false,
      settlementDate,
      "uang"
    );
    updateCombinedTotal();
  }
}

// function checkBank(id, settlementDate, mid, bankSettlement) {
//   var checkbox = document.getElementById(`checkbox_bank_${id}`);
//   var tbody = document.querySelector("#bank_selected_items tbody");
//   var tfoot = document.querySelector("#bank_selected_items tfoot");

//   if (checkbox.checked) {
//     if (!selectedBanks.includes(id)) {
//       selectedBanks.push(id);
//     }

//     var row = document.getElementById(`row_${mid}`);
//     if (!row) {
//       row = document.createElement("tr");
//       row.setAttribute("id", `row_${mid}`);
//       row.innerHTML = `
//                 <td>${mid}</td>
//                 <td><ul id="dates_${mid}"></ul></td>
//                 <td class="text-end"><ul id="amounts_${mid}"></ul></td>
//                 <td class="text-end total" id="total_${mid}">${to_rupiah(
//         bankSettlement
//       )}</td>
//                 <td><button id="remove_${mid}" class="btn btn-danger">x</button></td>
//             `;
//       tbody.appendChild(row);
//     } else {
//       document.querySelector(`#total_${mid}`).innerText = to_rupiah(
//         parseInt(
//           document.querySelector(`#total_${mid}`).innerText.replace(/\D/g, "")
//         ) + parseInt(bankSettlement)
//       );
//     }

//     document.querySelector(
//       `#dates_${mid}`
//     ).innerHTML += `<li>${settlementDate}</li>`;
//     document.querySelector(`#amounts_${mid}`).innerHTML += `<li>${to_rupiah(
//       bankSettlement
//     )}</li>`;

//     totalBankSettlement += parseInt(bankSettlement);
//     tfoot.innerHTML = `
//             <td colspan="2" class="text-start">Total</td>
//             <td colspan="2" class="text-end">${to_rupiah(
//               totalBankSettlement
//             )}</td>
//         `;

//     document
//       .getElementById(`remove_${mid}`)
//       .addEventListener("click", function () {
//         var rowToRemove = document.getElementById(`row_${mid}`);
//         if (rowToRemove) {
//           rowToRemove.remove();
//           selectedBanks = selectedBanks.filter((bankId) => bankId !== id);
//           totalBankSettlement -= parseInt(bankSettlement);
//           tfoot.innerHTML = `
//                     <td colspan="2" class="text-start">Total</td>
//                     <td colspan="2" class="text-end">${to_rupiah(
//                       totalBankSettlement
//                     )}</td>
//                 `;

//           checkbox.checked = false;

//           handleCheckboxChange(
//             mid,
//             parseInt(bankSettlement),
//             false,
//             settlementDate,
//             "uang"
//           );
//           updateCombinedTotal();
//         }
//       });

//     handleCheckboxChange(
//       mid,
//       parseInt(bankSettlement),
//       true,
//       settlementDate,
//       "uang"
//     );
//     updateCombinedTotal();
//   } else {
//     selectedBanks = selectedBanks.filter((bankId) => bankId !== id);

//     totalBankSettlement -= parseInt(bankSettlement);
//     tfoot.innerHTML = `
//             <td colspan="2" class="text-start">Total</td>
//             <td colspan="2" class="text-end">${to_rupiah(
//               totalBankSettlement
//             )}</td>
//         `;

//     var row = document.getElementById(`row_${mid}`);
//     if (row) {
//       row.remove();
//     }

//     handleCheckboxChange(
//       mid,
//       parseInt(bankSettlement),
//       false,
//       settlementDate,
//       "uang"
//     );
//     updateCombinedTotal();
//   }
// }

function checkBo(id, settlementDate, bankType, mid, bankPayment) {
  var checkbox = document.getElementById(`checkbox_bo_${id}`);
  var tbody = document.querySelector("#bo_selected_items tbody");
  var tfoot = document.querySelector("#bo_selected_items tfoot");

  if (checkbox.checked) {
    if (!selectedBo.includes(id)) {
      selectedBo.push(id);
    }

    var row = document.getElementById(`row_${mid}`);
    if (!row) {
      row = document.createElement("tr");
      row.setAttribute("id", `row_${mid}`);
      row.innerHTML = `
                <td>${mid}</td>
                <td><ul id="dates_${mid}"></ul></td>
                <td class="text-end"><ul id="amounts_${mid}"></ul></td>
                <td class="text-end total" id="total_${mid}">${to_rupiah(
        bankPayment
      )}</td>
                <td><button id="remove_${mid}" class="btn btn-danger">x</button></td>
            `;
      tbody.appendChild(row);
    } else {
      document.querySelector(`#total_${mid}`).innerText = to_rupiah(
        parseInt(
          document.querySelector(`#total_${mid}`).innerText.replace(/\D/g, "")
        ) + parseInt(bankPayment)
      );
    }

    document.querySelector(`#dates_${mid}`).innerHTML += `<li>${to_date(
      settlementDate
    )}</li>`;
    document.querySelector(`#amounts_${mid}`).innerHTML += `<li>${to_rupiah(
      bankPayment
    )}</li>`;

    totalBankPayment += parseInt(bankPayment);
    tfoot.innerHTML = `
            <td colspan="3" class="text-start">Total</td>
            <td colspan="2" class="text-end">${to_rupiah(totalBankPayment)}</td>
        `;

    document
      .getElementById(`remove_${mid}`)
      .addEventListener("click", function () {
        var rowToRemove = document.getElementById(`row_${mid}`);
        if (rowToRemove) {
          rowToRemove.remove();
          selectedBo = selectedBo.filter((boId) => boId !== id);
          totalBankPayment -= parseInt(bankPayment);
          tfoot.innerHTML = `
                    <td colspan="3" class="text-start">Total</td>
                    <td colspan="2" class="text-end">${to_rupiah(
                      totalBankPayment
                    )}</td>
                `;

          checkbox.checked = false;

          handleCheckboxChange(
            mid,
            parseInt(bankPayment),
            false,
            settlementDate,
            "tabungan"
          );
          updateCombinedTotal();
        }
      });

    handleCheckboxChange(
      mid,
      parseInt(bankPayment),
      true,
      settlementDate,
      "tabungan"
    );
    updateCombinedTotal();
  } else {
    selectedBo = selectedBo.filter((boId) => boId !== id);

    totalBankPayment -= parseInt(bankPayment);
    tfoot.innerHTML = `
            <td colspan="3" class="text-start">Total</td>
            <td colspan="2" class="text-end">${to_rupiah(totalBankPayment)}</td>
        `;

    var row = document.getElementById(`row_${mid}`);
    if (row) {
      row.remove();
    }

    handleCheckboxChange(
      mid,
      parseInt(bankPayment),
      false,
      settlementDate,
      "tabungan"
    );
    updateCombinedTotal();
  }
}

const originalData = [
  // { mid: '000002187010754', uang: 5000, tabungan: 3000 },
];

// Array data saat ini
let data = [];

// Daftar uang dan tabungan yang bisa ditambahkan
const moneyOptions = [
  // { id: "1", mid: "000002187010754", money_date: "1999-07-23", jumlah: 1000 },
];

const savingOptions = [
  // { id: "1", mid: "000002187010754", saving_date: "1999-07-23", jumlah: 1000 },
];

// Menampilkan data dalam tabel
// function renderTable() {
//   const tableBody = document.querySelector("#dataTable tbody");
//   tableBody.innerHTML = ""; // Bersihkan tabel sebelum diisi

//   // Mengelompokkan data per nama
//   const groupedData = data.reduce((acc, item) => {
//     if (!acc[item.mid]) {
//       acc[item.mid] = {
//         totalUang: 0,
//         totalTabungan: 0,
//         uangList: [],
//         tabunganList: [],
//         uangDates: [],
//         tabunganDates: [],
//       };
//     }
//     if (item.uang) {
//       acc[item.mid].totalUang += item.uang;
//       acc[item.mid].uangList.push(item.uang);
//       acc[item.mid].uangDates.push(item.date);
//     }
//     if (item.tabungan) {
//       acc[item.mid].totalTabungan += item.tabungan;
//       acc[item.mid].tabunganList.push(item.tabungan);
//       acc[item.mid].tabunganDates.push(item.date);
//     }
//     return acc;
//   }, {});

//   // Menampilkan data yang dikelompokkan dalam tabel
//   Object.keys(groupedData).forEach((name) => {
//     const group = groupedData[name];

//     const row = document.createElement("tr");
//     row.className = " border-bottom border-primary";

//     const nameCell = document.createElement("td");
//     nameCell.textContent = name;
//     row.appendChild(nameCell);

//     const uangCell = document.createElement("td");
//     uangCell.innerHTML = `<ul class="money-list">
//           ${group.uangList
//             .map(
//               (amount, index) =>
//                 `<li class="row align-items-center my-3">
//           <div class="col">
//           ${to_rupiah(amount)}
//           </div>
//           </li>
//           `
//               // <div class="col">
//               // <button class="btn btn-sm btn-danger" onclick="removeAmount('${name}', ${amount}, 'uang')">x</button>
//               // </div>
//             )
//             .join("")}</ul>`;
//     row.appendChild(uangCell);

//     const totalUangCell = document.createElement("td");
//     totalUangCell.textContent = to_rupiah(group.totalUang);
//     row.appendChild(totalUangCell);

//     const uangDateCell = document.createElement("td");
//     uangDateCell.innerHTML = `<ul class="money-date-list">
//           ${group.uangDates
//             .map(
//               (date) =>
//                 `<li class="row align-items-center my-3">
//           <div class="col">
//           ${to_date(date)}
//           </div>
//           </li>
//           `
//             )
//             .join("")}</ul>`;
//     row.appendChild(uangDateCell);

//     const tabunganCell = document.createElement("td");
//     tabunganCell.innerHTML = `<ul class="saving-list">${group.tabunganList
//       .map(
//         (amount, index) =>
//           `
//         <li class="row align-items-center my-3">
//           <div class="col">
//           ${to_rupiah(amount)}
//           </div>
//           </li>
//           `
//         // <div class="col">
//         // <button class="btn btn-sm btn-danger" onclick="removeAmount('${name}', ${amount}, 'tabungan')">x</button>
//         // </div>
//       )
//       .join("")}</ul>`;
//     row.appendChild(tabunganCell);

//     const totalTabunganCell = document.createElement("td");
//     totalTabunganCell.textContent = to_rupiah(group.totalTabungan);
//     row.appendChild(totalTabunganCell);

//   console.log("Total Sales: " + totalSales);
//     console.log("Tabungan : "+group.totalTabungan)
//     console.log("Uang : "+group.totalUang)
//     // console.log((abs(group.totalTabungan - group.totalUang) / totalSales * 100) == 1);

//     const tabDateCell = document.createElement("td");
//     tabDateCell.innerHTML = `<ul class="saving-date-list">
//           ${group.tabunganDates
//             .map(
//               (date) =>
//                 `<li class="row align-items-center my-3">
//           <div class="col">
//           ${to_date(date)}
//           </div>
//           </li>
//           `
//             )
//             .join("")}</ul>`;
//     row.appendChild(tabDateCell);

//     // Kolom selisih
//     const selisihCell = document.createElement("td");
//     selisihCell.textContent = to_rupiah(group.totalTabungan - group.totalUang);
//     row.appendChild(selisihCell);

//     const statusCell = document.createElement("td");
//     statusCell.innerHTML =
//       group.totalUang === group.totalTabungan
//         ? "<div class='badge badge-success'>MATCH</div>"
//         : "<div class='badge badge-danger'>NOT MATCH</div>";
//     row.appendChild(statusCell);

//     // const actionCell = document.createElement("td");
//     // const deleteButton = document.createElement("button");
//     // deleteButton.className = "btn btn-sm btn-danger";
//     // deleteButton.textContent = "Cancel";
//     // deleteButton.onclick = () => deleteItem(name);
//     // actionCell.appendChild(deleteButton);
//     // row.appendChild(actionCell);

//     tableBody.appendChild(row);
//   });
// }

function renderTable() {
  const tableBody = document.querySelector("#dataTable tbody");
  tableBody.innerHTML = ""; // Bersihkan tabel sebelum diisi

  // Mengelompokkan data per mid
  const groupedData = data.reduce((acc, item) => {
    if (!acc[item.mid]) {
      acc[item.mid] = {
        totalUang: 0,
        totalTabungan: 0,
        totalSales: 0, // Tambahkan totalSales per mid
        uangList: [],
        tabunganList: [],
        uangDates: [],
        tabunganDates: [],
      };
    }
    if (item.uang) {
      acc[item.mid].totalUang += item.uang;
      acc[item.mid].uangList.push(item.uang);
      acc[item.mid].uangDates.push(item.date);
    }
    if (item.tabungan) {
      acc[item.mid].totalTabungan += item.tabungan;
      acc[item.mid].tabunganList.push(item.tabungan);
      acc[item.mid].tabunganDates.push(item.date);
    }
    if (item.sales) {
      acc[item.mid].totalSales += item.sales; // Tambahkan sales ke totalSales
    }
    return acc;
  }, {});

  // Menampilkan data yang dikelompokkan dalam tabel
  Object.keys(groupedData).forEach((mid) => {
    const group = groupedData[mid];

    const row = document.createElement("tr");
    row.className = "border-bottom border-primary";

    const nameCell = document.createElement("td");
    nameCell.textContent = mid;
    row.appendChild(nameCell);

    const uangCell = document.createElement("td");
    uangCell.innerHTML = `<ul class="money-list">
          ${group.uangList
            .map(
              (amount, index) =>
                `<li class="row align-items-center my-3">
          <div class="col">
          ${to_rupiah(amount)} 
          </div>
          </li>`
            )
            .join("")}</ul>`;
    row.appendChild(uangCell);

    const totalUangCell = document.createElement("td");
    totalUangCell.textContent = to_rupiah(group.totalUang);
    row.appendChild(totalUangCell);

    const uangDateCell = document.createElement("td");
    uangDateCell.innerHTML = `<ul class="money-date-list">
          ${group.uangDates
            .map(
              (date) =>
                `<li class="row align-items-center my-3">
          <div class="col">
          ${to_date(date)} 
          </div>
          </li>`
            )
            .join("")}</ul>`;
    row.appendChild(uangDateCell);

    const tabunganCell = document.createElement("td");
    tabunganCell.innerHTML = `<ul class="saving-list">${group.tabunganList
      .map(
        (amount, index) =>
          `<li class="row align-items-center my-3">
          <div class="col">
          ${to_rupiah(amount)} 
          </div>
          </li>`
      )
      .join("")}</ul>`;
    row.appendChild(tabunganCell);

    const totalTabunganCell = document.createElement("td");
    totalTabunganCell.textContent = to_rupiah(group.totalTabungan);
    row.appendChild(totalTabunganCell);

    const tabDateCell = document.createElement("td");
    tabDateCell.innerHTML = `<ul class="saving-date-list">
          ${group.tabunganDates
            .map(
              (date) =>
                `<li class="row align-items-center my-3">
          <div class="col">
          ${to_date(date)} 
          </div>
          </li>`
            )
            .join("")}</ul>`;
    row.appendChild(tabDateCell);

    // Kolom selisih
    // const selisihCell = document.createElement("td");
    // selisihCell.textContent = to_rupiah(group.totalTabungan - group.totalUang);
    // row.appendChild(selisihCell);
    const selisihCell = document.createElement("td");
    const selisih = group.totalTabungan - group.totalUang;

    if (selisih < 0) {
      selisihCell.textContent = `( - ${to_rupiah(Math.abs(selisih))} )`;
    } else {
      selisihCell.textContent = `${to_rupiah(selisih)}`;
    }

    row.appendChild(selisihCell);

    // Kolom status
    // const statusCell = document.createElement("td");
    // statusCell.innerHTML =
    //   group.totalUang === group.totalTabungan
    //     ? "<div class='badge badge-success'>MATCH</div>"
    //     : "<div class='badge badge-danger'>NOT MATCH</div>";
    // row.appendChild(statusCell);

    const statusCell = document.createElement("td");
    statusCell.innerHTML =
      Math.abs(group.totalUang - group.totalTabungan) < 100 ||
      group.totalUang === group.totalTabungan
        ? "<div class='badge badge-success'>MATCH</div>"
        : "<div class='badge badge-danger'>NOT MATCH</div>";
    row.appendChild(statusCell);

    tableBody.appendChild(row);
  });
}

// Fungsi untuk menghapus item berdasarkan mid
// function deleteItem(mid) {
//   const confirmation = confirm(`Anda yakin ingin menghapus data dengan MID ${mid}?`);
//   if (confirmation) {
//     data = data.filter(item => item.mid !== mid);
//     renderTable();
//   }
// }

// Fungsi untuk mengedit item berdasarkan mid
// function editItem(mid) {
//   const newMid = prompt("Masukkan MID baru:", mid);
//   if (newMid !== null && newMid !== mid) {
//     data = data.map(item => (item.mid === mid ? { ...item, mid: newMid } : item));
//     renderTable();
//   }
// }

// Menghapus jumlah tertentu dari data

function removeAmount(name, amount, type) {
  data = data.filter((d) => !(d.mid === name && d[type] === amount));

  // Remove the corresponding checkbox
  updateCheckboxState(name, amount, type, false);

  // Update totals based on the type
  if (type === "uang") {
    totalBankSettlement -= amount;
  } else if (type === "tabungan") {
    totalBankPayment -= amount;
  }

  // Update the table
  renderTable();

  // Update combined total
  updateCombinedTotal();
}

// Menghapus semua item untuk nama tertentu
function deleteItem(name) {
  data = data.filter((d) => d.mid !== name);
  // Uncheck all checkboxes for this name
  updateCheckboxState(name, null, null, false);
  renderTable();
}

// Menampilkan daftar uang yang bisa ditambahkan
function renderMoneyOptions() {
  const moneyList = document.querySelector("#moneyList");
  moneyList.innerHTML = ""; // Bersihkan daftar sebelum diisi

  moneyOptions.forEach((option) => {
    const checkbox = document.createElement("input");
    checkbox.className = "form-check-input";
    checkbox.type = "checkbox";
    checkbox.value = option.jumlah;
    checkbox.dataset.mid = option.mid;
    checkbox.dataset.money_date = option.money_date;
    checkbox.dataset.type = "uang";
    checkbox.dataset.id = option.id; // Tambahkan ID
    checkbox.onchange = () =>
      handleCheckboxChange(
        option.mid,
        parseInt(checkbox.value),
        checkbox.checked,
        option.money_date,
        "uang"
      );

    const label = document.createElement("label");
    label.className = "form-label";
    label.textContent = `${option.mid}: ${option.jumlah}`;

    moneyList.appendChild(label);
    label.appendChild(checkbox);
    moneyList.appendChild(document.createElement("br"));
  });
}

// Menampilkan daftar tabungan yang bisa ditambahkan
function renderSavingOptions() {
  const savingList = document.querySelector("#savingList");
  savingList.innerHTML = ""; // Bersihkan daftar sebelum diisi

  savingOptions.forEach((option) => {
    const label = document.createElement("label");
    label.textContent = `${option.mid}: ${option.jumlah}`;

    const checkbox = document.createElement("input");
    checkbox.type = "checkbox";
    checkbox.value = option.jumlah;
    checkbox.dataset.mid = option.mid;
    checkbox.dataset.saving_date = option.saving_date;
    checkbox.dataset.type = "tabungan";
    checkbox.dataset.id = option.id; // Tambahkan ID
    checkbox.onchange = () =>
      handleCheckboxChange(
        option.mid,
        parseInt(checkbox.value),
        checkbox.checked,
        option.saving_date,
        "tabungan"
      );

    label.appendChild(checkbox);
    savingList.appendChild(label);
    savingList.appendChild(document.createElement("br"));
  });
}

// Menangani perubahan checkbox
function handleCheckboxChange(name, amount, checked, date, type) {
  if (checked) {
    data.push({ mid: name, [type]: amount, date: date });
  } else {
    data = data.filter((d) => !(d.mid === name && d[type] === amount));
  }
  renderTable();
}

// Mengupdate status checkbox
function updateCheckboxState(name, amount, type, checked) {
  const checkboxes = document.querySelectorAll(
    `#moneyList input[type="checkbox"], #savingList input[type="checkbox"]`
  );
  checkboxes.forEach((checkbox) => {
    if (
      checkbox.dataset.mid === name &&
      (amount === null || parseInt(checkbox.value) === amount)
    ) {
      checkbox.checked = checked;
    }
  });
}

// FUNCTION BUANGAN----------------------------------------------------------------------------------------------------

// function updateCheckboxState2(id, type, checked) {
//   // Check all checkboxes for both bank and BO types
//   const checkboxes = document.querySelectorAll(
//     `#moneyList input[type="checkbox"], #savingList input[type="checkbox"]`
//   );
//   checkboxes.forEach((checkbox) => {
//     // Update checkbox based on the type and ID
//     if (type === 'bank' && checkbox.id === `checkbox_bank_${id}`) {
//       checkbox.checked = checked;
//     } else if (type === 'bo' && checkbox.id === `checkbox_bo_${id}`) {
//       checkbox.checked = checked;
//     }
//   });
// }

// function deleteItem3(name) {
//   console.log(`Deleting items for: ${name}`);

//   data = data.filter((d) => d.mid !== name);

//   console.log(`Unchecking checkboxes for: ${name}`);
//   document.querySelectorAll(`#bank_selected_items input[id^="checkbox_bank_${name}"]`).forEach((checkbox) => {
//     console.log(`Unchecking: ${checkbox.id}`);
//     checkbox.checked = false;
//   });

//   document.querySelectorAll(`#bo_selected_items input[id^="checkbox_bo_${name}"]`).forEach((checkbox) => {
//     console.log(`Unchecking: ${checkbox.id}`);
//     checkbox.checked = false;
//   });

//   const bankRows = document.querySelectorAll(`#bank_selected_items #bank_detail_${name}`);
//   bankRows.forEach(row => {
//     if (row) {
//       const bankSettlement = parseInt(row.querySelector('.total').innerText.replace(/\D/g, ''));
//       console.log(`Removing bank row with total: ${bankSettlement}`);
//       totalBankSettlement -= bankSettlement;
//       row.remove();
//     }
//   });

//   const boRows = document.querySelectorAll(`#bo_selected_items #bo_detail_${name}`);
//   boRows.forEach(row => {
//     if (row) {
//       const bankPayment = parseInt(row.querySelector('.total').innerText.replace(/\D/g, ''));
//       console.log(`Removing BO row with total: ${bankPayment}`);
//       totalBankPayment -= bankPayment;
//       row.remove();
//     }
//   });

//   updateCombinedTotal();
// }
// function removeAmount2(name, amount, type) {
//   data = data.filter((d) => !(d.mid === name && d[type] === amount));
//   // Remove the corresponding checkbox
//   updateCheckboxState(name, amount, type, false);
//   renderTable();
// }

// function checkBank3(id, settlementDate, mid, bankSettlement) {
//   var checkbox = document.getElementById(`checkbox_bank_${id}`);
//   var tbody = document.querySelector("#bank_selected_items tbody");
//   var tfoot = document.querySelector("#bank_selected_items tfoot");

//   if (checkbox.checked) {
//     selectedBanks.push(id);

//     var row = document.getElementById(`row_${mid}`);
//     if (!row) {
//       row = document.createElement("tr");
//       row.setAttribute("id", `row_${mid}`);
//       row.innerHTML = `
//             <td>${mid}</td>
//             <td><ul id="dates_${mid}"></ul></td>
//             <td class="text-end"><ul id="amounts_${mid}"></ul></td>
//             <td class="text-end total" id="total_${mid}">${to_rupiah(
//         bankSettlement
//       )}</td>
//             <td><button id="remove_${mid}" class="btn btn-danger">x</button></td>
//         `;
//       tbody.appendChild(row);
//     } else {
//       document.querySelector(`#total_${mid}`).innerText = to_rupiah(
//         parseInt(
//           document.querySelector(`#total_${mid}`).innerText.replace(/\D/g, "")
//         ) + parseInt(bankSettlement)
//       );
//     }

//     document.querySelector(
//       `#dates_${mid}`
//     ).innerHTML += `<li>${settlementDate}</li>`;
//     document.querySelector(`#amounts_${mid}`).innerHTML += `<li>${to_rupiah(
//       bankSettlement
//     )}</li>`;

//     totalBankSettlement += parseInt(bankSettlement);
//     tfoot.innerHTML = `
//             <td colspan="2" class="text-start">Total</td>
//             <td colspan="2" class="text-end">${to_rupiah(
//               totalBankSettlement
//             )}</td>
//         `;

//     document
//       .getElementById(`remove_${mid}`)
//       .addEventListener("click", function () {
//         var rowToRemove = document.getElementById(`row_${mid}`);
//         if (rowToRemove) {
//           rowToRemove.remove();

//           var idx = selectedBanks.indexOf(id);
//           if (idx !== -1) {
//             selectedBanks.splice(idx, 1);
//           }

//           totalBankSettlement -= parseInt(bankSettlement);
//           tfoot.innerHTML = `
//                 <td colspan="2" class="text-start">Total</td>
//                 <td colspan="2" class="text-end">${to_rupiah(
//                   totalBankSettlement
//                 )}</td>
//             `;

//           checkbox.checked = false;

//           // Update data when unchecked
//           handleCheckboxChange(
//             mid,
//             parseInt(bankSettlement),
//             false,
//             settlementDate,
//             "uang"
//           );
//           updateCombinedTotal();
//         }
//       });

//     // Update data when checked
//     handleCheckboxChange(
//       mid,
//       parseInt(bankSettlement),
//       true,
//       settlementDate,
//       "uang"
//     );
//     updateCombinedTotal();
//   } else {
//     var idx = selectedBanks.indexOf(id);
//     if (idx !== -1) {
//       selectedBanks.splice(idx, 1);
//     }

//     totalBankSettlement -= parseInt(bankSettlement);
//     tfoot.innerHTML = `
//             <td colspan="2" class="text-start">Total</td>
//             <td colspan="2" class="text-end">${to_rupiah(
//               totalBankSettlement
//             )}</td>
//         `;

//     var row = document.getElementById(`row_${mid}`);
//     if (row) {
//       row.remove();
//     }

//     // Update data when unchecked
//     handleCheckboxChange(
//       mid,
//       parseInt(bankSettlement),
//       false,
//       settlementDate,
//       "uang"
//     );
//     updateCombinedTotal();
//   }
// }

// function oldcheckBo(id, settlementDate, bankType, mid, bankPayment) {
//   var checkbox = document.getElementById(`checkbox_bo_${id}`);
//   var tbody = document.querySelector("#bo_selected_items tbody");
//   var tfoot = document.querySelector("#bo_selected_items tfoot");

//   if (checkbox.checked) {
//     // Clear existing rows
//     // tbody.innerHTML = "";

//     selectedBo.push(id);

//     var row = document.createElement("tr");
//     row.setAttribute("id", `bo_detail_${id}`);
//     // <td>${bankType}</td>
//     row.innerHTML = `
//             <td>${mid}</td>
//             <td>${to_date(settlementDate)}</td>
//             <td class="text-end">${to_rupiah(bankPayment)}</td>
//             <td><button id="butonbo_${id}" class="btn btn-danger">x</button></td>
//         `;
//     totalBankPayment = totalBankPayment + parseInt(bankPayment);
//     tbody.appendChild(row);
//     tfoot.innerHTML = `
//             <td colspan="3" class="text-start">Total</td>
//             <td colspan="2" class="text-end">${to_rupiah(totalBankPayment)}</td>
//         `;

//     document
//       .getElementById(`butonbo_${id}`)
//       .addEventListener("click", function () {
//         var rowToRemove = document.getElementById(`bo_detail_${id}`);
//         if (rowToRemove) {
//           rowToRemove.remove();

//           var idx = selectedBanks.indexOf(id);
//           if (idx !== -1) {
//             selectedBanks.splice(idx, 1);
//           }
//           totalBankPayment -= parseInt(bankPayment);
//           tfoot.innerHTML = `
//                 <td colspan="2" class="text-start">Total</td>
//                 <td colspan="2" class="text-end">${to_rupiah(
//                   totalBankPayment
//                 )}</td>
//             `;

//           checkbox.checked = false;
//         }
//       });
//   } else {
//     var idx = selectedBo.indexOf(id);
//     if (idx !== -1) {
//       selectedBo.splice(idx, 1);
//     }
//     totalBankPayment = totalBankPayment - parseInt(bankPayment);
//     tfoot.innerHTML = "";
//     tfoot.innerHTML = `
//             <td colspan="3" class="text-start">Total</td>
//             <td colspan="2" class="text-end">${to_rupiah(totalBankPayment)}</td>
//         `;
//     var row = document.getElementById(`bo_detail_${id}`);
//     row.remove();
//   }
// }

// function checkBo2(id, settlementDate, bankType, mid, bankPayment) {
//   var checkbox = document.getElementById(`checkbox_bo_${id}`);
//   var tbody = document.querySelector("#bo_selected_items tbody");
//   var tfoot = document.querySelector("#bo_selected_items tfoot");

//   if (checkbox.checked) {
//     selectedBo.push(id);

//     var row = document.getElementById(`row_${mid}`);
//     if (!row) {
//       row = document.createElement("tr");
//       row.setAttribute("id", `row_${mid}`);
//       row.innerHTML = `
//             <td>${mid}</td>
//             <td><ul id="dates_${mid}"></ul></td>
//             <td class="text-end"><ul id="amounts_${mid}"></ul></td>
//             <td class="text-end total" id="total_${mid}">${to_rupiah(
//         bankPayment
//       )}</td>
//             <td><button id="remove_${mid}" class="btn btn-danger">x</button></td>
//         `;
//       tbody.appendChild(row);
//     } else {
//       document.querySelector(`#total_${mid}`).innerText = to_rupiah(
//         parseInt(
//           document.querySelector(`#total_${mid}`).innerText.replace(/\D/g, "")
//         ) + parseInt(bankPayment)
//       );
//     }

//     document.querySelector(`#dates_${mid}`).innerHTML += `<li>${to_date(
//       settlementDate
//     )}</li>`;
//     document.querySelector(`#amounts_${mid}`).innerHTML += `<li>${to_rupiah(
//       bankPayment
//     )}</li>`;

//     totalBankPayment += parseInt(bankPayment);
//     tfoot.innerHTML = `
//             <td colspan="3" class="text-start">Total</td>
//             <td colspan="2" class="text-end">${to_rupiah(totalBankPayment)}</td>
//         `;

//     document
//       .getElementById(`remove_${mid}`)
//       .addEventListener("click", function () {
//         var rowToRemove = document.getElementById(`row_${mid}`);
//         if (rowToRemove) {
//           rowToRemove.remove();

//           var idx = selectedBo.indexOf(id);
//           if (idx !== -1) {
//             selectedBo.splice(idx, 1);
//           }

//           totalBankPayment -= parseInt(bankPayment);
//           tfoot.innerHTML = `
//                 <td colspan="3" class="text-start">Total</td>
//                 <td colspan="2" class="text-end">${to_rupiah(
//                   totalBankPayment
//                 )}</td>
//             `;

//           checkbox.checked = false;

//           // Update data when unchecked
//           handleCheckboxChange(
//             mid,
//             parseInt(bankPayment),
//             false,
//             settlementDate,
//             "tabungan"
//           );
//         }
//       });

//     // Update data when checked
//     handleCheckboxChange(
//       mid,
//       parseInt(bankPayment),
//       true,
//       settlementDate,
//       "tabungan"
//     );
//   } else {
//     var idx = selectedBo.indexOf(id);
//     if (idx !== -1) {
//       selectedBo.splice(idx, 1);
//     }

//     totalBankPayment -= parseInt(bankPayment);
//     tfoot.innerHTML = `
//             <td colspan="3" class="text-start">Total</td>
//             <td colspan="2" class="text-end">${to_rupiah(totalBankPayment)}</td>
//         `;

//     var row = document.getElementById(`row_${mid}`);
//     if (row) {
//       row.remove();
//     }

//     // Update data when unchecked
//     handleCheckboxChange(
//       mid,
//       parseInt(bankPayment),
//       false,
//       settlementDate,
//       "tabungan"
//     );
//   }
// }

// function checkBo3(id, settlementDate, bankType, mid, bankPayment) {
//   var checkbox = document.getElementById(`checkbox_bo_${id}`);
//   var tbody = document.querySelector("#bo_selected_items tbody");
//   var tfoot = document.querySelector("#bo_selected_items tfoot");

//   if (checkbox.checked) {
//     selectedBo.push(id);

//     var row = document.getElementById(`row_${mid}`);
//     if (!row) {
//       row = document.createElement("tr");
//       row.setAttribute("id", `row_${mid}`);
//       row.innerHTML = `
//             <td>${mid}</td>
//             <td><ul id="dates_${mid}"></ul></td>
//             <td class="text-end"><ul id="amounts_${mid}"></ul></td>
//             <td class="text-end total" id="total_${mid}">${to_rupiah(
//         bankPayment
//       )}</td>
//             <td><button id="remove_${mid}" class="btn btn-danger">x</button></td>
//         `;
//       tbody.appendChild(row);
//     } else {
//       document.querySelector(`#total_${mid}`).innerText = to_rupiah(
//         parseInt(
//           document.querySelector(`#total_${mid}`).innerText.replace(/\D/g, "")
//         ) + parseInt(bankPayment)
//       );
//     }

//     document.querySelector(`#dates_${mid}`).innerHTML += `<li>${to_date(
//       settlementDate
//     )}</li>`;
//     document.querySelector(`#amounts_${mid}`).innerHTML += `<li>${to_rupiah(
//       bankPayment
//     )}</li>`;

//     totalBankPayment += parseInt(bankPayment);
//     tfoot.innerHTML = `
//             <td colspan="3" class="text-start">Total</td>
//             <td colspan="2" class="text-end">${to_rupiah(totalBankPayment)}</td>
//         `;

//     document
//       .getElementById(`remove_${mid}`)
//       .addEventListener("click", function () {
//         var rowToRemove = document.getElementById(`row_${mid}`);
//         if (rowToRemove) {
//           rowToRemove.remove();

//           var idx = selectedBo.indexOf(id);
//           if (idx !== -1) {
//             selectedBo.splice(idx, 1);
//           }

//           totalBankPayment -= parseInt(bankPayment);
//           tfoot.innerHTML = `
//                 <td colspan="3" class="text-start">Total</td>
//                 <td colspan="2" class="text-end">${to_rupiah(
//                   totalBankPayment
//                 )}</td>
//             `;

//           checkbox.checked = false;

//           // Update data when unchecked
//           handleCheckboxChange(
//             mid,
//             parseInt(bankPayment),
//             false,
//             settlementDate,
//             "tabungan"
//           );
//           updateCombinedTotal();
//         }
//       });

//     // Update data when checked
//     handleCheckboxChange(
//       mid,
//       parseInt(bankPayment),
//       true,
//       settlementDate,
//       "tabungan"
//     );
//     updateCombinedTotal();
//   } else {
//     var idx = selectedBo.indexOf(id);
//     if (idx !== -1) {
//       selectedBo.splice(idx, 1);
//     }

//     totalBankPayment -= parseInt(bankPayment);
//     tfoot.innerHTML = `
//             <td colspan="3" class="text-start">Total</td>
//             <td colspan="2" class="text-end">${to_rupiah(totalBankPayment)}</td>
//         `;

//     var row = document.getElementById(`row_${mid}`);
//     if (row) {
//       row.remove();
//     }

//     // Update data when unchecked
//     handleCheckboxChange(
//       mid,
//       parseInt(bankPayment),
//       false,
//       settlementDate,
//       "tabungan"
//     );
//     updateCombinedTotal();
//   }
// }

// // function checkBank(id, settlementDate, bankType, mid, bankSettlement) {
// // function checkBankold(id, settlementDate, mid, bankSettlement) {
// //   var checkbox = document.getElementById(`checkbox_bank_${id}`);
// //   var tbody = document.querySelector("#bank_selected_items tbody");
// //   var tfoot = document.querySelector("#bank_selected_items tfoot");

// //   if (checkbox.checked) {
// //     // Clear existing rows

// //     selectedBanks.push(id);

// //     var row = document.createElement("tr");
// //     row.setAttribute("id", `bank_detail_${id}`);
// //     row.innerHTML = `
// //             <td>${mid}</td>
// //             <td>${settlementDate}</td>
// //             <td class="text-end">${to_rupiah(parseInt(bankSettlement))}</td>
// //             <td><button id=buton_${id} class="btn btn-danger">x</button></td>
// //         `;
// //     totalBankSettlement = totalBankSettlement + parseInt(bankSettlement);
// //     tbody.appendChild(row);
// //     tfoot.innerHTML = `
// //             <td colspan="2" class="text-start">Total</td>
// //             <td colspan="2" class="text-end">${to_rupiah(
// //               totalBankSettlement
// //             )}</td>
// //         `;
// //   } else {
// //     var idx = selectedBanks.indexOf(id);
// //     if (idx !== -1) {
// //       selectedBanks.splice(idx, 1);
// //     }
// //     totalBankSettlement = totalBankSettlement - parseInt(bankSettlement);
// //     tfoot.innerHTML = "";
// //     tfoot.innerHTML = `
// //             <td colspan="2" class="text-start">Total</td>
// //             <td colspan="2" class="text-end">${to_rupiah(
// //               totalBankSettlement
// //             )}</td>
// //         `;
// //     var row = document.getElementById(`bank_detail_${id}`);
// //     row.remove();
// //   }
// // }

// function oldcheckBank(id, settlementDate, mid, bankSettlement) {
//   var checkbox = document.getElementById(`checkbox_bank_${id}`);
//   var tbody = document.querySelector("#bank_selected_items tbody");
//   var tfoot = document.querySelector("#bank_selected_items tfoot");

//   var settle_head = document.querySelector("#settle_head tbody");

//   if (checkbox.checked) {
//     // Clear existing rows

//     selectedBanks.push(id);

//     var row = document.createElement("tr");
//     row.setAttribute("id", `bank_detail_${id}`);
//     row.innerHTML = `
//               <td>${mid}</td>
//               <td>${settlementDate}</td>
//               <td class="text-end">${to_rupiah(parseInt(bankSettlement))}</td>
//               <td><button id="buton_${id}_${mid}" class="btn btn-danger">x</button></td>
//           `;
//     totalBankSettlement += parseInt(bankSettlement);
//     tbody.appendChild(row);
//     tfoot.innerHTML = `
//               <td colspan="2" class="text-start">Total</td>
//               <td colspan="2" class="text-end">${to_rupiah(
//                 totalBankSettlement
//               )}</td>
//           `;

//     // Add event listener to the button to remove the row
//     document
//       .getElementById(`buton_${id}_${mid}`)
//       .addEventListener("click", function () {
//         var rowToRemove = document.getElementById(`bank_detail_${id}`);
//         if (rowToRemove) {
//           rowToRemove.remove();

//           var idx = selectedBanks.indexOf(id);
//           if (idx !== -1) {
//             selectedBanks.splice(idx, 1);
//           }

//           var indeks = headMid.indexOf(mid);
//           if (indeks !== -1) {
//             headMid.splice(indeks, 1);
//           }

//           totalBankSettlement -= parseInt(bankSettlement);
//           tfoot.innerHTML = `
//               <td colspan="2" class="text-start">Total</td>
//               <td colspan="2" class="text-end">${to_rupiah(
//                 totalBankSettlement
//               )}</td>
//           `;
//           checkbox.checked = false;
//         }
//       });
//   } else {
//     var idx = selectedBanks.indexOf(id);
//     if (idx !== -1) {
//       selectedBanks.splice(idx, 1);
//     }

//     totalBankSettlement -= parseInt(bankSettlement);
//     tfoot.innerHTML = `
//               <td colspan="2" class="text-start">Total</td>
//               <td colspan="2" class="text-end">${to_rupiah(
//                 totalBankSettlement
//               )}</td>
//           `;
//     var row = document.getElementById(`bank_detail_${id}`);
//     if (row) {
//       row.remove();
//     }
//   }
// }

// function checkBank2(id, settlementDate, mid, bankSettlement) {
//   var checkbox = document.getElementById(`checkbox_bank_${id}`);
//   var tbody = document.querySelector("#bank_selected_items tbody");
//   var tfoot = document.querySelector("#bank_selected_items tfoot");

//   if (checkbox.checked) {
//     selectedBanks.push(id);

//     var row = document.getElementById(`row_${mid}`);
//     if (!row) {
//       row = document.createElement("tr");
//       row.setAttribute("id", `row_${mid}`);
//       row.innerHTML = `
//             <td>${mid}</td>
//             <td><ul id="dates_${mid}"></ul></td>
//             <td class="text-end"><ul id="amounts_${mid}"></ul></td>
//             <td class="text-end total" id="total_${mid}">${to_rupiah(
//         bankSettlement
//       )}</td>
//             <td><button id="remove_${mid}" class="btn btn-danger">x</button></td>
//         `;
//       tbody.appendChild(row);
//     } else {
//       document.querySelector(`#total_${mid}`).innerText = to_rupiah(
//         parseInt(
//           document.querySelector(`#total_${mid}`).innerText.replace(/\D/g, "")
//         ) + parseInt(bankSettlement)
//       );
//     }

//     document.querySelector(
//       `#dates_${mid}`
//     ).innerHTML += `<li>${settlementDate}</li>`;
//     document.querySelector(`#amounts_${mid}`).innerHTML += `<li>${to_rupiah(
//       bankSettlement
//     )}</li>`;

//     totalBankSettlement += parseInt(bankSettlement);
//     tfoot.innerHTML = `
//             <td colspan="2" class="text-start">Total</td>
//             <td colspan="2" class="text-end">${to_rupiah(
//               totalBankSettlement
//             )}</td>
//         `;

//     document
//       .getElementById(`remove_${mid}`)
//       .addEventListener("click", function () {
//         var rowToRemove = document.getElementById(`row_${mid}`);
//         if (rowToRemove) {
//           rowToRemove.remove();

//           var idx = selectedBanks.indexOf(id);
//           if (idx !== -1) {
//             selectedBanks.splice(idx, 1);
//           }

//           totalBankSettlement -= parseInt(bankSettlement);
//           tfoot.innerHTML = `
//                 <td colspan="2" class="text-start">Total</td>
//                 <td colspan="2" class="text-end">${to_rupiah(
//                   totalBankSettlement
//                 )}</td>
//             `;

//           checkbox.checked = false;

//           // Update data when unchecked
//           handleCheckboxChange(
//             mid,
//             parseInt(bankSettlement),
//             false,
//             settlementDate,
//             "uang"
//           );
//         }
//       });

//     // Update data when checked
//     handleCheckboxChange(
//       mid,
//       parseInt(bankSettlement),
//       true,
//       settlementDate,
//       "uang"
//     );
//   } else {
//     var idx = selectedBanks.indexOf(id);
//     if (idx !== -1) {
//       selectedBanks.splice(idx, 1);
//     }

//     totalBankSettlement -= parseInt(bankSettlement);
//     tfoot.innerHTML = `
//             <td colspan="2" class="text-start">Total</td>
//             <td colspan="2" class="text-end">${to_rupiah(
//               totalBankSettlement
//             )}</td>
//         `;

//     var row = document.getElementById(`row_${mid}`);
//     if (row) {
//       row.remove();
//     }

//     // Update data when unchecked
//     handleCheckboxChange(
//       mid,
//       parseInt(bankSettlement),
//       false,
//       settlementDate,
//       "uang"
//     );
//   }
// }

// function updateCombinedTotal2() {
//   var combinedTotal = totalBankSettlement + totalBankPayment;
//   var combinedTotalElement = document.getElementById("combined_total");
//   var tfoot = document.querySelector("#dataTable tfoot");

//   if (!combinedTotalElement) {
//     combinedTotalElement = document.createElement("tr");
//     combinedTotalElement.setAttribute("id", "combined_total");
//     // <td colspan="2" class="text-start">Total</td>
//     combinedTotalElement.innerHTML = `
//       <td colspan="2" class="text-end">Bank Settlement : ${to_rupiah(
//         totalBankSettlement
//       )}</td>
//       <td colspan="2" class="text-end">Bank Movement : ${to_rupiah(
//         totalBankPayment
//       )}</td>
//       <td colspan="4" class="text-end">
//       `;
//     tfoot.appendChild(combinedTotalElement);
//   } else {
//     combinedTotalElement.innerHTML = `
//       <td colspan="2" class="text-end">Bank Settlement : ${to_rupiah(
//         totalBankSettlement
//       )}</td>
//       <td colspan="2" class="text-end">Bank Movement : ${to_rupiah(
//         totalBankPayment
//       )}</td>
//       <td colspan="4" class="text-end">
//     `;
//   }
// }

// FUNCTION BUANGAN----------------------------------------------------------------------------------------------------

// Render daftar uang dan tabungan pertama kali
renderMoneyOptions();
renderSavingOptions();

KTUtil.onDOMContentLoaded(function () {
  KTDatatablesServerSide.init();
  KTDatatablesServerSideBO.init();
});
