@component('mail::message')
# Reset Password Notification

Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda.

@component('mail::button', ['url' => $actionUrl])
Reset Password
@endcomponent

Link reset password ini akan kedaluwarsa dalam {{ config('auth.passwords.users.expire', 60) }} menit.

Jika Anda tidak meminta reset password, tidak ada tindakan lebih lanjut yang diperlukan.

Salam,<br>
{{ config('app.name') }}

@component('mail::subcopy')
Jika Anda mengalami masalah dengan tombol "Reset Password", salin dan tempel URL berikut
ke browser web Anda: [{{ $actionUrl }}]({{ $actionUrl }})
@endcomponent
@endcomponent
