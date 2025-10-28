<?php
include('config.php');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Adicionar Produto | GJ Estoque Digital</title>
<link rel="stylesheet" href="style.css">
<style>
.container {
    display: flex;
    padding: 30px;
}
.left {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
}
.left img {
    max-width: 80%;
    border-radius: 20px;
}
.right {
    flex: 1;
    background-color: #f5f5f5;
    padding: 25px;
    border-radius: 15px;
}
input, textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 5px;
    border: 1px solid #ccc;
}
button {
    padding: 10px 15px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    margin-right: 10px;
}
.salvar { background-color: #28a745; color: white; }
.cancelar { background-color: #dc3545; color: white; }
</style>
</head>
<body>


<div class="container">
    <div class="left">
        <img src="Imagens/caracteristicas.png" alt="Características">
    </div>

    <div class="right">
        <h2>Adicionar Novo Produto</h2>
        <form id="formProduto" method="POST">
            <label>Nome:</label>
            <input type="text" name="nome" placeholder="Nome" required>
            <label>Marca:</label>
            <input type="text" name="marca" placeholder="Marca" required>
            <label>Quantidade:</label>
            <input type="number" name="quantidade" placeholder="Quantidade" required>
            <label>Valor de Venda:</label>
            <input type="number" step="0.01" name="valor_venda" placeholder="Valor de Venda (R$)" required>
            <label>Descrição:</label>
            <textarea name="descricao" placeholder="Descrição" rows="3" required></textarea>

            <button type="submit" name="salvar" class="salvar">Salvar</button>
            <button type="button" class="cancelar" onclick="cancelar()">Cancelar</button>
        </form>
    </div>
</div>

<script>
function cancelar() {
    if (confirm('Tem certeza que deseja cancelar o cadastro?')) {
        window.location.href = '?pagina=produtos';
    }
}
</script>

<?php
if (isset($_POST['salvar'])) {
    $nome = $_POST['nome'];
    $marca = $_POST['marca'];
    $quantidade = $_POST['quantidade'];
    $valor = $_POST['valor_venda'];
    $descricao = $_POST['descricao'];

    if ($nome && $marca && $quantidade && $valor && $descricao) {
        $sql = "INSERT INTO produtos (nome, marca, quantidade, valor_venda, descricao)
                VALUES ('$nome', '$marca', '$quantidade', '$valor', '$descricao')";
        if (mysqli_query($conexao, $sql)) {
            echo "<script>alert('Produto cadastrado com sucesso!'); window.location.href='?pagina=produtos';</script>";
        } else {
            echo "<script>alert('Erro ao cadastrar produto.');</script>";
        }
    }
}
?>
</body>
</html>
