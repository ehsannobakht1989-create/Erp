<!doctype html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex">
<title>نصب سامانه ERP</title>
<style>body{font-family:Tahoma,sans-serif;max-width:760px;margin:2rem auto;padding:0 1rem;line-height:1.8}fieldset{margin:1rem 0}label{display:block;margin:.4rem 0}input,select{width:100%;padding:.4rem}.ok{color:#176b2c}.bad{color:#b00020}.errors{background:#fde8ea;padding:.5rem 1.5rem}code{direction:ltr;display:block;background:#f3f3f3;padding:.5rem;text-align:left}</style>
</head>
<body>
<h1>نصب سامانه ERP</h1>
@if ($done)
    <p class="ok">نصب با موفقیت انجام شد و نصب‌کننده قفل شد.</p>
    <p>ماژول‌های دارای داده اولیه: {{ $seeded === [] ? 'هیچ' : implode('، ', $seeded) }}</p>
    <p>برای اجرای زمان‌بندی و صف رویدادها، این خط را در Cron هاست (هر دقیقه) اضافه کنید:</p>
    <code>* * * * * cd {{ $base }} &amp;&amp; php artisan schedule:run &gt;&gt; /dev/null 2&gt;&amp;1</code>
@else
    <h2>بررسی پیش‌نیازها</h2>
    <ul>
    @foreach ($requirements as $check)
        <li class="{{ $check['ok'] ? 'ok' : 'bad' }}">{{ $check['ok'] ? '✓' : '✗' }} {{ $check['label'] }}</li>
    @endforeach
    </ul>
    @if ($errors !== [])
        <div class="errors" role="alert"><ul>@foreach ($errors as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <form method="post" action="/install">
        <input type="hidden" name="_install_token" value="{{ $token }}">
        <fieldset><legend>پایگاه داده</legend>
            <label>نوع<select name="db_connection"><option value="mysql" @selected(($old['db_connection'] ?? 'mysql') === 'mysql')>MySQL / MariaDB</option><option value="sqlite" @selected(($old['db_connection'] ?? '') === 'sqlite')>SQLite</option></select></label>
            <label>میزبان<input name="db_host" value="{{ $old['db_host'] ?? '127.0.0.1' }}" dir="ltr"></label>
            <label>درگاه<input name="db_port" value="{{ $old['db_port'] ?? '3306' }}" dir="ltr"></label>
            <label>نام پایگاه داده (برای SQLite مسیر کامل فایل)<input name="db_database" value="{{ $old['db_database'] ?? '' }}" dir="ltr" required></label>
            <label>نام کاربری<input name="db_username" value="{{ $old['db_username'] ?? '' }}" dir="ltr"></label>
            <label>گذرواژه<input type="password" name="db_password" dir="ltr"></label>
        </fieldset>
        <fieldset><legend>سامانه و مدیر ارشد</legend>
            <label>نشانی سامانه<input name="app_url" value="{{ $old['app_url'] ?? '' }}" dir="ltr" required></label>
            <label>نام مدیر<input name="admin_name" value="{{ $old['admin_name'] ?? '' }}" required></label>
            <label>ایمیل مدیر<input type="email" name="admin_email" value="{{ $old['admin_email'] ?? '' }}" dir="ltr" required></label>
            <label>گذرواژه مدیر (حداقل ۱۰ نویسه)<input type="password" name="admin_password" dir="ltr" required></label>
            <label>تکرار گذرواژه<input type="password" name="admin_password_confirmation" dir="ltr" required></label>
        </fieldset>
        <button type="submit">نصب</button>
    </form>
@endif
</body>
</html>
