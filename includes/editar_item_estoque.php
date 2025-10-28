<?php
include('config.php');

// Verifica se foi passado o id_estoque via GET
if (!isset($_GET['id_estoque'])) {
    echo "<script>alert('Item do estoque não especificado!'); window.location.href='?pagina=estoque';</script>";
    exit();
}

$id_estoque = $_GET['id_estoque'];

// Busca os dados do item do estoque no banco de dados
$sql = "SELECT e.*, p.nome, p.marca 
        FROM estoque e 
        JOIN produtos p ON e.id_produto = p.id_produto 
        WHERE e.id_estoque = '$id_estoque'";
$result = mysqli_query($conexao, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "<script>alert('Item do estoque não encontrado!'); window.location.href='?pagina=estoque';</script>";
    exit();
}

$item_estoque = mysqli_fetch_assoc($result);

// Busca todos os nomes de produtos para o dropdown
$nomes = explode(', ', mysqli_fetch_assoc(mysqli_query($conexao, "SELECT BuscarNomesDeProdutos() AS nomes"))['nomes']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Item do Estoque | GJ Estoque Digital</title>
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
        <h2>Editar Item do Estoque</h2>
        <form method="POST" onsubmit="return confirmarAtualizacao()">
            <input type="hidden" name="id_estoque" value="<?php echo $item_estoque['id_estoque']; ?>">
            
            <label>Nome:</label>
            <select id="nome" name="nome" required>
                <option value="">Selecione o produto</option>
                <?php 
                foreach ($nomes as $n) {
                    $selected = ($n == $item_estoque['nome']) ? 'selected' : '';
                    echo "<option value='$n' $selected>$n</option>";
                }
                ?>
            </select>

            <label>Marca:</label>
            <select id="marca" name="marca" required>
                <option value="<?php echo $item_estoque['marca']; ?>" selected><?php echo $item_estoque['marca']; ?></option>
            </select>

            <label>Quantidade:</label>
            <input type="number" name="quantidade" placeholder="Quantidade" value="<?php echo $item_estoque['quantidade_compra']; ?>" required>
            
            <label>Valor de Compra:</label>
            <input type="number" step="0.01" name="valor_compra" placeholder="Valor de Compra (R$)" value="<?php echo $item_estoque['valor_compra']; ?>" required>
            
            <label>Data de Validade:</label>
            <input type="date" name="validade" value="<?php echo $item_estoque['validade']; ?>" required>

            <button type="submit" name="atualizar" class="salvar">Atualizar</button>
            <button type="button" class="cancelar" onclick="cancelar()">Cancelar</button>
        </form>
    </div>
</div>

<script>
// Carrega as marcas quando a página é carregada
document.addEventListener('DOMContentLoaded', function() {
    const nomeSelecionado = document.getElementById('nome').value;
    if (nomeSelecionado) {
        carregarMarcas(nomeSelecionado);
    }
});

document.getElementById('nome').addEventListener('change', function() {
    const nome = this.value;
    carregarMarcas(nome);
});

function carregarMarcas(nome) {
    fetch(`includes/buscar_marcas.php?nome=${encodeURIComponent(nome)}`)
        .then(r => r.text())
        .then(html => {
            document.getElementById('marca').innerHTML = html;
            // Mantém a marca selecionada se ainda estiver disponível
            const marcaOriginal = '<?php echo $item_estoque['marca']; ?>';
            const options = document.getElementById('marca').options;
            for (let i = 0; i < options.length; i++) {
                if (options[i].value === marcaOriginal) {
                    options[i].selected = true;
                    break;
                }
            }
        });
}

function confirmarAtualizacao() {
    return confirm('Tem certeza que deseja atualizar os dados deste item do estoque?');
}

function cancelar() {
    if (confirm('Tem certeza que deseja cancelar a edição? Todas as alterações serão perdidas.')) {
        window.location.href = '?pagina=estoque';
    }
}
</script>

<?php
if (isset($_POST['atualizar'])) {
    $id_estoque = $_POST['id_estoque'];
    $nome = mysqli_real_escape_string($conexao, $_POST['nome']);
    $marca = mysqli_real_escape_string($conexao, $_POST['marca']);
    $quantidade = $_POST['quantidade'];
    $valor_compra = $_POST['valor_compra'];
    $validade = $_POST['validade'];

    if ($nome && $marca && $quantidade && $valor_compra && $validade) {
        // 1️⃣ Buscar id_produto
        $result = mysqli_query($conexao, "SELECT id_produto FROM produtos WHERE nome='$nome' AND marca='$marca' LIMIT 1");
        if ($row = mysqli_fetch_assoc($result)) {
            $id_produto = $row['id_produto'];

            // 2️⃣ Atualizar no estoque
            $sql = "UPDATE estoque SET 
                    id_produto = '$id_produto', 
                    quantidade_compra = '$quantidade', 
                    valor_compra = '$valor_compra', 
                    validade = '$validade' 
                    WHERE id_estoque = '$id_estoque'";

            if (mysqli_query($conexao, $sql)) {
                echo "<script>
                    alert('Item do estoque atualizado com sucesso!');
                    window.location.href = '?pagina=estoque';
                </script>";
            } else {
                echo "<script>
                    alert('Erro ao atualizar item do estoque: " . addslashes(mysqli_error($conexao)) . "');
                </script>";
            }
        } else {
            echo "<script>alert('Produto não encontrado.');</script>";
        }
    } else {
        echo "<script>alert('Por favor, preencha todos os campos!');</script>";
    }
}
?>
</body>
</html>
