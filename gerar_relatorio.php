<?php
ob_start(); // Inicia buffer de saída — impede envio precoce de dados

require_once 'config.php';
require_once 'TCPDF-main/tcpdf.php';

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Definir informações do documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sua Aplicação');
$pdf->SetTitle('GJ | EMBALAGENS - Estoque Digital');
$pdf->SetSubject('Lista de Produtos e Valor Total');

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Fonte padrão
$pdf->SetFont('helvetica', '', 11);

$pdf->AddPage();

// Conteúdo do relatório
$html = '<h1>Relatório de Produtos Cadastrados - GJ | EMBALAGENS</h1><br><br>';
$html .= '<table border="1">';
$html .= '<thead><tr><th>Código</th><th>Quantidade</th><th>Nome do Produto</th><th>Marca</th><th>Valor Unitário</th><th>Descrição</th><th>Valor Total</th></tr></thead>';
$html .= '<tbody>';

$sql = "SELECT codigo, quantidade, nome, marca, preco, descricao FROM produtos";
$resultado = mysqli_query($conexao, $sql);

$valorTotalGeral = 0;

while ($linha = mysqli_fetch_assoc($resultado)) {
    $precoUnitario = $linha['preco'];
    $quantidade = $linha['quantidade'];
    $precoTotalProduto = $precoUnitario * $quantidade;
    $valorTotalGeral += $precoTotalProduto;

    $html .= '<tr>';
    $html .= '<td>' . $linha['codigo'] . '</td>';
    $html .= '<td>' . $quantidade . '</td>';
    $html .= '<td>' . $linha['nome'] . '</td>';
    $html .= '<td>' . $linha['marca'] . '</td>';
    $html .= '<td>$' . number_format($precoUnitario, 2, ',', '.') . '</td>';
    $html .= '<td>' . $linha['descricao'] . '</td>';
    $html .= '<td>$' . number_format($precoTotalProduto, 2, ',', '.') . '</td>';
    $html .= '</tr>';
}

$html .= '</tbody></table><br>';

$html .= '<p style="text-align:right;"><strong>Valor Bruto de Todos os Produtos: $' . number_format($valorTotalGeral, 2, ',', '.') . '</strong></p>';

$pdf->writeHTML($html, true, false, true, false, '');

ob_end_clean(); // Limpa o buffer antes de gerar o PDF

$pdf->Output('relatorio_produtos.pdf', 'I');

exit; // Garante que nada mais será enviado

mysqli_close($conexao);
?>
