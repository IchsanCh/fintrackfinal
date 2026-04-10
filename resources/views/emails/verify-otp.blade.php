<div style="font-family: Arial; max-width: 500px; margin: auto;">
    <h2>Kode Verifikasi</h2>

    <p>Halo {{ $user->name }},</p>

    <p>Gunakan kode berikut untuk verifikasi akun kamu:</p>

    <h1 style="letter-spacing: 5px; text-align: center;">
        {{ $otp }}
    </h1>

    <p>Kode ini berlaku selama 10 menit.</p>

    <p>Jangan berikan kode ini ke siapapun.</p>

    <br>

    <small>FinTrack System</small>
</div>
