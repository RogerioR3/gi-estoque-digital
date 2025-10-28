<?php
require_once 'config.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Histórico de Vendas</h2>
    <div>
        <a href="?pagina=nova_venda" class="btn btn-success me-2">Nova Venda</a>
        <a href="includes/gerar_relatorio_historico.php" class="btn btn-secondary">Gerar Relatório</a>
    </div>
</div>

<div class="p-3 rounded" style="background-color: #f2f2f2;">
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-sm">
            <thead class="table-light">
                <tr>
                    <th>ID_Venda</th>
                    <th>Nome</th>
                    <th>Quantidade_Venda</th>
                    <th>Marca</th>
                    <th>Data_Compra</th>
                    <th>Valor_Compra</th>
                    <th>Valor_Venda</th>
                    <th>Data_Venda</th>
                    <th>Lucro</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM view_vendas_lucro";
                $res = $conexao->query($sql);
                if ($res->num_rows > 0) {
                    while ($linha = $res->fetch_assoc()) {
                        echo "<tr>
                                <td>{$linha['ID_Venda']}</td>
                                <td>{$linha['Nome']}</td>
                                <td>{$linha['Quantidade_Venda']}</td>
                                <td>{$linha['Marca']}</td>
                                <td>{$linha['Data_Compra']}</td>
                                <td>R$ " . number_format($linha['Valor_Compra'], 2, ',', '.') . "</td>
                                <td>R$ " . number_format($linha['Valor_Venda'], 2, ',', '.') . "</td>
                                <td>{$linha['Data_Venda']}</td>
                                <td>R$ " . number_format($linha['Lucro'], 2, ',', '.') . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='9' class='text-center'>Nenhuma venda registrada.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
