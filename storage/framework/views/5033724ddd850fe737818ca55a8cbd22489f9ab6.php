<section class="bg-white sp-100">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="sec-title mb-5">
                    <h3><?php echo e($frontDetail->testimonial_title); ?></h3>
                </div>
            </div>
        </div>
        <div id="testimonial-slider" class="testimonial-slider mb-0 text-center">
            <?php $__empty_1 = true; $__currentLoopData = $testimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $testimonial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="testimonial-item">
                    <div class="client-info">
                        <p class="mb-4"><?php echo e(ucfirst($testimonial->comment)); ?></p>
                        <h5 class="mb-1"><?php echo e(ucwords($testimonial->name)); ?></h5>
                    </div>
                    <div class="rating text-warning">
                        <i class="zmdi zmdi-star "></i>
                        <i class="zmdi <?php if($testimonial->rating < 2): ?>zmdi-star-border <?php else: ?> zmdi-star <?php endif; ?> "></i>
                        <i class="zmdi  <?php if($testimonial->rating < 3): ?>zmdi-star-border <?php else: ?> zmdi-star <?php endif; ?>"></i>
                        <i class="zmdi  <?php if($testimonial->rating < 4): ?>zmdi-star-border <?php else: ?> zmdi-star <?php endif; ?>"></i>
                        <i class="zmdi  <?php if($testimonial->rating < 5): ?> zmdi-star-border <?php else: ?> zmdi-star <?php endif; ?>"></i>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <?php endif; ?>
        </div>
    </div>
</section><?php /**PATH /Users/froiden/Htdocs/codecanyon/worksuite-saas/resources/views/saas/section/testimonial.blade.php ENDPATH**/ ?>