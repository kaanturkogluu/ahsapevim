<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banka Doğrulama - AhşapEvim</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            width: 100%;
            max-width: 520px;
            padding: 16px;
        }

        .card {
            background: rgba(255,255,255,0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 24px;
            padding: 32px 28px;
            text-align: center;
            box-shadow: 0 32px 80px rgba(0,0,0,0.5);
        }

        .logo {
            font-size: 22px;
            font-weight: 800;
            color: #C87A53;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }

        .logo span { color: #fff; }

        .separator {
            width: 40px;
            height: 2px;
            background: linear-gradient(90deg, #C87A53, #e8a87c);
            margin: 14px auto;
            border-radius: 2px;
        }

        .lock-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #C87A53, #e8a87c);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            box-shadow: 0 8px 24px rgba(200, 122, 83, 0.4);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 8px 24px rgba(200, 122, 83, 0.4); }
            50% { transform: scale(1.06); box-shadow: 0 12px 36px rgba(200, 122, 83, 0.6); }
        }

        .lock-icon svg { width: 28px; height: 28px; fill: white; }

        h1 {
            color: #fff;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .subtitle {
            color: rgba(255,255,255,0.6);
            font-size: 13.5px;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .order-info {
            background: rgba(200, 122, 83, 0.1);
            border: 1px solid rgba(200, 122, 83, 0.25);
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: left;
        }

        .order-info .label { color: rgba(255,255,255,0.5); font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        .order-info .value { color: #fff; font-weight: 700; font-size: 14px; }
        .order-info .amount { color: #e8a87c; font-weight: 800; font-size: 18px; }

        .spinner-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(200,122,83,0.3);
            border-top-color: #C87A53;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .spinner-text { color: rgba(255,255,255,0.7); font-size: 13px; }

        .security-badges {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .badge {
            display: flex;
            align-items: center;
            gap: 5px;
            color: rgba(255,255,255,0.45);
            font-size: 11px;
        }

        .badge svg { width: 14px; height: 14px; fill: rgba(255,255,255,0.35); }

        /* Iyzico 3DS iframe/form wrapper */
        #iyzico3dsContainer {
            display: none;
            margin-top: 20px;
        }

        #iyzico3dsContainer iframe {
            width: 100%;
            min-height: 420px;
            border: none;
            border-radius: 12px;
            background: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="logo">Ahşap<span>Evim</span></div>
            <div class="separator"></div>

            <div class="lock-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM12 17c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                </svg>
            </div>

            <h1>3D Secure Doğrulama</h1>
            <p class="subtitle">Bankanızın güvenlik sayfasına yönlendiriliyorsunuz.<br>Lütfen bu pencereyi kapatmayın.</p>

            <div class="order-info">
                <div>
                    <div class="label">Sipariş No</div>
                    <div class="value">#{{ $order->id }}</div>
                </div>
                <div style="text-align: right;">
                    <div class="label">Ödeme Tutarı</div>
                    <div class="amount">{{ number_format($order->total_amount, 2, ',', '.') }} ₺</div>
                </div>
            </div>

            <div class="spinner-container">
                <div class="spinner"></div>
                <span class="spinner-text">Banka bağlantısı kuruluyor...</span>
            </div>

            <div class="security-badges">
                <div class="badge">
                    <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
                    <span>256-bit SSL Şifreleme</span>
                </div>
                <div class="badge">
                    <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>
                    <span>Iyzico Güvenceli</span>
                </div>
                <div class="badge">
                    <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    <span>3D Secure</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Iyzico 3DS HTML formunu sayfa yüklendiğinde gönder --}}
    <div id="iyzico3dsFormContainer" style="display:none;">
        {!! $htmlContent !!}
    </div>

    <script>
        // Sayfa yüklenince Iyzico'nun form/iframe'ini otomatik gönder
        window.addEventListener('load', function () {
            // Iyzico genellikle kendi form'unu otomatik submit eder
            // Eğer bir form varsa yine de manuel tetikle
            var forms = document.querySelectorAll('#iyzico3dsFormContainer form');
            if (forms.length > 0) {
                setTimeout(function () {
                    forms[0].submit();
                }, 800);
            }
        });
    </script>
</body>
</html>
