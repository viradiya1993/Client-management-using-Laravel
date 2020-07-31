<?php $__env->startSection('title', trans('installer_messages.permissions.title')); ?>
<?php $__env->startSection('container'); ?>
    <?php if(isset($permissions['errors'])): ?>
        <div class="alert alert-danger">Please fix the below error and the click   <?php echo e(trans('installer_messages.checkPermissionAgain')); ?></div>
    <?php endif; ?>
    <ul class="list">
        <?php $__currentLoopData = $permissions['permissions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="list__item list__item--permissions <?php echo e($permission['isSet'] ? 'success' : 'error'); ?>">
                <?php echo e(strtolower($permission['folder'])); ?><span><?php echo e($permission['permission']); ?></span>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </ul>

    <p style="background: #f7f7f9;padding: 10px;font-size:14px">
        chmod -R 775 storage/app/ storage/framework/ storage/logs/ bootstrap/cache/
    </p>


    <div class="buttons">

        <?php if( ! isset($permissions['errors'])): ?>
            <a class="button" href="<?php echo e(route('LaravelInstaller::database')); ?>">
                <?php echo e(trans('installer_messages.next')); ?>

            </a>
        <?php else: ?>

            <a class="button" href="javascript:window.location.href='';">
                <?php echo e(trans('installer_messages.checkPermissionAgain')); ?>

            </a>
        <?php endif; ?>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('vendor.installer.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/ClientManagment/ClientManagment/codecanyon-23263417-worksuite-saas-project-management-system/resources/views/vendor/installer/permissions.blade.php ENDPATH**/ ?>