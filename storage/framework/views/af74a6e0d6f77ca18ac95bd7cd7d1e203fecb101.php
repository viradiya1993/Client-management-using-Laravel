<?php $__env->startSection('title', trans('installer_messages.welcome.title')); ?>
<?php $__env->startSection('container'); ?>
    <p class="paragraph" style="text-align: center;"><?php echo e(trans('installer_messages.welcome.message')); ?></p>
    <div class="buttons">
        <a href="<?php echo e(route('LaravelInstaller::environment')); ?>" class="button"><?php echo e(trans('installer_messages.next')); ?></a>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('vendor.installer.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/ClientManagment/ClientManagment/codecanyon-23263417-worksuite-saas-project-management-system/resources/views/vendor/installer/welcome.blade.php ENDPATH**/ ?>