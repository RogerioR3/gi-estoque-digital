<?php
include('../config.php');

$nome = mysqli_real_escape_string($conexao, $_GET['nome']);
$marca = mysqli_real_escape_string($conexao, $_GET['marca']);

echo "<!-- DEBUG: Buscando por nome='$nome', marca='$marca' -->";

// Buscar o id_produto baseado no nome e marca
$sql_produto = "SELECT id_produto FROM produtos WHERE nome = '$nome' AND marca = '$marca'";
$result_produto = mysqli_query($conexao, $sql_produto);

if (!$result_produto) {
    echo "<!-- DEBUG: Erro na query do produto: " . mysqli_error($conexao) . " -->";
    echo "<option value=''>Erro no banco de dados</option>";
    exit;
}

if (mysqli_num_rows($result_produto) === 0) {
    echo "<!-- DEBUG: Nenhum produto encontrado com esses parâmetros -->";
    echo "<option value=''>Produto não encontrado</option>";
    exit;
}

$produto = mysqli_fetch_assoc($result_produto);
$id_produto = $produto['id_produto'];
echo "<!-- DEBUG: id_produto encontrado: $id_produto -->";

// Buscar estoques com quantidade disponível
$sql = "
SELECT 
    e.id_estoque, 
    e.validade,
    (e.quantidade_compra - IFNULL((
        SELECT SUM(v.quantidade_venda) 
        FROM vendas v 
        WHERE v.id_estoque = e.id_estoque
    ), 0)) AS estoque_disponivel
FROM estoque e
WHERE e.id_produto = '$id_produto'
HAVING estoque_disponivel > 0
ORDER BY e.validade ASC
";

echo "<!-- DEBUG: Query estoque: $sql -->";

$result = mysqli_query($conexao, $sql);

if (!$result) {
    echo "<!-- DEBUG: Erro na query do estoque: " . mysqli_error($conexao) . " -->";
    echo "<option value=''>Erro: " . mysqli_error($conexao) . "</option>";
    exit;
}

echo "<!-- DEBUG: " . mysqli_num_rows($result) . " registros encontrados no estoque -->";

if (mysqli_num_rows($result) > 0) {
    echo "<option value=''>Selecione a validade</option>";
    while ($row = mysqli_fetch_assoc($result)) {
        $id_estoque = $row['id_estoque'];
        $validade = $row['validade'];
        $quantidade = $row['estoque_disponivel'];
        echo "<!-- DEBUG: Option - id_estoque: $id_estoque, validade: $validade, qtd: $quantidade -->";
        echo "<option value='$id_estoque'>$validade (Qtd: $quantidade)</option>";
    }
} else {
    echo "<option value=''>Nenhuma validade encontrada</option>";
}
?>