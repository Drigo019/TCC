
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar senha - Container do Queijo</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #29251e, #66502a);
        }

        .cartao-recuperacao {
            width: 100%;
            max-width: 440px;
            padding: 35px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, .25);
        }

        .icone {
            width: 65px;
            height: 65px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #fff3cd;
            color: #987000;
            font-size: 30px;
        }

        h1 {
            text-align: center;
            font-size: 25px;
            font-weight: 700;
            color: #30291d;
        }

        .descricao {
            text-align: center;
            color: #777;
            font-size: 14px;
            line-height: 1.6;
            margin: 15px 0 25px;
        }

        label {
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control {
            padding: 12px;
            border-radius: 10px;
        }

        .btn-enviar {
            width: 100%;
            padding: 12px;
            margin-top: 22px;
            border: none;
            border-radius: 10px;
            background: #a47700;
            color: white;
            font-weight: 700;
        }

        .btn-enviar:hover {
            background: #805c00;
        }

        .voltar {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #8a680b;
            text-decoration: none;
            font-size: 14px;
        }

        .voltar:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <main class="cartao-recuperacao">
        <div class="icone">🔐</div>

        <h1>Esqueceu sua senha?</h1>

        <p class="descricao">
            Informe o CPF cadastrado na sua conta.
            Enviaremos as instruções para o e-mail associado a ela.
        </p>

        <form action="../solicitar_recuperacao.php" method="post">
            <label for="cpf">CPF</label>

            <input
                type="text"
                name="cpf"
                id="cpf"
                class="form-control"
                placeholder="000.000.000-00"
                maxlength="14"
                inputmode="numeric"
                autocomplete="off"
                required
            >

            <button type="submit" class="btn-enviar">
                Recuperar senha
            </button>
        </form>

        <a href="inicio.php" class="voltar">
            ← Voltar para o login
        </a>
    </main>

    <script>
        document.getElementById('cpf').addEventListener('input', function () {
            let cpf = this.value.replace(/\D/g, '').slice(0, 11);

            cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
            cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
            cpf = cpf.replace(/(\d{3})(\d{1,2})$/, '$1-$2');

            this.value = cpf;
        });
    </script>
</body>
</html>
