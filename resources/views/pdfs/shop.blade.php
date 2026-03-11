<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 13px;
            color: #1a1a1a;
            line-height: 1.5;
        }

        .header {
            background-color: #1e3a5f;
            color: #ffffff;
            padding: 24px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 22px;
            font-weight: 700;
            margin: 0;
        }

        .header .date {
            font-size: 11px;
            opacity: 0.8;
        }

        .content {
            padding: 24px 32px;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #1e3a5f;
            border-bottom: 2px solid #1e3a5f;
            padding-bottom: 4px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 24px;
        }

        .info-row {
            display: flex;
            gap: 8px;
        }

        .info-label {
            font-weight: 600;
            color: #555;
            min-width: 120px;
            flex-shrink: 0;
        }

        .info-value {
            color: #1a1a1a;
        }

        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            margin: 2px 4px 2px 0;
        }

        .badge-enabled {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-disabled {
            background-color: #f8d7da;
            color: #721c24;
        }

        .badge-category {
            background-color: #e8f0fe;
            color: #1a3e72;
        }

        .badge-principal {
            background-color: #fff3cd;
            color: #856404;
        }

        .badge-tag {
            background-color: #e2e3e5;
            color: #383d41;
        }

        .category-path {
            padding: 4px 0;
            font-size: 12px;
        }

        .category-path .separator {
            color: #999;
            margin: 0 4px;
        }

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .schedule-table th {
            background-color: #f0f4f8;
            color: #1e3a5f;
            padding: 6px 10px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #d0d9e4;
        }

        .schedule-table td {
            padding: 5px 10px;
            border-bottom: 1px solid #e8ecf0;
        }

        .schedule-table tr:last-child td {
            border-bottom: none;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
        }

        .feature-icon {
            width: 16px;
            text-align: center;
            font-weight: bold;
        }

        .feature-yes {
            color: #28a745;
        }

        .feature-no {
            color: #ccc;
        }

        .contact-block {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 8px;
        }

        .contact-block-title {
            font-weight: 700;
            font-size: 13px;
            color: #1e3a5f;
            margin-bottom: 6px;
        }

        .notes-block {
            background-color: #fffef5;
            border-left: 3px solid #f0c040;
            padding: 10px 14px;
            font-size: 12px;
            margin-top: 6px;
        }

        .social-links {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .social-link {
            font-size: 12px;
            color: #1e3a5f;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            color: #999;
            padding: 16px 32px;
            border-top: 1px solid #e8ecf0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $shop->company }}</h1>
        <div class="date">{{ now()->format('d/m/Y') }}</div>
    </div>

    <div class="content">
        {{-- Status --}}
        <div class="section">
            <span class="badge {{ $shop->enabled ? 'badge-enabled' : 'badge-disabled' }}">
                {{ $shop->enabled ? 'Actif' : 'Inactif' }}
            </span>
            @if($shop->vat_number)
                <span style="margin-left: 12px; font-size: 12px; color: #555;">TVA : {{ $shop->vat_number }}</span>
            @endif
            @if($shop->pointOfSale)
                <span style="margin-left: 12px; font-size: 12px; color: #555;">Point de vente : {{ $shop->pointOfSale->name }}</span>
            @endif
        </div>

        {{-- Address & Contact --}}
        <div class="section">
            <div class="section-title">Coordonnees</div>
            <div class="info-grid">
                <div class="info-row">
                    <span class="info-label">Adresse</span>
                    <span class="info-value">
                        {{ $shop->street }} {{ $shop->number }}<br>
                        {{ $shop->postal_code }} {{ $shop->city }}
                    </span>
                </div>
                @if($shop->phone)
                    <div class="info-row">
                        <span class="info-label">Telephone</span>
                        <span class="info-value">{{ $shop->phone }}</span>
                    </div>
                @endif
                @if($shop->phone_other)
                    <div class="info-row">
                        <span class="info-label">Tel. secondaire</span>
                        <span class="info-value">{{ $shop->phone_other }}</span>
                    </div>
                @endif
                @if($shop->mobile)
                    <div class="info-row">
                        <span class="info-label">GSM</span>
                        <span class="info-value">{{ $shop->mobile }}</span>
                    </div>
                @endif
                @if($shop->fax)
                    <div class="info-row">
                        <span class="info-label">Fax</span>
                        <span class="info-value">{{ $shop->fax }}</span>
                    </div>
                @endif
                @if($shop->email)
                    <div class="info-row">
                        <span class="info-label">Email</span>
                        <span class="info-value">{{ $shop->email }}</span>
                    </div>
                @endif
                @if($shop->website)
                    <div class="info-row">
                        <span class="info-label">Site web</span>
                        <span class="info-value">{{ $shop->website }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Social Media --}}
        @if($shop->facebook || $shop->instagram || $shop->twitter || $shop->tiktok || $shop->youtube || $shop->linkedin)
            <div class="section">
                <div class="section-title">Reseaux sociaux</div>
                <div class="social-links">
                    @foreach(['facebook', 'instagram', 'twitter', 'tiktok', 'youtube', 'linkedin'] as $network)
                        @if($shop->{$network})
                            <span class="social-link">{{ ucfirst($network) }}: {{ $shop->{$network} }}</span>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Features --}}
        <div class="section">
            <div class="section-title">Caracteristiques</div>
            <div class="features-grid">
                <div class="feature-item">
                    <span class="feature-icon {{ $shop->city_center ? 'feature-yes' : 'feature-no' }}">{{ $shop->city_center ? '&#10003;' : '&#10005;' }}</span>
                    Centre-ville
                </div>
                <div class="feature-item">
                    <span class="feature-icon {{ $shop->open_at_lunch ? 'feature-yes' : 'feature-no' }}">{{ $shop->open_at_lunch ? '&#10003;' : '&#10005;' }}</span>
                    Ouvert le midi
                </div>
                <div class="feature-item">
                    <span class="feature-icon {{ $shop->pmr ? 'feature-yes' : 'feature-no' }}">{{ $shop->pmr ? '&#10003;' : '&#10005;' }}</span>
                    Acces PMR
                </div>
                <div class="feature-item">
                    <span class="feature-icon {{ $shop->click_collect ? 'feature-yes' : 'feature-no' }}">{{ $shop->click_collect ? '&#10003;' : '&#10005;' }}</span>
                    Click & Collect
                </div>
                <div class="feature-item">
                    <span class="feature-icon {{ $shop->ecommerce ? 'feature-yes' : 'feature-no' }}">{{ $shop->ecommerce ? '&#10003;' : '&#10005;' }}</span>
                    E-commerce
                </div>
            </div>
        </div>

        {{-- Categories --}}
        @if($shop->categories->isNotEmpty())
            <div class="section">
                <div class="section-title">Categories</div>
                @foreach($shop->categories as $category)
                    <div class="category-path">
                        @if($category->pivot->principal)
                            <span class="badge badge-principal">Principale</span>
                        @endif
                        <span class="badge badge-category">{{ $category->fullPath() }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Tags --}}
        @if($shop->tags->isNotEmpty())
            <div class="section">
                <div class="section-title">Tags</div>
                <div>
                    @foreach($shop->tags as $tag)
                        <span class="badge badge-tag">{{ $tag->name }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Situations --}}
        @if($shop->situations->isNotEmpty())
            <div class="section">
                <div class="section-title">Situations</div>
                <div>
                    @foreach($shop->situations as $situation)
                        <span class="badge badge-tag">{{ $situation->name }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Schedules --}}
        @if($shop->schedules->isNotEmpty())
            <div class="section">
                <div class="section-title">Horaires</div>
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th>Jour</th>
                            <th>Matin</th>
                            <th>Apres-midi</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
                        @endphp
                        @foreach($shop->schedules->sortBy('day') as $schedule)
                            <tr>
                                <td style="font-weight: 600;">{{ $days[$schedule->day] ?? $schedule->day }}</td>
                                <td>
                                    @if($schedule->is_closed)
                                        <span style="color: #999;">—</span>
                                    @elseif($schedule->morning_start && $schedule->morning_end)
                                        {{ \Carbon\Carbon::parse($schedule->morning_start)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->morning_end)->format('H:i') }}
                                    @else
                                        <span style="color: #999;">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($schedule->is_closed)
                                        <span style="color: #999;">—</span>
                                    @elseif($schedule->noon_start && $schedule->noon_end)
                                        {{ \Carbon\Carbon::parse($schedule->noon_start)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->noon_end)->format('H:i') }}
                                    @else
                                        <span style="color: #999;">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($schedule->is_closed)
                                        <span style="color: #dc3545;">Ferme</span>
                                    @elseif($schedule->is_by_appointment)
                                        <span style="color: #856404;">Sur RDV</span>
                                    @else
                                        <span style="color: #28a745;">Ouvert</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Contact Person --}}
        @if($shop->last_name || $shop->first_name)
            <div class="section">
                <div class="section-title">Personne de contact</div>
                <div class="contact-block">
                    <div class="contact-block-title">
                        {{ $shop->civility }} {{ $shop->first_name }} {{ $shop->last_name }}
                        @if($shop->function)
                            <span style="font-weight: 400; color: #555;"> — {{ $shop->function }}</span>
                        @endif
                    </div>
                    <div class="info-grid">
                        @if($shop->contact_street)
                            <div class="info-row">
                                <span class="info-label">Adresse</span>
                                <span class="info-value">{{ $shop->contact_street }} {{ $shop->contact_number }}, {{ $shop->contact_postal_code }} {{ $shop->contact_city }}</span>
                            </div>
                        @endif
                        @if($shop->contact_phone)
                            <div class="info-row">
                                <span class="info-label">Telephone</span>
                                <span class="info-value">{{ $shop->contact_phone }}</span>
                            </div>
                        @endif
                        @if($shop->contact_mobile)
                            <div class="info-row">
                                <span class="info-label">GSM</span>
                                <span class="info-value">{{ $shop->contact_mobile }}</span>
                            </div>
                        @endif
                        @if($shop->contact_email)
                            <div class="info-row">
                                <span class="info-label">Email</span>
                                <span class="info-value">{{ $shop->contact_email }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Admin Contact --}}
        @if($shop->admin_last_name || $shop->admin_first_name)
            <div class="section">
                <div class="section-title">Contact administratif</div>
                <div class="contact-block">
                    <div class="contact-block-title">
                        {{ $shop->admin_civility }} {{ $shop->admin_first_name }} {{ $shop->admin_last_name }}
                        @if($shop->admin_function)
                            <span style="font-weight: 400; color: #555;"> — {{ $shop->admin_function }}</span>
                        @endif
                    </div>
                    <div class="info-grid">
                        @if($shop->admin_phone)
                            <div class="info-row">
                                <span class="info-label">Telephone</span>
                                <span class="info-value">{{ $shop->admin_phone }}</span>
                            </div>
                        @endif
                        @if($shop->admin_mobile)
                            <div class="info-row">
                                <span class="info-label">GSM</span>
                                <span class="info-value">{{ $shop->admin_mobile }}</span>
                            </div>
                        @endif
                        @if($shop->admin_email)
                            <div class="info-row">
                                <span class="info-label">Email</span>
                                <span class="info-value">{{ $shop->admin_email }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Notes --}}
        @if($shop->comment1 || $shop->comment2 || $shop->comment3 || $shop->note)
            <div class="section">
                <div class="section-title">Notes</div>
                @foreach(['comment1', 'comment2', 'comment3', 'note'] as $field)
                    @if($shop->{$field})
                        <div class="notes-block">{{ $shop->{$field} }}</div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    <div class="footer">
        Bottin — Fiche commercant exportee le {{ now()->format('d/m/Y a H:i') }}
    </div>
</body>
</html>
