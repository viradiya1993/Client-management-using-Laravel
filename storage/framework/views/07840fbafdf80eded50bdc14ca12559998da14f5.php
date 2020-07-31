<?php $__env->startSection('content'); ?>

    <!--
        |‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒`‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
        | Features
        |‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
        !-->

    <?php echo $__env->make('saas.section.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->make('cookieConsent::index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->make('saas.section.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->make('saas.section.feature', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->make('saas.section.testimonial', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>



<?php $__env->stopSection(); ?>
<?php $__env->startPush('footer-script'); ?>
    <script>
        var maxHeight = -1;
        $(document).ready(function() {


            var promise1 = new Promise(function (resolve, reject) {

                $('.planNameHead').each(function () {
                    maxHeight = maxHeight > $(this).height() ? maxHeight : $(this).height();
                });
                resolve(maxHeight);
            }).then(function (maxHeight) {
                // console.log(maxHeight);
                $('.planNameHead').each(function () {
                    $(this).height(Math.round(maxHeight));
                });
                $('.planNameTitle').each(function () {
                    $(this).height(Math.round(maxHeight - 28));
                });

            });
        });
        function planShow(type){
            if(type == 'monthly'){
                $('#monthlyPlan').show();
                $('#annualPlan').hide();
            }
            else{
                $('#monthlyPlan').hide();
                $('#annualPlan').show();
            }
        }
    </script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sass-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/froiden/Htdocs/codecanyon/worksuite-saas/resources/views/saas/home.blade.php ENDPATH**/ ?>