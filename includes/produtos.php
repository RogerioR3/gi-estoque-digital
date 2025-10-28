<?php
require_once 'config.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Produtos</h2>
    <div>
        <a href="?pagina=adicionar_produto" class="btn btn-success me-2">Adicionar Produto</a>
        <a href="includes/gerar_relatorio_produtos.php" class="btn btn-secondary">Gerar Relatório</a>
    </div>
</div>

<div class="p-3 rounded" style="background-color: #f2f2f2;">
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-sm">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Quantidade</th>
                    <th>Marca</th>
                    <th>Valor Venda</th>
                    <th>Descrição</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM produtos";
                $res = $conexao->query($sql);
                if ($res->num_rows > 0) {
                    while ($linha = $res->fetch_assoc()) {
                        echo "<tr>
                                <td>{$linha['id_produto']}</td>
                                <td>{$linha['nome']}</td>
                                <td>{$linha['quantidade']}</td>
                                <td>{$linha['marca']}</td>
                                <td>R$ " . number_format($linha['valor_venda'], 2, ',', '.') . "</td>
                                <td>{$linha['descricao']}</td>
                                <td class='text-center'>
                                    <div class='btn-group' role='group'>
                                        <!-- Botão Editar -->
                                        <a href='?pagina=editar_produto&id_produto={$linha['id_produto']}' 
                                           class='btn btn-sm btn-outline-primary me-1' 
                                           title='Editar Produto'>
                                            <img src='Imagens/editar.png' alt='Editar' style='width: 16px; height: 16px;'>
                                        </a>
                                        <!-- Botão Excluir -->
                                        <button type='button' 
                                                class='btn btn-sm btn-outline-danger' 
                                                onclick='confirmarExclusao({$linha['id_produto']})'
                                                title='Excluir Produto'>
                                            <img src='Imagens/excluir.png' alt='Excluir' style='width: 16px; height: 16px;'>
                                        </button>
                                    </div>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>Nenhum produto encontrado.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Script para confirmação de exclusão -->
<script>
function confirmarExclusao(idProduto) {
    if (confirm('Tem certeza que deseja excluir este produto? Esta ação não pode ser desfeita.')) {
        window.location.href = 'includes/excluir_produto.php?id_produto=' + idProduto;
    }
}
</script>
