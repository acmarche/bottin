<x-mail::message>
    # Introduction

    The body of your message.
    {{$shop->company}}
    <x-mail::button :url="{{$url}}">
        Gérer les données de ma fiche
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
