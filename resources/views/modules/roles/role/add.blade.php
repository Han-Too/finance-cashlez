<x-app-layout>

    <div class="container">
        <div class="card card-flush px-10 py-6 rounded-sm">
            <div class="card-title">
                <h2 class="fw-bolder">Add role</h2>
            </div>
            <form class="form" {{-- action="/roles/update"  --}} id="add_role_form" {{-- method="post" --}}>
                @csrf
                <div class="py-10 px-lg-17">
                    <div class="scroll-y me-n7 pe-7">
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-bold mb-2">Role Name</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" class="form-control form-control-solid" id="named"
                                placeholder="Place role's name" name="name" required />
                            <!--end::Input-->
                        </div>
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-bold mb-4">Role Permission</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <style>
                                .checkbox-grid {
                                    display: grid;
                                    grid-template-columns: repeat(2, 1fr);
                                    /* Dua kolom dengan lebar yang sama */
                                    gap: 10px;
                                    /* Jarak antar checkbox */
                                }
                            </style>

                            @php
                                // Mengelompokkan permission berdasarkan kata kedua dari value->name
                                $groupedPermissions = [];
                                foreach ($permission as $permis) {
                                    $parts = explode('-', $permis->name);
                                    $header = isset($parts[1]) ? trim($parts[1]) : 'Uncategorized'; // Header diambil dari kata kedua, jika ada

                                    // Menambahkan permission ke kelompok berdasarkan header
                                    if (!isset($groupedPermissions[$header])) {
                                        $groupedPermissions[$header] = []; // Inisialisasi array untuk header
                                    }
                                    $groupedPermissions[$header][] = $permis; // Tambahkan permission ke dalam header
                                }

                                function getFirstPart($string)
                                {
                                    // Memisahkan string berdasarkan tanda strip
                                    $parts = explode('-', $string);

                                    // Mengembalikan kata pertama (jika ada)
                                    return isset($parts[0]) ? trim($parts[0]) : null;
                                }
                            @endphp

                            @foreach ($groupedPermissions as $header => $permissions)
                                @if ($header == 'param')
                                    <h5 class="mb-3">Parameter</h5>
                                @elseif ($header == 'bs')
                                    <h5 class="mb-3">Bank Settlement</h5>
                                @elseif ($header == 'reconlist')
                                    <h5 class="mb-3">Reconcile List</h5>
                                @elseif ($header == 'disburslist')
                                    <h5 class="mb-3">Disbursement List</h5>
                                @elseif ($header == 'unmatchlist')
                                    <h5 class="mb-3">Unmatch List</h5>
                                @else
                                    <h5 class="mb-3">{{ ucwords($header) }}</h5>
                                @endif

                                <div class="row">
                                    @foreach ($permissions as $key => $permis)
                                        <div class="col-6">
                                            <div class="form-check mb-5">
                                                <input class="form-check-input" type="checkbox" name="permissions[]"
                                                    id="{{ $permis->id }}" value="{{ $permis->id }}">
                                                <label class="form-check-label" for="{{ $permis->id }}">
                                                    {{ ucwords(getFirstPart($permis->name)) }}
                                                </label>
                                            </div>
                                        </div>

                                        @if (($key + 1) % 2 === 0)
                                </div>
                                <div class="row">
                            @endif
                            @endforeach
                        </div>
                        @endforeach

                        <!--end::Input-->
                    </div>
                    <!--end::Input group-->
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary rounded-sm mx-3">Submit</button>
                    <a href="/roles" class="btn btn-light mx-3 rounded-sm">Back</a>
                </div>
        </div>
        </form>
    </div>
    </div>

    @section('scripts')
        {{-- <script src="{{ asset('cztemp/assets/custom/js/role.js') }}"></script> --}}

        <script>
            // Inisialisasi array untuk menyimpan id checkbox yang checked
            let selectedPermissions = [];

            // Function untuk menangani perubahan status checkbox
            function handleCheckboxChange(checkbox) {
                const id = checkbox.value; // Mendapatkan id dari checkbox

                if (checkbox.checked) {
                    // Jika checkbox dicentang, tambahkan id ke array
                    if (!selectedPermissions.includes(id)) {
                        selectedPermissions.push(id);
                    }
                } else {
                    // Jika checkbox tidak dicentang, hapus id dari array
                    const index = selectedPermissions.indexOf(id);
                    if (index > -1) {
                        selectedPermissions.splice(index, 1);
                    }
                }

                // console.log(selectedPermissions); // Untuk debugging, menampilkan array yang berisi id yang dipilih
            }

            // Mendaftarkan event listener pada semua checkbox
            document.querySelectorAll('input[name="permissions[]"]').forEach(function(checkbox) {
                // Ketika checkbox diubah, panggil handleCheckboxChange
                checkbox.addEventListener('change', function() {
                    handleCheckboxChange(this);
                });

                // Jika sudah dicentang saat halaman dimuat, tambahkan id ke array
                if (checkbox.checked) {
                    selectedPermissions.push(checkbox.value);
                }
            });
        </script>

        <script>
            $("#add_role_form").on("submit", function(event) {
                event.preventDefault();
                var token = $('meta[name="csrf-token"]').attr('content');
                var named = document.getElementById("named").value;
                var formData = new FormData(this);

                formData.append("permission", selectedPermissions);
                formData.append("named", named);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    type: 'POST',
                    data: formData,
                    url: `${baseUrl}/roles/store`,
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
                                location.href = baseUrl + "/roles";
                            });
                        } else {
                            swal.fire({
                                text: data.message,
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
        </script>
    @endsection
</x-app-layout>
