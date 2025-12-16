<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $subject ?? 'رسالة' }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6f8;font-family:Arial,\'Helvetica Neue\',Helvetica,sans-serif;direction:rtl;color:#333;">
<table width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td align="center" style="padding:20px 10px;">
            <table width="700" cellpadding="0" cellspacing="0" role="presentation" style="max-width:700px;">
                <!-- Header -->
                <tr>
                    <td style="background:#ffffff;padding:20px;border-radius:8px 8px 0 0;"> 
                        <table width="100%" role="presentation">
                            <tr>
                                <td style="text-align:right;vertical-align:middle;">
                                    <img src="{{ asset('images/logo.png') }}" alt="Logo" width="120" style="display:inline-block;vertical-align:middle;">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Content card -->
                <tr>
                    <td style="background:#ffffff;padding:28px 32px;border:1px solid #eef2f5;border-top:none;">
                        <h2 style="margin:0 0 12px 0;font-size:20px;color:#0b2a4a;text-align:right;">{{ $subject ?? 'رسالة' }}</h2>
                        <div style="font-size:15px;line-height:1.6;color:#333;text-align:right;">
                            {{-- Render body as trusted HTML. Ensure content is sanitized upstream. --}}
                            {!! $body !!}
                        </div>

                        <!-- Optional CTA example (uncomment in usage) -->
                        {{--
                        <p style="text-align:center;margin-top:20px;">
                            <a href="#" style="background:#2874f0;color:#fff;padding:10px 18px;border-radius:6px;text-decoration:none;display:inline-block;">عرض الطلب</a>
                        </p>
                        --}}
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="padding:14px 18px;text-align:center;color:#7b8694;font-size:13px;background:transparent;">
                        <div style="max-width:700px;margin:0 auto;">
                            <div style="padding:12px 0;color:#7b8694;">© {{ date('Y') }} Moon Chef. جميع الحقوق محفوظة.</div>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
