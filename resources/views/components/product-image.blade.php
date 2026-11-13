@props([
    'url' => null,
    'ean' => null,
    'alt' => '',
    'class' => 'h-full w-full object-contain',
    'wrapperClass' => 'relative grid h-full w-full place-items-center overflow-hidden rounded-xl bg-slate-100',
    'lazy' => true,
])

@php
    $fallback = asset('/LOGO_FOCCUS.png');
    $primaryUrl = $url ?? $fallback;
    $eanPicturesUrl = $ean
        ? 'http://www.eanpictures.com.br:9000/api/gtin/' . preg_replace('/\D/', '', $ean)
        : null;
    $loadingAttr = $lazy ? 'loading="lazy"' : '';
@endphp

@once
    <style>
        /* Logo da Foccus usado no lugar da foto: centralizado e com respiro nas bordas */
        img.product-image-fallback {
            object-fit: contain !important;
            padding: 15% !important;
        }
    </style>
@endonce

<div class="{{ $wrapperClass }}">
    <img
        src="{{ $primaryUrl }}"
        alt="{{ $alt }}"
        class="{{ $class }}{{ $url ? '' : ' product-image-fallback' }}"
        {{ $loadingAttr }}
        decoding="async"
        onerror="
            var img = this;
            var usarLogo = function () { img.onerror = null; img.classList.add('product-image-fallback'); img.src = '{{ $fallback }}'; };
            if (!img._fallbackAttempted) {
                img._fallbackAttempted = true;
                @if($eanPicturesUrl)
                var xhr = new XMLHttpRequest();
                xhr.timeout = 2000;
                xhr.open('HEAD', '{{ $eanPicturesUrl }}', true);
                xhr.onload = function() { if(xhr.status < 400) img.src = '{{ $eanPicturesUrl }}'; else usarLogo(); };
                xhr.onerror = usarLogo;
                xhr.ontimeout = usarLogo;
                xhr.send();
                @else
                usarLogo();
                @endif
            } else {
                usarLogo();
            }
        "
    >
</div>
