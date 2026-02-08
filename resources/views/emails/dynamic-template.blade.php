<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <style>
        /* Reset */
        body, table, td, p, a, li { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        body { margin: 0; padding: 0; width: 100% !important; }
        img { border: 0; outline: none; text-decoration: none; }

        /* Layout */
        .email-wrapper { width: 100%; background-color: #f1f5f9; padding: 32px 0; }
        .email-container { max-width: 560px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }

        /* Header */
        .email-header { background: linear-gradient(135deg, #4f46e5, #7c3aed); padding: 28px 32px; text-align: center; }
        .email-header h1 { margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 20px; font-weight: 700; color: #ffffff; letter-spacing: -0.3px; }

        /* Body */
        .email-body { padding: 28px 32px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 15px; line-height: 1.7; color: #334155; }
        .email-body h2 { font-size: 18px; font-weight: 600; color: #1e293b; margin: 0 0 12px; }
        .email-body h3 { font-size: 16px; font-weight: 600; color: #1e293b; margin: 0 0 8px; }
        .email-body p { margin: 0 0 16px; }
        .email-body a { color: #4f46e5; text-decoration: underline; }
        .email-body ul, .email-body ol { padding-left: 20px; margin: 0 0 16px; }
        .email-body li { margin-bottom: 6px; }
        .email-body .btn { display: inline-block; background: #4f46e5; color: #ffffff !important; text-decoration: none; padding: 12px 28px; border-radius: 10px; font-weight: 600; font-size: 14px; margin: 8px 0; }
        .email-body .btn:hover { background: #4338ca; }
        .email-body .highlight-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; margin: 16px 0; }
        .email-body .amount { font-size: 24px; font-weight: 700; color: #1e293b; }

        /* Footer */
        .email-footer { padding: 20px 32px; text-align: center; border-top: 1px solid #f1f5f9; }
        .email-footer p { margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 12px; color: #94a3b8; line-height: 1.6; }
        .email-footer a { color: #64748b; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td align="center">
                    <div class="email-container">
                        {{-- Header --}}
                        <div class="email-header">
                            <h1>{{ config('app.name', 'QuizWhiz') }}</h1>
                        </div>

                        {{-- Dynamic body from DB template --}}
                        <div class="email-body">
                            {!! $body !!}
                        </div>

                        {{-- Footer --}}
                        <div class="email-footer">
                            <p>
                                &copy; {{ date('Y') }} {{ config('app.name', 'QuizWhiz') }}. All rights reserved.<br>
                                You received this email because you are a member of {{ config('app.name') }}.
                            </p>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
