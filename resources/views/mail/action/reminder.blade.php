<x-mail::message>
{{-- Header with both logos --}}
<x-slot:header>
<tr>
<td class="header" style="padding: 28px 0; text-align: center; background-color: #ffffff;">
<table align="center" cellpadding="0" cellspacing="0" role="presentation" style="margin: 0 auto;">
<tr>
@if($logoMarche)
<td style="padding: 0 24px; vertical-align: middle;">
<img src="{{ $message->embed($logoMarche) }}" height="64" alt="Ville de Marche-en-Famenne" style="display: block; max-height: 64px; width: auto; border: 0;">
</td>
@endif
@if($logoAdl)
<td style="padding: 0 24px; vertical-align: middle;">
<img src="{{ $message->embed($logoAdl) }}" height="64" alt="Agence de Développement Local" style="display: block; max-height: 64px; width: auto; border: 0;">
</td>
@endif
</tr>
</table>
</td>
</tr>
</x-slot:header>

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

{{-- Footer --}}
<x-slot:footer>
<tr>
<td>
<table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="content-cell" align="center" style="padding: 32px; color: #b0adc5; font-size: 12px; text-align: center; line-height: 1.6;">
<strong style="color: #5c5e6b; font-size: 13px;">Agence de Développement Local</strong><br>
Rue Victor Libert 36 J — 6900 Marche-en-Famenne<br>
<a href="tel:+3284327078" style="color: #5c5e6b; text-decoration: none;">084 32 70 78</a> — <a href="mailto:adl@marche.be" style="color: #5c5e6b; text-decoration: none;">adl@marche.be</a><br>
<a href="https://adl.marche.be" style="color: #5c5e6b; text-decoration: none;">adl.marche.be</a>
<br><br>
© {{ date('Y') }} {{ config('app.name') }}
</td>
</tr>
</table>
</td>
</tr>
</x-slot:footer>
</x-mail::message>
