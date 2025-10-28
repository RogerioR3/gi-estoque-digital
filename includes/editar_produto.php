<?php
include('config.php');

// Verifica se foi passado o id_produto via GET
if (!isset($_GET['id_produto'])) {
    echo "<script>alert('Produto não especificado!'); window.location.href='?pagina=produtos';</script>";
    exit();
}

$id_produto = $_GET['id_produto'];

// Busca os dados do produto no banco de dados
$sql = "SELECT * FROM produtos WHERE id_produto = '$id_produto'";
$result = mysqli_query($conexao, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "<script>alert('Produto não encontrado!'); window.location.href='?pagina=produtos';</script>";
    exit();
}

$produto = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Produto | GJ Estoque Digital</title>
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
        <h2>Editar Produto</h2>
        <form id="formProduto" method="POST" onsubmit="return confirmarAtualizacao()">
            <input type="hidden" name="id_produto" value="<?php echo $produto['id_produto']; ?>">
            
            <label>Nome:</label>
            <input type="text" name="nome" placeholder="Nome" value="<?php echo htmlspecialchars($produto['nome']); ?>" required>
            
            <label>Marca:</label>
            <input type="text" name="marca" placeholder="Marca" value="<?php echo htmlspecialchars($produto['marca']); ?>" required>
            
            <label>Quantidade:</label>
            <input type="number" name="quantidade" placeholder="Quantidade" value="<?php echo $produto['quantidade']; ?>" required>
            
            <label>Valor de Venda:</label>
            <input type="number" step="0.01" name="valor_venda" placeholder="Valor de Venda (R$)" value="<?php echo $produto['valor_venda']; ?>" required>
            
            <label>Descrição:</label>
            <textarea name="descricao" placeholder="Descrição" rows="3" required><?php echo htmlspecialchars($produto['descricao']); ?></textarea>

            <button type="submit" name="atualizar" class="salvar">Atualizar</button>
            <button type="button" class="cancelar" onclick="cancelar()">Cancelar</button>
        </form>
    </div>
</div>

<script>
function confirmarAtualizacao() {
    return confirm('Tem certeza que deseja atualizar os dados deste produto?');
}

function cancelar() {
    if (confirm('Tem certeza que deseja cancelar a edição? Todas as alterações serão perdidas.')) {
        window.location.href = '?pagina=produtos';
    }
}
</script>

<?php
if (isset($_POST['atualizar'])) {
    $id_produto = $_POST['id_produto'];
    $nome = mysqli_real_escape_string($conexao, $_POST['nome']);
    $marca = mysqli_real_escape_string($conexao, $_POST['marca']);
    $quantidade = $_POST['quantidade'];
    $valor_venda = $_POST['valor_venda'];
    $descricao = mysqli_real_escape_string($conexao, $_POST['descricao']);

    if ($nome && $marca && $quantidade && $valor_venda && $descricao) {
        $sql = "UPDATE produtos SET 
                nome = '$nome', 
                marca = '$marca', 
                quantidade = '$quantidade', 
                valor_venda = '$valor_venda', 
                descricao = '$descricao' 
                WHERE id_produto = '$id_produto'";
        
        if (mysqli_query($conexao, $sql)) {
            echo "<script>
                alert('Produto atualizado com sucesso!');
                window.location.href = '?pagina=produtos';
            </script>";
        } else {
            echo "<script>
                alert('Erro ao atualizar produto: " . addslashes(mysqli_error($conexao)) . "');
            </script>";
        }
    } else {
        echo "<script>alert('Por favor, preencha todos os campos!');</script>";
    }
}
?>
</body>
</html>
