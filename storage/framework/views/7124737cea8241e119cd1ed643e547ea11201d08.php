<div class="clients bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 mb-30 text-center">
                <p class="c-blue mb-2"><?php echo e($frontDetail->client_title); ?></p>
                <h4> <?php echo e($frontDetail->client_detail); ?></h4>

            </div>
            <div class="col-12">
                <div class="client-slider" id="client-slider">
                    <?php $__currentLoopData = $frontClients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $frontClient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="client-img">
                            <div class="img-holder">
                                <img src="<?php echo e($frontClient->image_url); ?>" alt="partner">
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /var/www/html/ClientManagment/ClientManagment/codecanyon-23263417-worksuite-saas-project-management-system/resources/views/saas/section/client.blade.php ENDPATH**/ ?>