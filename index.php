<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

require_once 'config.php';

// Define a aba ativa
$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 'avisos';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GJ | Estoque Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/estilo.css">
    <style>
        body {
            background-color: #F0F8FF;
        }
        .top-bar {
            background-color: #444;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 1.3em;
            font-weight: bold;
        }
        .nav-bar {
            background-color: #ddd;
            padding: 10px;
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        .nav-bar a {
            text-decoration: none;
            padding: 10px 20px;
            color: #333;
            background-color: #e6e6e6;
            border-radius: 6px;
            font-weight: 500;
        }
        .nav-bar a.active {
            background-color: #bbb;
            color: black;
            font-weight: bold;
        }
        .content-area {
            padding: 30px;
        }
        footer {
            background-color: #f0f0f0;
            color: #333;
            padding: 10px 0;
            text-align: center;
            width: 100%;
            bottom: 0;
            margin-top: 30px;
        }
    </style>
</head>
<body>

    <div class="top-bar">
        Central Embalagens | Estoque Digital
    </div>

    <div class="nav-bar">
        <a href="?pagina=avisos" class="<?= $pagina == 'avisos' ? 'active' : '' ?>">Avisos</a>
        <a href="?pagina=produtos" class="<?= $pagina == 'produtos' ? 'active' : '' ?>">Produtos</a>
        <a href="?pagina=estoque" class="<?= $pagina == 'estoque' ? 'active' : '' ?>">Estoque</a>
        <a href="?pagina=historico" class="<?= $pagina == 'historico' ? 'active' : '' ?>">Histórico</a>
    </div>

    <div class="content-area">
        <?php
            $arquivo = "includes/" . $pagina . ".php";
            if (file_exists($arquivo)) {
                include($arquivo);
            } else {
                echo "<p>Página não encontrada.</p>";
            }
        ?>
    </div>

    <footer>
        <p>&copy; 2025 Central Embalagens | Estoque Digital</p>
    </footer>

</body>
</html>

