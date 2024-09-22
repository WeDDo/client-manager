<x-mail::layout>
    <x-slot:header>
    <x-mail::header :url="config('app.url')">
        {{ config('app.display_name') }}
    </x-mail::header>
    </x-slot:header>
    {{ $slot }}
    <x-slot:footer>
        <x-mail::footer>
            © {{ date('Y') }} {{ config('app.display_name') }}. {{ __('All rights reserved.') }}
        </x-mail::footer>
    </x-slot:footer>
</x-mail::layout>
