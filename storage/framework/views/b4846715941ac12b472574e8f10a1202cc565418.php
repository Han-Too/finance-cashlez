<?php if (isset($component)) { $__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da = $component; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AppLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

    <div class="container">
        <div class="card card-flush px-10 py-6 rounded-sm">
            <div class="card-title">
                <h2 class="fw-bolder">Edit role</h2>
            </div>
            <form class="form"  id="update_role_form" >
                <?php echo csrf_field(); ?>
                <input type="hidden" id="nameold" name="nameold" value="<?php echo e($data->name); ?>">
                <div class="py-10 px-lg-17">
                    <div class="scroll-y me-n7 pe-7">
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-bold mb-2">Role Name</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" class="form-control form-control-solid" id="named"
                                placeholder="Place role's title" name="name" value="<?php echo e($data->name); ?>" required />
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

                            <?php
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
                            ?>

                            <?php $__currentLoopData = $groupedPermissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $header => $permissions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($header == 'param'): ?>
                                    <h5 class="mb-3">Parameter</h5>
                                <?php elseif($header == 'bs'): ?>
                                    <h5 class="mb-3">Bank Settlement</h5>
                                <?php elseif($header == 'reconlist'): ?>
                                    <h5 class="mb-3">Reconcile List</h5>
                                <?php elseif($header == 'disburslist'): ?>
                                    <h5 class="mb-3">Disbursement List</h5>
                                <?php elseif($header == 'unmatchlist'): ?>
                                    <h5 class="mb-3">Unmatch List</h5>
                                <?php else: ?>
                                    <h5 class="mb-3"><?php echo e(ucwords($header)); ?></h5>
                                <?php endif; ?>

                                <div class="row">
                                    <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $permis): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="col-6">
                                            <div class="form-check mb-5">
                                                <input class="form-check-input" type="checkbox" name="permissions[]"
                                                    id="<?php echo e($permis->id); ?>" value="<?php echo e($permis->id); ?>"
                                                    <?php echo e(in_array($permis->id, $data->permission->pluck('id')->toArray()) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="<?php echo e($permis->id); ?>">
                                                    <?php echo e(ucwords(getFirstPart($permis->name))); ?>

                                                </label>
                                            </div>
                                        </div>

                                        <?php if(($key + 1) % 2 === 0): ?>
                                </div>
                                <div class="row">
                            <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

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

    <?php $__env->startSection('scripts'); ?>
        

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
            $("#update_role_form").on("submit", function(event) {
                event.preventDefault();
                var token = $('meta[name="csrf-token"]').attr('content');
                var named = document.getElementById("named").value;
                var nameold = document.getElementById("nameold").value;
                var formData = new FormData(this);

                formData.append("permission", selectedPermissions);
                formData.append("named", named);
                formData.append("id", nameold);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    type: 'POST',
                    data: formData,
                    url: `${baseUrl}/roles/update`,
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
    <?php $__env->stopSection(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da)): ?>
<?php $component = $__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da; ?>
<?php unset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\finance-server\resources\views/modules/roles/role/edit.blade.php ENDPATH**/ ?>