<x-mail::message>
# Ada Peluang Kolaborasi Baru! 💼

Halo, ada pesan baru masuk:

<x-mail::panel>
**Nama:** {{ $data['name'] }}
<br>
**Email:** {{ $data['email'] }}
</x-mail::panel>

**Pesan Mereka:**

{{ $data['content'] ?? $data['message'] }}

<x-mail::button :url="'mailto:' . $data['email']">
Balas Pesan
</x-mail::button>

Salam,
**Portfolio Notification System**
</x-mail::message>
