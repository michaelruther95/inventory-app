@component('mail::message')


VETMASYS has created a new account for you to use. You can login now using the credentials below.

<hr>
<span style="display: block;">
	<strong>Email:</strong> {{ $email }}
</span>
<hr>
<span style="display: block;">
	<strong>Password:</strong> {{ $password }}
</span>
<hr>

@component('mail::button', ['url' => $url])
LOGIN ACCOUNT
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent