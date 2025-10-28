<?php
require_once 'config.php';
?>

<h2 class="text-center mb-4">Avisos de Validade</h2>

<div class="p-3 mb-4 rounded" style="background-color: #f8d7da;">
    <h4 class="text-danger">Produtos Vencidos</h4>
    <div class="table-responsive">
        <table class="table table-light table-bordered table-sm">
            <thead class="table-danger">
                <tr>
                    <th>ID_Estoque</th>
                    <th>Nome</th>
                    <th>Quantidade_Estoque</th>
                    <th>Marca</th>
                    <th>Valor_Compra</th>
                    <th>Valor_Venda</th>
                    <th>Data_de_Validade</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM view_estoque_vencido";
                $res = $conexao->query($sql);
                if ($res->num_rows > 0) {
                    while ($linha = $res->fetch_assoc()) {
                        echo "<tr>
                                <td>{$linha['ID_Estoque']}</td>
                                <td>{$linha['Nome']}</td>
                                <td>{$linha['Quantidade_Estoque']}</td>
                                <td>{$linha['Marca']}</td>
                                <td>$" . number_format($linha['Valor_Compra'], 2, ',', '.') . "</td>
                                <td>$" . number_format($linha['Valor_Venda'], 2, ',', '.') . "</td>
                                <td>{$linha['Data_de_Validade']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>Nenhum produto vencido.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<div class="p-3 rounded" style="background-color: #fff3cd;">
    <h4 class="text-warning">Produtos com Validade Próxima</h4>
    <div class="table-responsive">
        <table class="table table-light table-bordered table-sm">
            <thead class="table-warning">
                <tr>
                    <th>ID_Estoque</th>
                    <th>Nome</th>
                    <th>Quantidade_Estoque</th>
                    <th>Marca</th>
                    <th>Valor_Compra</th>
                    <th>Valor_Venda</th>
                    <th>Data_de_Validade</th>
                    <th>Dias_Para_Vencer</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM view_estoque_validade_proxima";
                $res = $conexao->query($sql);
                if ($res->num_rows > 0) {
                    while ($linha = $res->fetch_assoc()) {
                        echo "<tr>
                                <td>{$linha['ID_Estoque']}</td>
                                <td>{$linha['Nome']}</td>
                                <td>{$linha['Quantidade_Estoque']}</td>
                                <td>{$linha['Marca']}</td>
                                <td>$" . number_format($linha['Valor_Compra'], 2, ',', '.') . "</td>
                                <td>$" . number_format($linha['Valor_Venda'], 2, ',', '.') . "</td>
                                <td>{$linha['Data_de_Validade']}</td>
                                <td>{$linha['Dias_Para_Vencer']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' class='text-center'>Nenhum produto próximo do vencimento.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

