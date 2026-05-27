<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$shop->company}}</title>
</head>
<body
    style="background-color: #f8fafc; font-family: Inter, ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif; margin: 0; padding: 0;">
<div style="max-width: 752px; margin: 0 auto; padding: 24px;">

    {{$content}}

    <p style="color: #94a3b8; font-size: 12px; text-align: center; margin-top: 16px;">
        Gérez votre fiche en cliquant sur cette url
    </p>

    <a href="{{$url}}">{{$url}}</a>
    <p style="color: #94a3b8; font-size: 12px; text-align: center; margin-top: 16px;">
        {{ config('app.name') }}
    </p>
</div>
</body>
</html>


