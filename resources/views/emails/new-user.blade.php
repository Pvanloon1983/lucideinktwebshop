<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>{{ __('Welkom bij Lucide Inkt') }}</title>
  <style>
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background: #f7f7f7;
      color: #222;
      margin: 0;
      padding: 0;
    }
    .email-container {
      max-width: 600px;
      margin: 40px auto;
      background: #fff;
      border-radius: 8px;
      padding: 32px 24px;
      box-shadow: 0 2px 8px #eee;
      box-sizing: border-box;
    }
    h1 {
      color: #ab0f14;
      font-size: 2em;
      margin-bottom: 0;
      font-family: 'Segoe UI', Arial, sans-serif;
    }
    .details {
      margin: 24px 0;
      font-size: 15px;
    }
    .btn {
      display: inline-block;
      padding: 12px 24px;
      background: #ab0f14;
      color: #fff !important;
      text-decoration: none;
      border-radius: 4px;
      margin-top: 16px;
      font-size: 16px;
      font-weight: 400;
    }
    .footer {
      margin-top: 32px;
      font-size: 15px;
      color: #888;
      text-align: left;
    }
  </style>
</head>
<body>
<div class="email-container">
  <h1>{{ __('Welkom bij Lucide Inkt!') }}</h1>
  <p>{{ __('Hoi') }} {{ $user->first_name }},</p>
  <p>
    {{ __('Je bent succesvol als nieuwe gebruiker geregistreerd bij Lucide Inkt.') }}<br>
  </p>
  <div class="details">
    <strong>{{ __('Jouw gegevens:') }}</strong><br>
    {{ __('Naam:') }} {{ $user->first_name }} {{ $user->last_name }}<br>
    {{ __('E-mailadres:') }} {{ $user->email }}
  </div>
  <a href="https://lucideinkt.nl/login" class="btn">{{ __('Inloggen bij Lucide Inkt') }}</a>
  <p style="margin-top:24px;">
    {{ __('Heb je vragen of hulp nodig? Neem gerust contact met ons op via') }} <a href="mailto:info@lucideinkt.nl">info@lucideinkt.nl</a>.
  </p>
  <div class="footer">
    {{ __('Met vriendelijke groet,') }}<br>
    Lucide Inkt
  </div>
</div>
</body>
</html>