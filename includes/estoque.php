<?php
require_once 'config.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Estoque</h2>
    <div>
        <a href="?pagina=adicionar_item_estoque" class="btn btn-success me-2">Adicionar Ítem</a>
        <a href="includes/gerar_relatorio_estoque.php" class="btn btn-secondary">Gerar Relatório</a>
    </div>
</div>

<div class="p-3 rounded" style="background-color: #f2f2f2;">
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-sm">
            <thead class="table-light">
                <tr>
                    <th>ID_Estoque</th>
                    <th>Nome</th>
                    <th>Quantidade_Estoque</th>
                    <th>Marca</th>
                    <th>Data_Compra</th>
                    <th>Valor_Compra</th>
                    <th>Data_de_Validade</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM view_estoque_detalhado";
                $res = $conexao->query($sql);
                if ($res->num_rows > 0) {
                    while ($linha = $res->fetch_assoc()) {
                        echo "<tr>
                                <td>{$linha['ID_Estoque']}</td>
                                <td>{$linha['Nome']}</td>
                                <td>{$linha['Quantidade_Estoque']}</td>
                                <td>{$linha['Marca']}</td>
                                <td>{$linha['Data_Compra']}</td>
                                <td>R$ " . number_format($linha['Valor_Compra'], 2, ',', '.') . "</td>
                                <td>{$linha['Data_de_Validade']}</td>
                                <td class='text-center'>
                                    <div class='btn-group' role='group'>
                                        <!-- Botão Editar -->
                                        <a href='?pagina=editar_item_estoque&id_estoque={$linha['ID_Estoque']}' 
                                           class='btn btn-sm btn-outline-primary me-1' 
                                           title='Editar Item'>
                                            <img src='Imagens/editar.png' alt='Editar' style='width: 16px; height: 16px;'>
                                        </a>
                                        <!-- Botão Excluir -->
                                        <button type='button' 
                                                class='btn btn-sm btn-outline-danger' 
                                                onclick='confirmarExclusaoEstoque({$linha['ID_Estoque']})'
                                                title='Excluir Item'>
                                            <img src='Imagens/excluir.png' alt='Excluir' style='width: 16px; height: 16px;'>
                                        </button>
                                    </div>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' class='text-center'>Nenhum produto em estoque.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Script para confirmação de exclusão -->
<script>
function confirmarExclusaoEstoque(idEstoque) {
    if (confirm('Tem certeza que deseja excluir este item do estoque? Esta ação não pode ser desfeita.')) {
        window.location.href = 'includes/excluir_estoque.php?id_estoque=' + idEstoque;
    }
}
</script>
