"use strict";

var KTDatatablesServerSide2 = (function () {
  var dt;

  var initDatatable = function () {
    dt = $("#kt_datatable_permission").DataTable({
      searchDelay: 200,
      processing: true,
      serverSide: true,
      // order: [[1, "desc"]],
      stateSave: true,
      select: {
        style: "os",
        selector: "td:first-child",
        className: "row-selected",
      },
      ajax: {
        url: baseUrl + "/permission/data",
      },
      columnDefs: [
        {
          targets: 0,
          orderable: true,
          className: "text-start",
          width: "50px",
          render: function (data, type, row, meta) {
            // console.log(row);
            return meta.row + 1;
          },
        },
        {
          targets: 1,
          orderable: true,
          className: "text-start",
          render: function (data, type, row) {
            return `<p class="fw-bolder fs-5">${row.name}</p>`;
          },
        },
        {
          targets: -1,
          orderable: false,
          className: "text-end",
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
                                    <a href="javascript:void()" onclick="editRowed('${row.id}')" class="menu-link px-3" data-kt-docs-table-filter="edit_row">
                                        Edit
                                    </a>
                                </div>
                                <!--end::Menu item-->

                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <a href="javascript:void()" onclick="deleteRowed('${row.id}')" class="menu-link px-3" data-kt-docs-table-filter="delete_row">
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
        $(row).find("td:eq(0)").attr("data-filter", row.name);
      },
    });

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

function deleteRowed($id) {
    if (!$id) {
        console.error("ID is empty.");
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
                url: baseUrl + "/permission/destroy/" + $id,
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


function editRowed($id) {
  $.ajax({
    url: baseUrl + "/permission/get/" + $id,
    type: "GET",
    success: function (response) {
      console.log(response);
      const editModal = new bootstrap.Modal(
        document.getElementById("editModal")
      );
      editModal.show();
      let newname = (document.getElementById("namaInput").value =
        response.name);
      let idvar = (document.getElementById("id").value = response.id);
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

$("#edit_permis_form").on("submit", function (event) {
  event.preventDefault();
  var token = $('meta[name="csrf-token"]').attr("content");
  const id = document.getElementById("id").value; // Ambil ID dari input hidden
  const named = document.getElementById("namaInput").value; // Ambil nama dari input

  if (named.includes(" ")) {
    swal.fire({
      html: "Tidak Boleh Mengandung Spasi! <br> Gunakan Tanda Strip ( - )",
      icon: "error",
      buttonsStyling: false,
      confirmButtonText: "Ok, got it!",
      customClass: {
        confirmButton: "btn font-weight-bold btn-light-primary",
      },
    });
  } else if (named.includes("_")) {
    swal.fire({
      html: "Tidak Boleh Mengandung Simbol _! <br> Gunakan Tanda Strip ( - )",
      icon: "error",
      buttonsStyling: false,
      confirmButtonText: "Ok, got it!",
      customClass: {
        confirmButton: "btn font-weight-bold btn-light-primary",
      },
    });
  } else if (named.includes("/")) {
    swal.fire({
      html: "Tidak Boleh Mengandung Simbol / ! <br> Gunakan Tanda Strip ( - )",
      icon: "error",
      buttonsStyling: false,
      confirmButtonText: "Ok, got it!",
      customClass: {
        confirmButton: "btn font-weight-bold btn-light-primary",
      },
    });
  } else {
    var formData = new FormData(this);

    formData.append("id", id);
    formData.append("named", named);

    $.ajax({
      headers: {
        "X-CSRF-TOKEN": token,
      },
      type: "POST",
      data: formData,
      url: `${baseUrl}/permission/update`,
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
              location.href = baseUrl + "/roles";
            });
        } else {
          swal
            .fire({
              text: data.message,
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
    });
  }
});

$("#add_permis_form").on("submit", function (event) {
  event.preventDefault();
  var token = $('meta[name="csrf-token"]').attr("content");
  var named = document.getElementById("permisnamed").value;

  if (named.includes(" ")) {
    swal.fire({
      html: "Tidak Boleh Mengandung Spasi! <br> Gunakan Tanda Strip ( - )",
      icon: "error",
      buttonsStyling: false,
      confirmButtonText: "Ok, got it!",
      customClass: {
        confirmButton: "btn font-weight-bold btn-light-primary",
      },
    });
  } else if (named.includes("_")) {
    swal.fire({
      html: "Tidak Boleh Mengandung Simbol _! <br> Gunakan Tanda Strip ( - )",
      icon: "error",
      buttonsStyling: false,
      confirmButtonText: "Ok, got it!",
      customClass: {
        confirmButton: "btn font-weight-bold btn-light-primary",
      },
    });
  } else if (named.includes("/")) {
    swal.fire({
      html: "Tidak Boleh Mengandung Simbol / ! <br> Gunakan Tanda Strip ( - )",
      icon: "error",
      buttonsStyling: false,
      confirmButtonText: "Ok, got it!",
      customClass: {
        confirmButton: "btn font-weight-bold btn-light-primary",
      },
    });
  } else {
    var formData = new FormData(this);

    formData.append("named", named);

    $.ajax({
      headers: {
        "X-CSRF-TOKEN": token,
      },
      type: "POST",
      data: formData,
      url: `${baseUrl}/permission/store`,
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
              location.href = baseUrl + "/roles";
            });
        } else {
          swal
            .fire({
              text: data.message,
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
    });
  }
});

KTUtil.onDOMContentLoaded(function () {
  KTDatatablesServerSide2.init();
});
