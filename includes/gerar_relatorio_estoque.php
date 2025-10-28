<?php
ob_start(); // Inicia buffer de saída — impede envio precoce de dados

require_once __DIR__ . '/../config.php'; // Sobe um nível para config.php
require_once __DIR__ . '/../TCPDF-main/tcpdf.php'; // Sobe um nível para TCPDF

$pdf = new TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

// Definir informações do documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistema de Estoque');
$pdf->SetTitle('Relatório de Estoque - GJ | EMBALAGENS');
$pdf->SetSubject('Relatório de Estoque');

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage();

// Buscar dados do banco
$conexao = require __DIR__ . '/../config.php';
$query = "SELECT * FROM view_estoque_detalhado ORDER BY Data_de_Validade, Nome";
$resultado = $conexao->query($query);

// Conteúdo do relatório
$html = '<h1>Relatório de Estoque Detalhado - GJ | EMBALAGENS</h1>';
$html .= '<p><strong>Data do Relatório:</strong> ' . date('d/m/Y H:i:s') . '</p><br>';

$html .= '<table border="1" cellpadding="4" style="width:100%; border-collapse:collapse;">';
$html .= '<thead>
            <tr style="background-color:#f2f2f2;">
                <th style="width:8%">ID Estoque</th>
                <th style="width:20%">Nome</th>
                <th style="width:8%">Qtd</th>
                <th style="width:12%">Marca</th>
                <th style="width:12%">Data Compra</th>
                <th style="width:12%">Valor Compra</th>
                <th style="width:12%">Data Validade</th>
                <th style="width:8%">Status</th>
            </tr>
          </thead>';
$html .= '<tbody>';

$totalItens = 0;
$totalInvestimento = 0;
$hoje = new DateTime();
$vencidos = 0;
$proximos = 0;

if($resultado && $resultado->num_rows > 0) {
    while($row = $resultado->fetch_assoc()) {
        $dataValidade = new DateTime($row['Data_de_Validade']);
        $diasParaVencer = $hoje->diff($dataValidade)->days;
        
        if($dataValidade < $hoje) {
            $status = '<span style="color:red; font-weight:bold;">VENCIDO</span>';
            $vencidos++;
        } elseif($diasParaVencer <= 7) {
            $status = '<span style="color:orange; font-weight:bold;">PRÓXIMO</span>';
            $proximos++;
        } else {
            $status = '<span style="color:green; font-weight:bold;">OK</span>';
        }
        
        $html .= '<tr>';
        $html .= '<td style="width:8%; text-align:center;">' . $row['ID_Estoque'] . '</td>';
        $html .= '<td style="width:20%">' . htmlspecialchars($row['Nome']) . '</td>';
        $html .= '<td style="width:8%; text-align:center;">' . $row['Quantidade_Estoque'] . '</td>';
        $html .= '<td style="width:12%">' . htmlspecialchars($row['Marca']) . '</td>';
        $html .= '<td style="width:12%; text-align:center;">' . date('d/m/Y', strtotime($row['Data_Compra'])) . '</td>';
        $html .= '<td style="width:12%; text-align:right;">R$ ' . number_format($row['Valor_Compra'], 2, ',', '.') . '</td>';
        $html .= '<td style="width:12%; text-align:center;">' . date('d/m/Y', strtotime($row['Data_de_Validade'])) . '</td>';
        $html .= '<td style="width:8%; text-align:center;">' . $status . '</td>';
        $html .= '</tr>';
        
        $totalItens += $row['Quantidade_Estoque'];
        $totalInvestimento += ($row['Valor_Compra'] * $row['Quantidade_Estoque']);
    }
} else {
    $html .= '<tr><td colspan="8" style="text-align:center;">Nenhum item em estoque</td></tr>';
}

$html .= '</tbody></table><br>';

// Resumo
$html .= '<div style="border:1px solid #ccc; padding:10px; background-color:#f9f9f9;">';
$html .= '<h3>Resumo do Estoque</h3>';
$html .= '<p><strong>Total de Itens em Estoque:</strong> ' . $totalItens . '</p>';
$html .= '<p><strong>Total Investido:</strong> R$ ' . number_format($totalInvestimento, 2, ',', '.') . '</p>';
$html .= '<p><strong>Itens Vencidos:</strong> <span style="color:red;">' . $vencidos . '</span></p>';
$html .= '<p><strong>Itens com Validade Próxima (até 7 dias):</strong> <span style="color:orange;">' . $proximos . '</span></p>';
$html .= '</div>';

$pdf->writeHTML($html, true, false, true, false, '');

ob_end_clean(); // Limpa o buffer antes de gerar o PDF

$pdf->Output('relatorio_estoque_' . date('Y-m-d') . '.pdf', 'I');

exit; // Garante que nada mais será enviado
?>