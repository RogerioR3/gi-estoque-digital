<?php
include 'config.php';

// Inicializar variáveis
$produtos = [];
$marcas = [];
$validades = [];
$id_produto_selecionado = null;
$produto_selecionado = '';
$marca_selecionada = '';
$validade_selecionada = '';

// Buscar todos os produtos para o primeiro select
$sql_produtos = "SELECT DISTINCT nome FROM produtos ORDER BY nome";
$result_produtos = $conexao->query($sql_produtos);
if ($result_produtos) {
    $produtos = $result_produtos->fetch_all(MYSQLI_ASSOC);
}

// Processar seleção do produto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['produto'])) {
    $produto_selecionado = $_POST['produto'];
    
    // Buscar marcas correspondentes ao produto selecionado
    $sql_marcas = "SELECT DISTINCT marca FROM produtos WHERE nome = ? ORDER BY marca";
    $stmt_marcas = $conexao->prepare($sql_marcas);
    $stmt_marcas->bind_param("s", $produto_selecionado);
    $stmt_marcas->execute();
    $result_marcas = $stmt_marcas->get_result();
    
    if ($result_marcas) {
        $marcas = $result_marcas->fetch_all(MYSQLI_ASSOC);
    }
    
    // Se uma marca foi selecionada, buscar o id_produto e validades
    if (isset($_POST['marca']) && !empty($_POST['marca'])) {
        $marca_selecionada = $_POST['marca'];
        
        // Buscar id_produto
        $sql_id_produto = "SELECT id_produto FROM produtos WHERE nome = ? AND marca = ?";
        $stmt_id_produto = $conexao->prepare($sql_id_produto);
        $stmt_id_produto->bind_param("ss", $produto_selecionado, $marca_selecionada);
        $stmt_id_produto->execute();
        $result_id_produto = $stmt_id_produto->get_result();
        
        if ($result_id_produto && $result_id_produto->num_rows > 0) {
            $row = $result_id_produto->fetch_assoc();
            $id_produto_selecionado = $row['id_produto'];
            
            // Buscar validades correspondentes ao id_produto
            $sql_validades = "SELECT DISTINCT validade FROM estoque WHERE id_produto = ? ORDER BY validade";
            $stmt_validades = $conexao->prepare($sql_validades);
            $stmt_validades->bind_param("i", $id_produto_selecionado);
            $stmt_validades->execute();
            $result_validades = $stmt_validades->get_result();
            
            if ($result_validades) {
                $validades = $result_validades->fetch_all(MYSQLI_ASSOC);
            }
            
            // Se uma validade foi selecionada
            if (isset($_POST['validade']) && !empty($_POST['validade'])) {
                $validade_selecionada = $_POST['validade'];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleção de Produto</title>
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
    <h2>Seleção de Produto</h2>
    
    <form method="POST" action="">
        <!-- Caixa de seleção para Produto -->
        <div class="form-group">
            <label for="produto">Produto:</label>
            <select name="produto" id="produto" onchange="this.form.submit()" required>
                <option value="">Selecione um produto</option>
                <?php foreach ($produtos as $produto): ?>
                    <option value="<?php echo htmlspecialchars($produto['nome']); ?>" 
                        <?php echo ($produto_selecionado == $produto['nome']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($produto['nome']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Caixa de seleção para Marca (apenas se produto foi selecionado) -->
        <?php if (!empty($produto_selecionado)): ?>
        <div class="form-group">
            <label for="marca">Marca:</label>
            <select name="marca" id="marca" onchange="this.form.submit()" required>
                <option value="">Selecione uma marca</option>
                <?php foreach ($marcas as $marca): ?>
                    <option value="<?php echo htmlspecialchars($marca['marca']); ?>" 
                        <?php echo ($marca_selecionada == $marca['marca']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($marca['marca']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>

        <!-- Caixa de seleção para Data de Validade (apenas se marca foi selecionada) -->
        <?php if (!empty($marca_selecionada) && !empty($id_produto_selecionado)): ?>
        <div class="form-group">
            <label for="validade">Data de Validade:</label>
            <select name="validade" id="validade" required>
                <option value="">Selecione uma data de validade</option>
                <?php foreach ($validades as $validade): ?>
                    <option value="<?php echo htmlspecialchars($validade['validade']); ?>" 
                        <?php echo ($validade_selecionada == $validade['validade']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($validade['validade']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <button type="submit">Confirmar Seleção</button>
        <?php endif; ?>
    </form>

    <!-- Exibir informações selecionadas -->
    <?php if (!empty($id_produto_selecionado)): ?>
    <div class="info">
        <h3>Informações Selecionadas:</h3>
        <p><strong>ID do Produto:</strong> <?php echo $id_produto_selecionado; ?></p>
        <p><strong>Produto:</strong> <?php echo htmlspecialchars($produto_selecionado); ?></p>
        <p><strong>Marca:</strong> <?php echo htmlspecialchars($marca_selecionada); ?></p>
        <?php if (!empty($validade_selecionada)): ?>
            <p><strong>Data de Validade Selecionada:</strong> <?php echo htmlspecialchars($validade_selecionada); ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</body>
</html>