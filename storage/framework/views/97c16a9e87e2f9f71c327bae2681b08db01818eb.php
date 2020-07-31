<footer class="bg-white footer">
    <div class="container">
        <div class="footer-top border-bottom">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-12 mb-30">
                    <div class="f-contact-detail">
                        <i class="flaticon-email"></i>
                        <h5><?php echo app('translator')->get('app.email'); ?></h5>
                        <p class="mb-0"><?php echo e($frontDetail->email); ?></p>
                    </div>
                </div>
                <?php if($frontDetail->phone): ?>
                    <div class="col-lg-4 col-md-6 col-12 mb-30">
                        <div class="f-contact-detail">
                            <i class="flaticon-call"></i>
                            <h5><?php echo app('translator')->get('app.phone'); ?></h5>
                            <p class="mb-0"><?php echo e($frontDetail->phone); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="col-lg-4 col-md-6 col-12 mb-30">
                    <div class="f-contact-detail">
                        <i class="flaticon-placeholder"></i>
                        <h5><?php echo app('translator')->get('app.address'); ?></h5>
                        <p class="mb-0"><?php echo e($frontDetail->address); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright py-4">
            <div class="row d-flex align-items-center justify-content-between">
                <div class="col-lg-4 col-md-6">
                    <p class="mb-0"><?php echo e(ucwords($frontDetail->footer_copyright_text)); ?> </p>
                </div>
                <div class="col-lg-4 col-md-6 text-center">
                    <div class="col-12 col-lg-6">
                        <?php $routeName = request()->route()->getName(); ?>
                        <ul class="nav nav-primary nav-hero">
                            <?php $__empty_1 = true; $__currentLoopData = $footerSettings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $footerSetting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo e(route('front.page', $footerSetting->slug)); ?>" ><?php echo e($footerSetting->name); ?></a>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-12 d-md-flex align-items-center">
                    <div class="form-group d-inline-block mr-20 my-2">
                        <select class="form-control" onchange="location = this.value;">
                            <option value="<?php echo e(route('front.language.lang', 'en')); ?>" <?php if($locale == 'en'): ?> selected <?php endif; ?>>English </option>
                            <?php $__empty_1 = true; $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <option value="<?php echo e(route('front.language.lang', $language->language_code)); ?>"  <?php if($locale == $language->language_code): ?> selected <?php endif; ?>><?php echo e($language->language_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="socials text-right">
                        <?php if($frontDetail->social_links): ?>
                            <?php $__empty_1 = true; $__currentLoopData = json_decode($frontDetail->social_links,true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php if(strlen($link['link']) > 0): ?>
                                    <a href="<?php echo e($link['link']); ?>" target="_blank">
                                        <i class="zmdi zmdi-<?php echo e($link['name']); ?>"></i>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer><?php /**PATH /Users/froiden/Htdocs/codecanyon/worksuite-saas/resources/views/sections/saas/saas_footer.blade.php ENDPATH**/ ?>