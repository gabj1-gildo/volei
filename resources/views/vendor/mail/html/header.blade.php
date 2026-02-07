@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://piravolei.online/img/logo.png" class="logo" alt="PiraVôlei">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
