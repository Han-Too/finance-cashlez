<div id="kt_aside" class="aside aside-dark aside-hoverable" data-kt-drawer="true" data-kt-drawer-name="aside"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
    data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start"
    data-kt-drawer-toggle="#kt_aside_mobile_toggle">
    <!--begin::Brand-->
    <?php
        $priv = App\Helpers\Utils::getPrivilege('reconcile/result');
        $privDis = App\Helpers\Utils::getPrivilege('disbursement');
    ?>
    <div class="aside-logo flex-column-auto" id="kt_aside_logo">
        <!--begin::Logo-->
        <a href="/">
            <img alt="Logo" src="<?php echo e(asset('cztemp/assets/media/logo-dark.png')); ?>" class="h-60px logo" />
        </a>
        <!--end::Logo-->
        <!--begin::Aside toggler-->
        <div id="kt_aside_toggle" class="btn btn-icon w-auto px-0 btn-active-color-primary aside-toggle"
            data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
            data-kt-toggle-name="aside-minimize">
            <!--begin::Svg Icon | path: icons/duotune/arrows/arr074.svg-->
            <span class="svg-icon svg-icon-1 rotate-180">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none">
                    <path
                        d="M11.2657 11.4343L15.45 7.25C15.8642 6.83579 15.8642 6.16421 15.45 5.75C15.0358 5.33579 14.3642 5.33579 13.95 5.75L8.40712 11.2929C8.01659 11.6834 8.01659 12.3166 8.40712 12.7071L13.95 18.25C14.3642 18.6642 15.0358 18.6642 15.45 18.25C15.8642 17.8358 15.8642 17.1642 15.45 16.75L11.2657 12.5657C10.9533 12.2533 10.9533 11.7467 11.2657 11.4343Z"
                        fill="black" />
                </svg>
            </span>
            <!--end::Svg Icon-->
        </div>
        <!--end::Aside toggler-->
    </div>
    <!--end::Brand-->
    <!--begin::Aside menu-->
    <div class="aside-menu flex-column-fluid">
        <!--begin::Aside Menu-->
        <div class="hover-scroll-overlay-y my-2 py-5 py-lg-8" id="kt_aside_menu_wrapper" data-kt-scroll="true"
            data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto"
            data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu"
            data-kt-scroll-offset="0">
            <!--begin::Menu-->
            <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500"
                id="#kt_aside_menu" data-kt-menu="true">
                <div class="menu-item">
                    <a class="menu-link <?php echo e(request()->segment(1) == '' ? 'active' : ''); ?>" href="<?php echo e(url('')); ?>">
                        <span class="menu-icon">
                            <i class="bi bi-grid fs-3"></i>
                        </span>
                        <span class="menu-title">Dashboard</span>
                    </a>
                </div>
                <?php if(Auth::user()->role == 1): ?>
                    <div class="menu-item">
                        <div class="menu-content pb-2">
                            <span class="menu-section text-muted text-uppercase fs-8 ls-1">User</span>
                        </div>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link <?php echo e(Str::startsWith(request()->path(), 'users') ? 'active' : ''); ?>"
                            href="<?php echo e(url('users')); ?>">
                            <span class="menu-icon">
                                <i class="bi bi-person fs-2"></i>
                            </span>
                            <span class="menu-title">User List</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link <?php echo e(Str::startsWith(request()->path(), 'roles') ? 'active' : ''); ?>"
                            href="<?php echo e(url('roles')); ?>">
                            <span class="menu-icon">
                                <i class="bi bi-window fs-3"></i>
                            </span>
                            <span class="menu-title">Role List</span>
                        </a>
                    </div>
                    
                    <div class="menu-item">
                        <div class="menu-content pt-8 pb-2">
                            <span class="menu-section text-muted text-uppercase fs-8 ls-1">Master Data</span>
                        </div>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link <?php echo e(Str::startsWith(request()->path(), 'banks') ? 'active' : ''); ?>"
                            href="<?php echo e(url('banks')); ?>">
                            <span class="menu-icon">
                                <i class="bi bi-layers fs-3"></i>
                            </span>
                            <span class="menu-title">Channel</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link <?php echo e(Str::startsWith(request()->path(), 'parameters') ? 'active' : ''); ?>"
                            href="<?php echo e(url('parameters')); ?>">
                            <span class="menu-icon">
                                <i class="bi bi-layers fs-3"></i>
                            </span>
                            <span class="menu-title">Parameter</span>
                        </a>
                    </div>
                <?php endif; ?>
                <div class="menu-item">
                    <div class="menu-content pt-8 pb-2">
                        <span class="menu-section text-muted text-uppercase fs-8 ls-1">Settlement</span>
                    </div>
                </div>
                <div class="menu-item">
                    <a class="menu-link <?php echo e(Str::startsWith(request()->path(), 'settlement') ? 'active' : ''); ?>"
                        href="<?php echo e(url('settlement')); ?>">
                        <span class="menu-icon">
                            <i class="bi bi-archive fs-3"></i>
                        </span>
                        <span class="menu-title">Upload Bank Settlement</span>
                    </a>
                </div>
                <div data-kt-menu-trigger="click"
                    class="menu-item <?php echo e(Str::startsWith(request()->path(), 'reconcile') ? 'here show' : ''); ?> menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="bi bi-patch-check fs-3"></i>
                        </span>
                        <span class="menu-title">Reconciliation</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link <?php echo e(request()->is('reconcile-list') ? 'active' : ''); ?>"
                                href="<?php echo e(url('reconcile-list')); ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Reconcile List</span>
                            </a>
                        </div>
                        
                        

                        <?php if($priv->read): ?>
                            <div class="menu-item">
                                <a class="menu-link <?php echo e(request()->is('reconcile/result') ? 'active' : ''); ?>"
                                    href="<?php echo e(url('reconcile/result')); ?>">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Result</span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
            </div>
            <!--end::Menu-->
        </div>
    </div>
    <!--end::Aside menu-->
</div>
<?php /**PATH C:\laragon\www\finance-server\resources\views/partials/sidebar.blade.php ENDPATH**/ ?>