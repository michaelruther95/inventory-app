@component('mail::message')

#Hello,

You requested a password reset for your account. Just click the button below and we'll redirect you to the reset password form page.

@component('mail::button', ['url' => $url])
RESET PASSWORD
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent