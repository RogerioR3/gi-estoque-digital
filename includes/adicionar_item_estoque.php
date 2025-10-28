<?php
include('config.php');
$nomes = explode(', ', mysqli_fetch_assoc(mysqli_query($conexao, "SELECT BuscarNomesDeProdutos() AS nomes"))['nomes']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Adicionar Item ao Estoque | GJ Estoque Digital</title>
<link rel="stylesheet" href="style.css">
<style>
.container { display: flex; padding: 30px; }
.left { flex: 1; display: flex; justify-content: center; align-items: center; }
.left img { max-width: 80%; border-radius: 20px; }
.right { flex: 1; background-color: #f5f5f5; padding: 25px; border-radius: 15px; }
select, input { width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc; }
button { padding: 10px 15px; border: none; border-radius: 8px; cursor: pointer; margin-right: 10px; }
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
        <h2>Adicionar Item ao Estoque</h2>
        <form method="POST">
            <label>Nome:</label>
            <select id="nome" name="nome" required>
                <option value="">Selecione o produto</option>
                <?php foreach ($nomes as $n) echo "<option value='$n'>$n</option>"; ?>
            </select>

            <label>Marca:</label>
            <select id="marca" name="marca" required>
                <option value="">Selecione a marca</option>
            </select>

            <label>Quantidade:</label>
            <input type="number" name="quantidade" placeholder="Quantidade" required>
            <label>Valor de Compra:</label>
            <input type="number" step="0.01" name="valor_compra" placeholder="Valor de Compra (R$)" required>
            <label>Data de Validade:</label>
            <input type="date" name="validade" required>

            <button type="submit" name="salvar" class="salvar">Salvar</button>
            <button type="button" class="cancelar" onclick="cancelar()">Cancelar</button>
        </form>
    </div>
</div>

<script>
document.getElementById('nome').addEventListener('change', function() {
    const nome = this.value;
    fetch(`includes/buscar_marcas.php?nome=${encodeURIComponent(nome)}`)
        .then(r => r.text())
        .then(html => document.getElementById('marca').innerHTML = html);
});

function cancelar() {
    if (confirm('Deseja cancelar a adição do item?')) {
        window.location.href = '?pagina=estoque';
    }
}
</script>

<?php
if (isset($_POST['salvar'])) {
    $nome = $_POST['nome'];
    $marca = $_POST['marca'];
    $quantidade = $_POST['quantidade'];
    $valor = $_POST['valor_compra'];
    $validade = $_POST['validade'];

    if ($nome && $marca && $quantidade && $valor && $validade) {
        // 1️⃣ Buscar id_produto
        $result = mysqli_query($conexao, "SELECT id_produto FROM produtos WHERE nome='$nome' AND marca='$marca' LIMIT 1");
        if ($row = mysqli_fetch_assoc($result)) {
            $id_produto = $row['id_produto'];

            // 2️⃣ Inserir no estoque
            $sql = "INSERT INTO estoque (id_produto, quantidade_compra, valor_compra, validade)
                    VALUES ('$id_produto', '$quantidade', '$valor', '$validade')";

            if (mysqli_query($conexao, $sql)) {
                echo "<script>alert('Item adicionado ao estoque com sucesso!'); window.location.href='?pagina=estoque';</script>";
            } else {
                echo "<script>alert('Erro ao adicionar item ao estoque.');</script>";
            }
        } else {
            echo "<script>alert('Produto não encontrado.');</script>";
        }
    }
}
?>
</body>
</html>
