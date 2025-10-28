<?php
ob_start(); // Inicia buffer de saída — impede envio precoce de dados

require_once __DIR__ . '/../config.php'; // Sobe um nível para config.php
require_once __DIR__ . '/../TCPDF-main/tcpdf.php'; // Sobe um nível para TCPDF

$pdf = new TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

// Definir informações do documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistema de Estoque');
$pdf->SetTitle('Relatório de Histórico de Vendas - GJ | EMBALAGENS');
$pdf->SetSubject('Relatório de Vendas');

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage();

// Buscar dados do banco
$conexao = require __DIR__ . '/../config.php';
$query = "SELECT * FROM view_vendas_lucro ORDER BY Data_Venda DESC, ID_Venda DESC";
$resultado = $conexao->query($query);

// Conteúdo do relatório
$html = '<h1>Relatório de Histórico de Vendas - GJ | EMBALAGENS</h1>';
$html .= '<p><strong>Data do Relatório:</strong> ' . date('d/m/Y H:i:s') . '</p><br>';

$html .= '<table border="1" cellpadding="4" style="width:100%; border-collapse:collapse;">';
$html .= '<thead>
            <tr style="background-color:#f2f2f2;">
                <th style="width:6%">ID Venda</th>
                <th style="width:18%">Nome</th>
                <th style="width:6%">Qtd</th>
                <th style="width:10%">Marca</th>
                <th style="width:10%">Data Compra</th>
                <th style="width:10%">Valor Compra</th>
                <th style="width:10%">Valor Venda</th>
                <th style="width:10%">Data Venda</th>
                <th style="width:10%">Lucro</th>
            </tr>
          </thead>';
$html .= '<tbody>';

$totalLucro = 0;

if($resultado && $resultado->num_rows > 0) {
    while($row = $resultado->fetch_assoc()) {
        $html .= '<tr>';
        $html .= '<td style="width:6%; text-align:center;">' . $row['ID_Venda'] . '</td>';
        $html .= '<td style="width:18%">' . htmlspecialchars($row['Nome']) . '</td>';
        $html .= '<td style="width:6%; text-align:center;">' . $row['Quantidade_Venda'] . '</td>';
        $html .= '<td style="width:10%">' . htmlspecialchars($row['Marca']) . '</td>';
        $html .= '<td style="width:10%; text-align:center;">' . date('d/m/Y', strtotime($row['Data_Compra'])) . '</td>';
        $html .= '<td style="width:10%; text-align:right;">R$ ' . number_format($row['Valor_Compra'], 2, ',', '.') . '</td>';
        $html .= '<td style="width:10%; text-align:right;">R$ ' . number_format($row['Valor_Venda'], 2, ',', '.') . '</td>';
        $html .= '<td style="width:10%; text-align:center;">' . date('d/m/Y', strtotime($row['Data_Venda'])) . '</td>';
        $html .= '<td style="width:10%; text-align:right; font-weight:bold;">R$ ' . number_format($row['Lucro'], 2, ',', '.') . '</td>';
        $html .= '</tr>';
        
        $totalLucro += $row['Lucro'];
    }
} else {
    $html .= '<tr><td colspan="9" style="text-align:center;">Nenhum registro encontrado</td></tr>';
}

$html .= '</tbody></table><br>';

$html .= '<p style="text-align:right; font-size:14px; font-weight:bold;">Total de Lucro: R$ ' . number_format($totalLucro, 2, ',', '.') . '</p>';

$pdf->writeHTML($html, true, false, true, false, '');

ob_end_clean(); // Limpa o buffer antes de gerar o PDF

$pdf->Output('relatorio_historico_vendas_' . date('Y-m-d') . '.pdf', 'I');

exit; // Garante que nada mais será enviado
?>