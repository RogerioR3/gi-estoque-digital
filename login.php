<?php
session_start();

// Se o usuário já estiver logado, vai direto para o sistema
if (isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit();
}

// Verifica login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // Usuário e senha válidos (pode futuramente vir do banco)
    $usuarioValido = 'GJEmbalagens';
    $senhaValida = 'embalagem3595';

    if ($usuario === $usuarioValido && $senha === $senhaValida) {
        $_SESSION['usuario'] = $usuario;
        header('Location: index.php'); // vai para o painel (Avisos)
        exit();
    } else {
        $erro = "Usuário ou senha incorretos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | GJ</title>

    <style>
        body{
            font-family: Arial, Helvetica, sans-serif;
            background-image: linear-gradient(45deg, white, blue);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        div{
            color: white;
            background-color: rgb(0, 0, 0, 0.8);
            padding: 40px;
            border-radius: 15px;
            width: 80%;
            max-width: 400px;
            box-sizing: border-box;
        }

        input{
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            box-sizing: border-box;
        }

        button{
            color: white;
            background-color: blue;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            box-sizing: border-box;
        }

        button:hover{
            background-color: rgb(40, 40, 242);
        }

        .erro {
            color: #ff7777;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            padding: 8px;
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
        }

        @media (max-width: 600px) {
            div {
                padding: 30px;
                width: 95%;
                max-width: none;
            }

            h1 {
                font-size: 1.5rem;
                margin-bottom: 20px;
            }

            input {
                padding: 15px;
                font-size: 1.1rem;
                margin-bottom: 20px;
            }

            button {
                padding: 15px;
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <div>
        <h1>Login - GJ | Estoque Digital</h1>

        <?php if (!empty($erro)) echo "<div class='erro'>$erro</div>"; ?>

        <form method="POST" action="">
            <input type="text" name="usuario" placeholder="Usuário" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Acessar</button>
        </form>
    </div>
</body>
</html>
