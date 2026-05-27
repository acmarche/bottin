<x-mail::message>
<table align="center" cellpadding="0" cellspacing="0" role="presentation" style="margin: 0 auto 24px; border-collapse: collapse;">
<tr>
@if($logoMarche)
<td style="padding: 0 20px; vertical-align: middle;">
<img src="{{ $message->embed($logoMarche) }}" height="64" alt="Ville de Marche-en-Famenne" style="display: block; max-height: 64px; width: auto; border: 0;">
</td>
@endif
@if($logoAdl)
<td style="padding: 0 20px; vertical-align: middle;">
<img src="{{ $message->embed($logoAdl) }}" height="64" alt="Agence de Développement Local" style="display: block; max-height: 64px; width: auto; border: 0;">
</td>
@endif
</tr>
</table>

# {{ $subject }}

Bonjour,

{!! $content !!}

<x-mail::panel>
**Fiche concernée :** {{ $shop->company }}
</x-mail::panel>

@if($url)
<x-mail::button :url="$url" color="primary">
Gérer les données de ma fiche
</x-mail::button>
@endif

Cordialement,
**{{ config('app.name') }}**

<x-slot:subcopy>
<strong>Agence de Développement Local</strong><br>
Rue Victor Libert 36 J — 6900 Marche-en-Famenne<br>
<a href="tel:+3284327078" style="color: inherit;">084 32 70 78</a> — <a href="mailto:adl@marche.be" style="color: inherit;">adl@marche.be</a><br>
<a href="https://adl.marche.be" style="color: inherit;">adl.marche.be</a>
</x-slot:subcopy>
</x-mail::message>
