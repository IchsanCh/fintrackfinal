<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — FinTrack</title>
</head>

<body style="margin:0;padding:0;background:#09080f;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#09080f;padding:48px 16px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;">

                    {{-- Header brand --}}
                    <tr>
                        <td style="padding-bottom:32px;">
                            <span
                                style="font-family:monospace;font-size:11px;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;color:#7c3aed;">
                                FinTrack
                            </span>
                        </td>
                    </tr>

                    {{-- Card --}}
                    <tr>
                        <td style="background:#110f1e;border:1px solid #1e1b35;border-radius:16px;overflow:hidden;">

                            {{-- Top accent bar --}}
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="height:3px;background:linear-gradient(90deg,#7c3aed,#a78bfa,#7c3aed);">
                                    </td>
                                </tr>
                            </table>

                            {{-- Body --}}
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding:40px 40px 32px;">

                                        {{-- Icon --}}
                                        <table cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                                            <tr>
                                                <td
                                                    style="width:48px;height:48px;background:#1e1b35;border-radius:12px;text-align:center;vertical-align:middle;">
                                                    <span style="font-size:22px;line-height:48px;">🔑</span>
                                                </td>
                                            </tr>
                                        </table>

                                        {{-- Heading --}}
                                        <p
                                            style="margin:0 0 8px;font-size:20px;font-weight:700;color:#f0eeff;letter-spacing:-0.3px;">
                                            Reset password
                                        </p>
                                        <p style="margin:0 0 32px;font-size:14px;color:#eeedfe;line-height:1.6;">
                                            Halo <strong style="color:#c4b5fd;">{{ $user->name }}</strong>,
                                            kami menerima permintaan reset password untuk akunmu.
                                            Klik tombol di bawah untuk membuat password baru.
                                        </p>

                                        {{-- CTA Button --}}
                                        <table cellpadding="0" cellspacing="0" style="margin-bottom:32px;">
                                            <tr>
                                                <td style="background:#7c3aed;border-radius:10px;">
                                                    <a href="{{ url('/reset-password?token=' . $token . '&email=' . urlencode($user->email)) }}"
                                                        style="display:inline-block;padding:14px 32px;font-size:14px;font-weight:600;color:#ffffff;text-decoration:none;letter-spacing:0.01em;">
                                                        Reset Password →
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>

                                        {{-- URL fallback --}}
                                        <table width="100%" cellpadding="0" cellspacing="0"
                                            style="margin-bottom:32px;">
                                            <tr>
                                                <td
                                                    style="background:#0d0b1a;border:1px solid #8000f8;border-radius:8px;padding:14px 16px;">
                                                    <p
                                                        style="margin:0 0 4px;font-size:11px;font-family:monospace;letter-spacing:0.1em;text-transform:uppercase;color:#ffffff;">
                                                        Atau copy link berikut
                                                    </p>
                                                    <p
                                                        style="margin:0;font-size:12px;font-family:monospace;color:#b9abff;word-break:break-all;line-height:1.5;">
                                                        {{ url('/reset-password?token=' . $token . '&email=' . urlencode($user->email)) }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>

                                        {{-- Timer info --}}
                                        <table width="100%" cellpadding="0" cellspacing="0"
                                            style="margin-bottom:28px;">
                                            <tr>
                                                <td
                                                    style="background:#1a1030;border:1px solid #2a1f50;border-radius:8px;padding:12px 16px;">
                                                    <table cellpadding="0" cellspacing="0">
                                                        <tr>
                                                            <td style="padding-right:10px;vertical-align:middle;">
                                                                <span style="font-size:16px;">⏱</span>
                                                            </td>
                                                            <td style="font-size:13px;color:#8b7db5;line-height:1.5;">
                                                                Link ini kadaluarsa dalam <strong
                                                                    style="color:#c4b5fd;">60 menit</strong>.
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>

                                        {{-- Divider --}}
                                        <table width="100%" cellpadding="0" cellspacing="0"
                                            style="margin-bottom:24px;">
                                            <tr>
                                                <td style="height:1px;background:#1e1b35;"></td>
                                            </tr>
                                        </table>

                                        {{-- Security note --}}
                                        <p style="margin:0;font-size:12px;color:#ffffff;line-height:1.6;">
                                            Jika kamu tidak meminta reset password, abaikan email ini —
                                            akunmu tetap aman dan password tidak akan berubah.
                                        </p>

                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding-top:28px;text-align:center;">
                            <p
                                style="margin:0;font-family:monospace;font-size:11px;color:#ffffff;letter-spacing:0.08em;">
                                &copy; {{ date('Y') }} FinTrack &nbsp;·&nbsp; All rights reserved
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>

</html>
