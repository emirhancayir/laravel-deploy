<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Şifre Sıfırlama</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #ff9900; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0;">{{ config('app.name') }}</h1>
    </div>

    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px;">
        <h2 style="color: #333;">Merhaba {{ $user->ad_soyad }},</h2>

        <p>Hesabınız için bir şifre sıfırlama talebi aldık.</p>

        <p>Şifrenizi sıfırlamak için aşağıdaki butona tıklayın:</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $resetLink }}"
               style="background: #ff9900;
                      color: white;
                      padding: 15px 30px;
                      text-decoration: none;
                      border-radius: 8px;
                      font-weight: bold;
                      display: inline-block;">
                Şifremi Sıfırla
            </a>
        </div>

        <p style="color: #666; font-size: 14px;">
            Bu link 60 dakika süreyle geçerlidir.
        </p>

        <p style="color: #666; font-size: 14px;">
            Eğer bu talebi siz yapmadıysanız, bu e-postayı görmezden gelebilirsiniz.
        </p>

        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">

        <p style="color: #999; font-size: 12px; text-align: center;">
            Bu e-posta {{ config('app.name') }} tarafından otomatik olarak gönderilmiştir.<br>
            Lütfen bu e-postayı yanıtlamayın.
        </p>
    </div>
</body>
</html>
