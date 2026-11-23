@props(['is' => null])

@php
    // إذا لم يتم تمرير الis، نتحقق إن كان هناك href لنجعله رابط a، وإلا نحعله div افتراضياً
    $tag = $is ?? ($attributes->has('href') ? 'a' : 'div');
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => 'border border-border rounded-lg bg-card p-4 md:text-sm block']) }}>
{{ $slot }}
</{{ $tag }}>
