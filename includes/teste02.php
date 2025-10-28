<?php
// Inclui o arquivo de configuração do banco de dados
require_once 'config.php';

// Inicializa variáveis
$produtos = [];
$marcas = [];
$validades = [];
$id_produto = '';
$id_estoque = '';
$quantidade = '';
$mensagem = '';

// Buscar todos os nomes de produtos
try {
    $sql = "SELECT DISTINCT nome FROM produtos ORDER BY nome";
    $result = $conexao->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $produtos[] = $row['nome'];
        }
    }
} catch (Exception $e) {
    $mensagem = "Erro ao carregar produtos: " . $e->getMessage();
}

// Processar seleção do produto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['produto'])) {
    $produto_selecionado = $_POST['produto'];
    
    // Buscar marcas para o produto selecionado
    try {
        $sql = "SELECT DISTINCT marca FROM produtos WHERE nome = ? ORDER BY marca";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("s", $produto_selecionado);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $marcas = [];
        while ($row = $result->fetch_assoc()) {
            $marcas[] = $row['marca'];
        }
        
        // Se também foi selecionada uma marca, buscar id_produto
        if (isset($_POST['marca']) && !empty($_POST['marca'])) {
            $marca_selecionada = $_POST['marca'];
            
            $sql = "SELECT id_produto FROM produtos WHERE nome = ? AND marca = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("ss", $produto_selecionado, $marca_selecionada);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $id_produto = $row['id_produto'];
                
                // Buscar validades para o id_produto
                $sql = "SELECT e.id_estoque, e.validade, e.quantidade_compra 
                        FROM estoque e 
                        WHERE e.id_produto = ? 
                        ORDER BY e.validade";
                $stmt = $conexao->prepare($sql);
                $stmt->bind_param("i", $id_produto);
                $stmt->execute();
                $result = $stmt->get_result();
                
                $validades = [];
                while ($row_val = $result->fetch_assoc()) {
                    $validades[] = [
                        'id_estoque' => $row_val['id_estoque'],
                        'validade' => $row_val['validade'],
                        'quantidade_compra' => $row_val['quantidade_compra']
                    ];
                }
            }
        }
        
        // Se foi selecionada uma validade, buscar id_estoque
        if (isset($_POST['validade']) && !empty($_POST['validade'])) {
            $validade_selecionada = $_POST['validade'];
            
            foreach ($validades as $val) {
                if ($val['validade'] == $validade_selecionada) {
                    $id_estoque = $val['id_estoque'];
                    break;
                }
            }
        }
        
        // Se foi enviada uma quantidade
        if (isset($_POST['quantidade'])) {
            $quantidade = $_POST['quantidade'];
        }
        
    } catch (Exception $e) {
        $mensagem = "Erro ao processar seleção: " . $e->getMessage();
    }
}

// Processar cancelar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancelar'])) {
    echo "<script>
        if (confirm('Deseja realmente cancelar? Todas as informações não salvas serão perdidas.')) {
            window.location.href = '?pagina=historico';
        }
    </script>";
}

// Processar salvar venda
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar'])) {
    if (isset($_POST['id_estoque']) && isset($_POST['quantidade']) && !empty($_POST['quantidade'])) {
        $id_estoque_salvar = $_POST['id_estoque'];
        $quantidade_salvar = $_POST['quantidade'];
        
        try {
            // Inserir na tabela vendas
            $sql = "INSERT INTO vendas (id_estoque, quantidade_venda, data_venda) VALUES (?, ?, CURDATE())";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("ii", $id_estoque_salvar, $quantidade_salvar);
            
            if ($stmt->execute()) {
                /*$mensagem = "Venda registrada com sucesso!";
                // Limpar campos após salvar
                $id_produto = '';
                $id_estoque = '';
                $quantidade = '';*/
                echo "<script>
                    alert('Venda registrada com sucesso!');
                    window.location.href = '?pagina=historico';
                </script>";
                //window.location.href = '?pagina=historico';
            } else {
                $mensagem = "Erro ao registrar venda: " . $stmt->error;
            }
        } catch (Exception $e) {
            $mensagem = "Erro ao salvar venda: " . $e->getMessage();
        }
    } else {
        $mensagem = "Preencha todos os campos obrigatórios!";
    }
}


