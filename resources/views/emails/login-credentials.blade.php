<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #2ECC71 0%, #27AE60 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .content {
            padding: 40px;
            color: #333333;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .credentials-box {
            background-color: #f0f8f4;
            border-left: 4px solid #2ECC71;
            padding: 20px;
            margin: 30px 0;
            border-radius: 4px;
        }
        .credentials-box h3 {
            color: #2ECC71;
            margin-top: 0;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .credential-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .credential-item:last-child {
            border-bottom: none;
        }
        .credential-label {
            font-weight: 600;
            color: #555555;
            flex: 1;
        }
        .credential-value {
            background-color: #ffffff;
            padding: 8px 12px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: #2ECC71;
            font-weight: bold;
            border: 1px solid #ddd;
            flex: 1.5;
            text-align: right;
            word-break: break-all;
        }
        .info-text {
            font-size: 14px;
            color: #666666;
            line-height: 1.6;
            margin: 20px 0;
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 4px;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #999999;
            border-top: 1px solid #eeeeee;
        }
        .button {
            display: inline-block;
            background-color: #2ECC71;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🎓 Suivi Académique</h1>
            <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">Bienvenue sur la plateforme</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                <strong>Bonjour {{ $personnel->nom_pers }} {{ $personnel->prenom_pers }},</strong>
                <p>
                    Votre compte a été créé avec succès sur la plateforme <strong>Suivi Académique</strong>.
                    Ci-dessous sont vos identifiants de connexion.
                </p>
            </div>

            <!-- Credentials Box -->
            <div class="credentials-box">
                <h3>📋 Vos identifiants</h3>
                <div class="credential-item">
                    <span class="credential-label">Email :</span>
                    <span class="credential-value">{{ $personnel->login_pers }}</span>
                </div>
                <div class="credential-item">
                    <span class="credential-label">Mot de passe :</span>
                    <span class="credential-value">{{ $password }}</span>
                </div>
                <div class="credential-item">
                    <span class="credential-label">Type de compte :</span>
                    <span class="credential-value">{{ ucfirst($personnel->type_pers) }}</span>
                </div>
            </div>

            <!-- Warning Message -->
            <div class="info-text">
                <strong>⚠️ Sécurité importante :</strong>
                <br>
                • Conservez vos identifiants en lieu sûr<br>
                • Ne partagez jamais votre mot de passe<br>
                • Nous vous recommandons de changer votre mot de passe lors de votre première connexion<br>
                • Ne supprimez jamais cet email
            </div>

            <p style="text-align: center;">
                <a href="http://localhost:4200" class="button">🔗 Accéder à la plateforme</a>
            </p>

            <p style="font-size: 14px; color: #666666; line-height: 1.6; margin-top: 30px;">
                Si vous n'avez pas demandé la création de ce compte ou si vous avez des questions,
                <strong>contactez immédiatement le responsable</strong>.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                © {{ date('Y') }} Suivi Académique. Tous droits réservés.<br>
                Email: rayanngokeng1@gmail.com
            </p>
        </div>
    </div>
</body>
</html>
