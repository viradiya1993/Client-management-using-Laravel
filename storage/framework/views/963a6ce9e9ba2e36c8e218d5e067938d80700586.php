<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">


    <title> <?php echo e(__($pageTitle)); ?> | <?php echo e(ucwords($setting->company_name)); ?></title>

    <!-- Bootstrap CSS -->
    <link type="text/css" rel="stylesheet" media="all" href="<?php echo e(asset('saas/vendor/bootstrap/css/bootstrap.min.css')); ?>">
    <link type="text/css" rel="stylesheet" media="all" href="<?php echo e(asset('saas/vendor/animate-css/animate.min.css')); ?>">
    <link type="text/css" rel="stylesheet" media="all" href="<?php echo e(asset('saas/vendor/slick/slick.css')); ?>">
    <link type="text/css" rel="stylesheet" media="all" href="<?php echo e(asset('saas/vendor/slick/slick-theme.css')); ?>">
    <link type="text/css" rel="stylesheet" media="all" href="<?php echo e(asset('saas/fonts/flaticon/flaticon.css')); ?>">
    <link href="<?php echo e(asset('front/plugin/froiden-helper/helper.css')); ?>" rel="stylesheet">
    <!-- Template CSS -->
    <link type="text/css" rel="stylesheet" media="all" href="<?php echo e(asset('saas/css/main.css')); ?>">
    <!-- Template Font Family  -->
    <link type="text/css" rel="stylesheet" media="all"
          href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900&display=swap">
    <link type="text/css" rel="stylesheet" media="all"
          href="<?php echo e(asset('saas/vendor/material-design-iconic-font/css/material-design-iconic-font.min.css')); ?>">

    <script src="https://www.google.com/recaptcha/api.js"></script>
    <style>
        :root {
            --main-color: <?php echo e($frontDetail->primary_color); ?>;
        }
        .help-block {
            color: #8a1f11 !important;
        }

    </style>
</head>

<body id="home">


<!-- Topbar -->
<?php echo $__env->make('sections.saas.saas_header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<!-- END Topbar -->

<!-- Header -->
<!-- END Header -->


<section class="sp-100 login-section" id="section-contact">
    <div class="container">
        <div class="login-box mt-5 shadow bg-white form-section">
            <h4 class="mb-0">
                <?php echo app('translator')->get('app.signup'); ?>
            </h4>
            <?php echo Form::open(['id'=>'register', 'method'=>'POST']); ?>

            <div class="row">
                <div id="alert" class="col-lg-12 col-12">

                </div>
                <div class="col-12" id="form-box">
                    <div class="form-group mb-4">
                        <label for="company_name"><?php echo e(__('modules.client.companyName')); ?></label>
                        <input type="text" name="company_name" id="company_name" placeholder="<?php echo e(__('modules.client.companyName')); ?>" class="form-control">
                    </div>
                    <div class="form-group mb-4">
                        <label for="email"><?php echo e(__('app.yourEmailAddress')); ?></label>
                        <input type="email" name="email" id="email" placeholder="<?php echo e(__('app.yourEmailAddress')); ?>" class="form-control">
                    </div>
                    <div class="form-group mb-4">
                        <label for="password"><?php echo e(__('modules.client.password')); ?></label>
                        <input type="password" class="form-control " id="password" name="password" placeholder="<?php echo e(__('modules.client.password')); ?>">
                    </div>
                    <div class="form-group mb-4">
                        <label for="password_confirmation"><?php echo e(__('app.confirmPassword')); ?></label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="<?php echo e(__('app.confirmPassword')); ?>">
                    </div>
                    <?php if(!is_null($global->google_recaptcha_key)): ?>
                        <div class="form-group mb-4">
                            <div class="g-recaptcha" data-sitekey="<?php echo e($global->google_recaptcha_key); ?>"></div>
                        </div>
                    <?php endif; ?>
                    <button type="button" class="btn btn-lg btn-custom mt-2" id="save-form">
                        <?php echo app('translator')->get('app.signup'); ?>
                    </button>
                </div>
            </div>
            <?php echo Form::close(); ?>

        </div>
    </div>
</section>

<!-- END Main container -->

<!-- Cta -->

<!-- End Cta -->

<!-- Footer -->
<?php echo $__env->make('sections.saas.saas_footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<!-- END Footer -->



<!-- Scripts -->
<script src="<?php echo e(asset('saas/vendor/jquery/jquery.min.js')); ?>"></script>
<script src="<?php echo e(asset('saas/vendor/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>
<script src="<?php echo e(asset('saas/vendor/slick/slick.min.js')); ?>"></script>
<script src="<?php echo e(asset('saas/vendor/wowjs/wow.min.js')); ?>"></script>
<script src="<?php echo e(asset('front/plugin/froiden-helper/helper.js')); ?>"></script>
<script src="<?php echo e(asset('saas/js/main.js')); ?>"></script>
<script src="<?php echo e(asset('front/plugin/froiden-helper/helper.js')); ?>"></script>
<!-- Global Required JS -->

<script>
    $('#save-form').click(function () {


        $.easyAjax({
            url: '<?php echo e(route('front.signup.store')); ?>',
            container: '.form-section',
            type: "POST",
            data: $('#register').serialize(),
            messagePosition: "inline",
            success: function (response) {
                if (response.status == 'success') {
                    $('#form-box').remove();
                } else if (response.status == 'fail') {
                    <?php if(!is_null($global->google_recaptcha_key)): ?>
                    grecaptcha.reset();
                    <?php endif; ?>

                }
            }
        })
    });
</script>

</body>
</html>
<?php /**PATH /var/www/html/ClientManagment/ClientManagment/codecanyon-23263417-worksuite-saas-project-management-system/resources/views/saas/register.blade.php ENDPATH**/ ?>