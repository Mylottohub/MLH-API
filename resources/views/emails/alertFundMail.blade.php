<img src="{{ $mailData['img'] }} " width="150px"/>
<h2> <span style="color:#D62828"> Hi {{ $mailData['name'] }} </span></h2>

<p>Your account has been credited with {{ $mailData['amount'] }}. Kindly login to check </p>
  
Thanks,<br>
<span style="color:#D62828">Management ({{ config('app.name') }}</span>)