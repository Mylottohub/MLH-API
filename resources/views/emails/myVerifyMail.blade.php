<img src="{{ $mailData['img'] }} " width="150px"/>
<h2> <span style="color:#D62828"> Welcome {{ $mailData['name'] }} </span></h2>

<p>Thank you for registering with My Lotto Hub. </p>
    
<p>Your verification code is:
</p>

<h2><span style="color:#3A1E92">
        {{ $mailData['otp'] }}</span></h2>

<p>Enter this code to verify your account and then set your new login pin.</p>
<p>Hey!! with One wallet, you can now play all Nigerian lotto games</p>
<p>Do not reply to this automated message</p>

Thanks,<br>
<span style="color:#D62828">Management ({{ config('app.name') }}</span>)
