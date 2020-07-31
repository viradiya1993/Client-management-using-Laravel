<?php if(!empty($featureWithImages)): ?>
    <!-- START Saas Features -->
    <section class="saas-features bg-white overflow-hidden">
        <div class="container">
            <?php $__currentLoopData = $featureWithImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($loop->iteration % 2 == 0): ?>
                    <div class="sp-100 pt-0">
                        <div class="row align-items-center">
                            <div class="col-lg-6 order-lg-1 wow fadeInLeft d-none d-lg-block" data-wow-delay="0.4s">
                                <div class="mock-img">
                                    <img src="<?php echo e($value->image_url); ?>" alt="mockup">
                                </div>
                            </div>
                            <div class="col-lg-6 pl-lg-5 order-lg-2">
                                <h3><?php echo e($value->title); ?></h3>
                                <p><?php echo $value->description; ?></p>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <div class="sp-100">
                        <div class="row align-items-center">
                            <div class="col-lg-6 pr-lg-5">
                                <h3><?php echo e($value->title); ?></h3>
                                <p><?php echo $value->description; ?></p>
                            </div>
                            <div class="col-lg-6 wow fadeInRight d-none d-lg-block" data-wow-delay="0.4s">
                                <div class="mock-img">
                                    <img src="<?php echo e($value->image_url); ?>" alt="mockup">
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>
<?php endif; ?>
<!-- END Saas Features -->
<?php if(!empty($featureWithIcons)): ?>
    <!-- START Features -->
    <section class="features bg-light sp-100-70">
        <div class="container">

            <div class="row">
                <div class="col-12">
                    <div class="sec-title mb-60">
                        <h3><?php echo e($frontDetail->feature_title); ?></h3>
                        <p><?php echo e($frontDetail->feature_description); ?></p>
                    </div>
                </div>
            </div>

            <div class="row">
                <?php $__currentLoopData = $featureWithIcons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $featureWithIcon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-lg-4 col-md-6 col-12 mb-30 wow fadeIn" data-wow-delay="0.4s">
                        <div class="feature-box bg-white shadow br-10 text-center">
                            <div class="icon mx-auto">
                                <i class="<?php echo e($featureWithIcon->icon); ?>"></i>
                            </div>
                            <h5><?php echo e($featureWithIcon->title); ?></h5>
                            <p><?php echo $featureWithIcon->description; ?></p>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>
    <!-- END Features -->
<?php endif; ?>
<!-- START Saas Features -->
<section class="saas-features bg-white overflow-hidden">
    <div class="container">
    </div>
</section>
<!-- END Saas Features -->
<?php /**PATH /Users/froiden/Htdocs/codecanyon/worksuite-saas/resources/views/saas/section/feature.blade.php ENDPATH**/ ?>