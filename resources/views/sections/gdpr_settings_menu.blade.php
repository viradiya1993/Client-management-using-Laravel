@section('other-section')
<ul class="nav tabs-vertical">
    <li class="tab">
        <a href="{{ route('admin.settings.index') }}" class="text-danger"><i class="ti-arrow-left"></i> Back</a></li>
    <li class="tab @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.gdpr.index') active @endif">
        <a href="{{ route('admin.gdpr.index') }}">General</a></li>

    <li class="tab @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.gdpr.right-to-data-portability') active @endif">
        <a href="{{ route('admin.gdpr.right-to-data-portability') }}">Right to data portability</a></li>
    <li class="tab @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.gdpr.right-to-erasure') active @endif">
        <a href="{{ route('admin.gdpr.right-to-erasure') }}">Right to Erasure</a></li>
    <li class="tab @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.gdpr.right-to-informed') active @endif">
        <a href="{{ route('admin.gdpr.right-to-informed') }}">Right to be informed</a></li>

    <li class="tab @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.gdpr.right-of-access') active @endif">
        <a href="{{ route('admin.gdpr.right-of-access') }}">Right of access/Right to rectification</a></li>

    <li class="tab @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'admin.gdpr.consent') active @endif">
        <a href="{{ route('admin.gdpr.consent') }}">Consent</a></li>
</ul>

<script src="{{ asset('plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
<script>
    var screenWidth = $(window).width();
    if(screenWidth <= 768){

        $('.tabs-vertical').each(function() {
            var list = $(this), select = $(document.createElement('select')).insertBefore($(this).hide()).addClass('settings_dropdown form-control');

            $('>li a', this).each(function() {
                var target = $(this).attr('target'),
                    option = $(document.createElement('option'))
                        .appendTo(select)
                        .val(this.href)
                        .html($(this).html())
                        .click(function(){
                            if(target==='_blank') {
                                window.open($(this).val());
                            }
                            else {
                                window.location.href = $(this).val();
                            }
                        });

                if(window.location.href == option.val()){
                    option.attr('selected', 'selected');
                }
            });
            list.remove();
        });

        $('.settings_dropdown').change(function () {
            window.location.href = $(this).val();
        })

    }
</script>
@endsection