@props([
    'title' => '',
    'icon' => '',
])

<div class="card">
    <div class="card-header"><i class="{{ $icon }} m-1"></i>{{ $title }}</div>

    <div class="card-body">
        {{ $slot }}
    </div>
</div>
