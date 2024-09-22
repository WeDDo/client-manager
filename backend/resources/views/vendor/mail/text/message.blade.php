<x-mail::layout>
    {{--    <x-slot:header>--}}
    {{--        <x-mail::header :url="config('app.url')">--}}
    {{--            {{ config('app.name') }}--}}
    {{--        </x-mail::header>--}}
    {{--    </x-slot:header>--}}

    {{ $slot }}

    <x-slot:footer>
        <x-mail::footer>
            © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
        </x-mail::footer>
    </x-slot:footer>
</x-mail::layout>
