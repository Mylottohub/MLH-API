<img src="{{ $mailData['img'] }} " width="150px"/>
<h2><span style="color:#D62828"> Dear {{ $mailData['name'] ?? 'User' }} </span></h2>

<p>Your password reset security code is:</p>

<h2><span style="color:#3A1E92"> {{ $mailData['otp'] }}</span></h2>

<p>Someone recently requested to reset your account password.. Enter the above code into the “Security Code” field in
    your LITApp to complete the password reset.</p>

<p>Note: For your protection, the server sent this email to all of the contact email addresses that you associated with
    your account. If you did not initiate this request, contact your system administrator.</p>

<p>Do not reply to this automated message.</p>

Thanks,<br>
<span style="color:#D62828">Management ({{ config('app.name') }}</span>)
