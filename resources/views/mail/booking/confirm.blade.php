{{-- <x-mail::message>
# Article Confirmed

Dear {{ $data['name'] }},

We are pleased to inform you that your confirmation on {{ date('d-m-Y', strtotime($data['date'])) }} at {{ $data['time'] }} has been confirmed.

<x-mail::button :url="route('blog.index')">
Read Our Blog
</x-mail::button>

Thank you for choosing us.

Best regards,
Laravel Blog
</x-mail::message>
 --}}