?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Venda</title>
    <style>
        .container { display: flex; padding: 30px; }
        .left { flex: 1; display: flex; justify-content: center; align-items: center; }
        .left img { max-width: 80%; border-radius: 20px; }
        .right { flex: 1; background-color: #f5f5f5; padding: 25px; border-radius: 15px; }
        select, input { width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc; }
        button { padding: 10px 15px; border: none; border-radius: 8px; cursor: pointer; margin-right: 10px; }
        .salvar { background-color: #28a745; color: white; }
        .cancelar { background-color: #dc3545; color: white; }
        .mensagem { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .sucesso { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .erro { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="left">
            <img src="Imagens/caracteristicas.png" alt="Características">
        </div>
        
        <div class="right">
            <h2>Registrar Nova Venda</h2>
            
            <?php if (!empty($mensagem)): ?>
                <div class="mensagem <?php echo strpos($mensagem, 'sucesso') !== false ? 'sucesso' : 'erro'; ?>">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="formVenda">
                <!-- Campo Produto -->
                <label for="produto">Produto:</label>
                <select name="produto" id="produto" required onchange="this.form.submit()">
                    <option value="">Selecione um produto</option>
                    <?php foreach ($produtos as $prod): ?>
                        <option value="<?php echo htmlspecialchars($prod); ?>" 
                            <?php echo (isset($_POST['produto']) && $_POST['produto'] == $prod) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($prod); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <!-- Campo Marca -->
                <label for="marca">Marca:</label>
                <select name="marca" id="marca" <?php echo empty($marcas) ? 'disabled' : ''; ?> onchange="this.form.submit()">
                    <option value="">Selecione uma marca</option>
                    <?php foreach ($marcas as $marca): ?>
                        <option value="<?php echo htmlspecialchars($marca); ?>" 
                            <?php echo (isset($_POST['marca']) && $_POST['marca'] == $marca) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($marca); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <!-- Campo Data Validade -->
                <label for="validade">Data Validade:</label>
                <select name="validade" id="validade" <?php echo empty($validades) ? 'disabled' : ''; ?> onchange="this.form.submit()">
                    <option value="">Selecione uma validade</option>
                    <?php foreach ($validades as $val): ?>
                        <option value="<?php echo htmlspecialchars($val['validade']); ?>" 
                            <?php echo (isset($_POST['validade']) && $_POST['validade'] == $val['validade']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($val['validade']) . ' (Estoque: ' . $val['quantidade_compra'] . ')'; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <!-- Campo Quantidade -->
                <label for="quantidade">Quantidade:</label>
                <input type="number" name="quantidade" id="quantidade" 
                    value="<?php echo htmlspecialchars($quantidade); ?>" 
                    min="1" required 
                    onchange="atualizarQuantidade()">
                
                <!-- Campos hidden para armazenar IDs -->
                <input type="hidden" name="id_produto" value="<?php echo htmlspecialchars($id_produto); ?>">
                <input type="hidden" name="id_estoque" value="<?php echo htmlspecialchars($id_estoque); ?>">
                
                <!-- Botões -->
                <div style="margin-top: 20px;">
                    <button type="submit" name="salvar" class="salvar" onclick="return confirmarSalvar()">Salvar</button>
                    <button type="submit" name="cancelar" class="cancelar">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function atualizarQuantidade() {
            // Esta função permite alterar a quantidade sem recarregar a página
            // O valor é mantido no formulário e enviado quando outros campos mudam
        }
        
        function confirmarSalvar() {
            const quantidade = document.getElementById('quantidade').value;
            const produto = document.getElementById('produto').value;
            const marca = document.getElementById('marca').value;
            const validade = document.getElementById('validade').value;
            
            if (!produto || !marca || !validade || !quantidade) {
                alert('Preencha todos os campos antes de salvar!');
                return false;
            }
            
            return confirm('Confirma o registro desta venda?');
        }
        
        // Prevenir envio do formulário ao alterar quantidade (para não recarregar a página)
        document.getElementById('quantidade').addEventListener('change', function(e) {
            e.stopPropagation();
        });
    </script>
</body>
</html>