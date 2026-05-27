<x-mail::message>
    # {{$subject}}

    <x-mail::panel>
        mon panel
        {{$content}}
    </x-mail::panel>

    {{$content}}

    <x-mail::button :url="$url">
        Gérer les données de ma fiche {{$shop->company}}
    </x-mail::button>


    {{ config('app.name') }}
</x-mail::message>
