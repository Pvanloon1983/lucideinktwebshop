<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ __('Wachtwoord resetten') }}</title>
  <style>
    body {
      font-family: 'DelimaMTProRegular', Arial, sans-serif;
      background: #ecd6af;
      color: #620505;
      margin: 0;
      padding: 0 15px;
    }
    .email-container {
      max-width: 500px;
      margin: 40px auto;
      background: #fff;
      border-radius: 4px;
      box-shadow: 0 1px 8px rgba(98, 5, 5, .12);
      padding: 32px 28px;
    }
    h1 {
      color: #ab0f14;
      font-size: 26px;
      margin-bottom: 18px;
      font-family: 'DelimaMTProRegular', Arial, sans-serif;
    }
    p {
      font-size: 17px;
      margin-bottom: 18px;
    }
    .btn {
      background: #ab0f14;
      color: #fff !important;
      border-radius: 4px;
      padding: 12px 28px;
      text-decoration: none;
      font-size: 16px;
      display: inline-block;
      font-weight: 400;
    }
    .footer {
      font-size: 15px;
      color: #888;
      margin-top: 30px;
      text-align: left;
    }
  </style>
</head>
<body>
  <div class="email-container">
    <h1>{{ __('Wachtwoord resetten') }}</h1>
    <p>{{ __('Beste') }} {{ $first_name }},</p>
    <p>{{ __('Je hebt aangegeven dat je je wachtwoord wilt resetten voor je account op Lucide Inkt.') }}</p>
    <p>{{ __('Klik op de onderstaande knop om een nieuw wachtwoord in te stellen:') }}</p>
    <p style="text-align: center;">
      <a href="{{ url('reset-password', $token) . '?email=' . urlencode($email) }}" class="btn">{{ __('Wachtwoord resetten') }}</a>
    </p>
    <p>{{ __('Als je deze aanvraag niet hebt gedaan, hoef je niets te doen.') }}</p>
    <div class="footer">
      {{ __('Met vriendelijke groet,') }}<br>
      Lucide Inkt
    </div>
  </div>
</body>
</html>
