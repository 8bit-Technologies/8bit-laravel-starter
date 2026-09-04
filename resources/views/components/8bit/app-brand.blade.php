@props(['link' => '/'])

<a href="{{ $link }}" wire:navigate {{ $attributes->class(['flex items-center gap-2 font-extrabold text-lg']) }}>
    <x-icon name="o-cube" class="w-6 h-6 text-primary" />
    <span>{{ config('app.name') }}</span>
</a>
