@props(['title', 'subtitle' => null])

<x-header :title="$title" :subtitle="$subtitle" separator size="text-xl" {{ $attributes }}>
    @isset($actions)
        <x-slot:actions>
            {{ $actions }}
        </x-slot:actions>
    @endisset
</x-header>
