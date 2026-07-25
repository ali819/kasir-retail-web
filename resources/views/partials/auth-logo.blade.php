{{-- Logo KasirKu untuk halaman auth. Param: $qr (bool) -> true = buka modal QR, false = link ke login --}}
@php $qr = $qr ?? false; @endphp
<a href="{{ $qr ? 'javascript:void(0)' : route('login') }}"
   @if ($qr) data-bs-toggle="modal" data-bs-target="#ModalQrCode" @endif
   class="text-nowrap logo-img text-center d-flex align-items-center justify-content-center gap-2 mb-4 w-100">
    <i class="ti ti-shopping-cart text-primary" style="font-size:34px; line-height:1;"></i>
    <span style="font-size:28px; font-weight:700; color:#2a3547;">Kasir<span class="text-primary">Ku</span></span>
</a>
