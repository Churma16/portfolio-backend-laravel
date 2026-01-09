<x-mail::message>
# 🚀 New Mission: Incoming Inquiry!

Halo Fathan, sistem **Churma.codes** baru saja menerima pesan dari seseorang yang tertarik membangun *scalable apps* bersama Anda.

Berikut detail pengirimnya:

<x-mail::panel>
**Name:** {{ $data['name'] }}
<br>
**Email:** {{ $data['email'] }}
</x-mail::panel>

**Message Content:**

"{{ $data['content'] ?? $data['message'] }}"

---

Jangan biarkan mereka menunggu terlalu lama. Segera balas untuk memulai kolaborasi.

<x-mail::button :url="'mailto:' . $data['email']">
Reply to {{ $data['name'] }} ➜
</x-mail::button>

Happy Coding,
**Churma.codes Notification Bot**
</x-mail::message>
