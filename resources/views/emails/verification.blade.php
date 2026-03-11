<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4F46E5; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
        .button { display: inline-block; background: #4F46E5; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        <div class="content">
            <h2>Merhaba {{ $user->ad }},</h2>
            <p>{{ config('app.name') }} ailesine hoş geldiniz!</p>
            <p>Hesabınızı aktifleştirmek için aşağıdaki butona tıklayın:</p>
            <p style="text-align: center;">
                <a href="{{ $dogrulamaLinki }}" class="button">E-postamı Doğrula</a>
            </p>
            <p>Veya aşağıdaki linki tarayıcınıza kopyalayın:</p>
            <p style="word-break: break-all; background: #eee; padding: 10px; border-radius: 4px;">
                {{ $dogrulamaLinki }}
            </p>
            <p><strong>Not:</strong> Bu link 24 saat içinde geçerliliğini yitirecektir.</p>
            <p>Eğer bu hesabı siz oluşturmadıysanız, bu e-postayı görmezden gelebilirsiniz.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Tüm hakları saklıdır.</p>
        </div>
    </div>
</body>
</html>
