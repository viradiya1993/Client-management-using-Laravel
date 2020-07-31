<section class="section-hero">

    <div class="banner position-relative">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 col-12 text-lg-left text-center">
                    <div class="banner-text mr-0 mr-lg-5">
                        <h3 class="mb-3 mb-md-4 font-weight-bold">  <?php echo e($frontDetail->header_title); ?></h3>
                        <p>
                            <?php echo e($frontDetail->header_description); ?></p>
                        <?php if($frontDetail->get_started_show == 'yes'): ?>
                            <a href="<?php echo e(route('front.signup.index')); ?>"
                               class="btn btn-lg btn-custom mt-4 btn-outline"><?php echo e($frontMenu->get_start); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-6 col-12 d-none d-lg-block wow zoomIn" data-wow-delay="0.4s">
                    <div class="banner-img shadow">
                        <img src="<?php echo e($frontDetail->image_url); ?>" alt="business" class="shadow">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php /**PATH /var/www/html/ClientManagment/ClientManagment/codecanyon-23263417-worksuite-saas-project-management-system/resources/views/saas/section/header.blade.php ENDPATH**/ ?>