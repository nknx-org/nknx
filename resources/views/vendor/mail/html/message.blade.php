@component('mail::layout')

<img width="192" height="75" src="https://nknx.org/assets/images/logo.jpg" alt="NKNx Logo">

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
This email was sent to you as a registered member of <a href="{{ config('app.url') }}">nknx.org</a>. Use of the service and website is subject to our <a href="{{ config('app.url') }}/terms-of-service">Terms of Use</a> and <a href="{{ config('app.url') }}/privacy-policy">Privacy Statement</a>.
<br/><br/>© {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
@endcomponent
@endslot
@endcomponent
