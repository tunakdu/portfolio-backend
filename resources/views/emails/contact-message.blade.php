<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni İletişim Mesajı</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            background-color: #f8fafc;
            padding: 20px;
        }
        .email-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .message-info {
            background: #f1f5f9;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #3b82f6;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
            align-items: center;
        }
        .info-label {
            font-weight: 600;
            color: #374151;
            width: 80px;
            margin-right: 15px;
        }
        .info-value {
            color: #6b7280;
            flex: 1;
        }
        .message-content {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        .message-content h3 {
            margin-top: 0;
            color: #111827;
            font-size: 18px;
        }
        .message-text {
            color: #374151;
            line-height: 1.7;
            white-space: pre-wrap;
        }
        .footer {
            background: #f8fafc;
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            border-top: 1px solid #e5e7eb;
        }
        .reply-button {
            display: inline-block;
            background: #3b82f6;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        .reply-button:hover {
            background: #2563eb;
        }
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .content {
                padding: 20px;
            }
            .header {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🎯 Yeni İletişim Mesajı</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Portfolio sitenizden yeni bir mesaj aldınız</p>
        </div>

        <div class="content">
            <div class="message-info">
                <div class="info-row">
                    <div class="info-label">👤 İsim:</div>
                    <div class="info-value"><strong>{{ $message->name }}</strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">📧 Email:</div>
                    <div class="info-value">
                        <a href="mailto:{{ $message->email }}" style="color: #3b82f6; text-decoration: none;">
                            {{ $message->email }}
                        </a>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">📅 Tarih:</div>
                    <div class="info-value">{{ $message->created_at ? $message->created_at->format('d.m.Y H:i') : now()->format('d.m.Y H:i') }}</div>
                </div>
            </div>

            <div class="message-content">
                <h3>💬 Mesaj İçeriği:</h3>
                <div class="message-text">{{ $message->message }}</div>
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <a href="mailto:{{ $message->email }}?subject=Re: Portfolio İletişim" class="reply-button">
                    📧 Hemen Yanıtla
                </a>
            </div>
        </div>

        <div class="footer">
            <p>Bu mesaj <strong>{{ config('app.name') }}</strong> portfolio sitesinden gönderilmiştir.</p>
            <p style="margin: 5px 0 0 0; font-size: 12px;">
                🌐 <a href="{{ config('app.url') }}" style="color: #6b7280;">{{ config('app.url') }}</a>
            </p>
        </div>
    </div>
</body>
</html>