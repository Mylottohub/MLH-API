<img src="{{ $mailData['img'] }} " width="150px"/>
<h2> <span style="color:#D62828"> Hi Admin </span></h2>

<p>{{ $mailData['name'] }} just made a withdrawal request of {{ $mailData['amount'] }}. Kindly login to check </p>
    
Thanks,<br>
<span style="color:#D62828">Management ({{ config('app.name') }}</span>)