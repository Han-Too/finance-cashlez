<?php echo $__env->yieldPushContent('script'); ?>;
<script>
    var hostUrl = `<?php echo e(asset('cztemp/assets/')); ?>`
</script>

<script>
    var baseUrl = `<?php echo e(url('/')); ?>`
    // var baseUrl2 = window.location.origin;
</script>

<script src="<?php echo e(asset('cztemp/assets/plugins/global/plugins.bundle.js')); ?>"></script>
<script src="<?php echo e(asset('cztemp/assets/js/scripts.bundle.js')); ?>"></script>

<script src="<?php echo e(asset('cztemp/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js')); ?>"></script>
<script src="<?php echo e(asset('cztemp/assets/plugins/custom/datatables/datatables.bundle.js')); ?>"></script>
<script src="<?php echo e(asset('cztemp/assets/js/custom/widgets.js')); ?>"></script>

<script src="<?php echo e(asset('cztemp/assets/custom/js/utils.js')); ?>"></script>

<?php echo $__env->yieldContent('scripts'); ?>;<?php /**PATH C:\laragon\www\finance-server\resources\views/partials/scripts.blade.php ENDPATH**/ ?>