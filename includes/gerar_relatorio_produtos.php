<?php
ob_start(); // Inicia buffer de saída — impede envio precoce de dados

require_once __DIR__ . '/../config.php'; // Sobe um nível para config.php
require_once __DIR__ . '/../TCPDF-main/tcpdf.php'; // Sobe um nível para TCPDF

$pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

// Definir informações do documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistema de Estoque');
$pdf->SetTitle('Relatório de Produtos - GJ | EMBALAGENS');
$pdf->SetSubject('Relatório de Produtos');

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage();

// Buscar dados do banco
$conexao = require __DIR__ . '/../config.php';
$query = "SELECT * FROM view_produtos_completos ORDER BY Nome, Marca";
$resultado = $conexao->query($query);

// Conteúdo do relatório
$html = '<h1>Relatório de Produtos - GJ | EMBALAGENS</h1>';
$html .= '<p><strong>Data do Relatório:</strong> ' . date('d/m/Y H:i:s') . '</p><br>';

$html .= '<table border="1" cellpadding="4" style="width:100%; border-collapse:collapse;">';
$html .= '<thead>
            <tr style="background-color:#f2f2f2;">
                <th style="width:8%">ID</th>
                <th style="width:22%">Nome</th>
                <th style="width:10%">Qtd</th>
                <th style="width:15%">Marca</th>
                <th style="width:15%">Valor Venda</th>
                <th style="width:30%">Descrição</th>
            </tr>
          </thead>';
$html .= '<tbody>';

$totalProdutos = 0;

if($resultado && $resultado->num_rows > 0) {
    while($row = $resultado->fetch_assoc()) {
        $html .= '<tr>';
        $html .= '<td style="width:8%; text-align:center;">' . $row['ID_Produto'] . '</td>';
        $html .= '<td style="width:22%">' . htmlspecialchars($row['Nome']) . '</td>';
        $html .= '<td style="width:10%; text-align:center;">' . $row['Quantidade_Produto'] . '</td>';
        $html .= '<td style="width:15%">' . htmlspecialchars($row['Marca']) . '</td>';
        $html .= '<td style="width:15%; text-align:right;">R$ ' . number_format($row['Valor_Venda'], 2, ',', '.') . '</td>';
        $html .= '<td style="width:30%">' . htmlspecialchars(substr($row['Descricao'], 0, 80)) . '</td>';
        $html .= '</tr>';
        
        $totalProdutos += $row['Quantidade_Produto'];
    }
} else {
    $html .= '<tr><td colspan="6" style="text-align:center;">Nenhum produto cadastrado</td></tr>';
}

$html .= '</tbody></table><br>';

$html .= '<p style="text-align:right; font-size:14px; font-weight:bold;">Total de Produtos em Estoque: ' . $totalProdutos . '</p>';

$pdf->writeHTML($html, true, false, true, false, '');

ob_end_clean(); // Limpa o buffer antes de gerar o PDF

$pdf->Output('relatorio_produtos_' . date('Y-m-d') . '.pdf', 'I');

exit; // Garante que nada mais será enviado
?>