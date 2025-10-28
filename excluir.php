<?php
session_start();
require_once 'config.php';

// Verificando se o código foi enviado via GET.
if (isset($_GET['codigo']) && !empty($_GET['codigo'])) {
    $codigo = mysqli_real_escape_string($conexao, $_GET['codigo']);

    // Usando a função para deletar os dados
    $sql = "DELETE FROM produtos WHERE codigo = ?";

    $stmt = mysqli_prepare($conexao, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $codigo);

        if (mysqli_stmt_execute($stmt)) {
            // Sucesso na exclusão
            $_SESSION['mensagem'] = [
                'tipo' => 'sucesso',
                'texto' => 'Produto excluído com sucesso!'
            ];
        } else {
            // Erro na exclusão
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Erro ao excluir produto: ' . mysqli_error($conexao)
            ];
        }
        mysqli_stmt_close($stmt);
    } else {
        // Erro na preparação da declaração
        $_SESSION['mensagem'] = [
            'tipo' => 'erro',
            'texto' => 'Erro na preparação da declaração de exclusão: ' . mysqli_error($conexao)
        ];
    }
} else {
    // Código do produto não informado
    $_SESSION['mensagem'] = [
        'tipo' => 'aviso',
        'texto' => 'Código do produto não informado para exclusão.'
    ];
}

mysqli_close($conexao);

header('Location: index.php');
exit();

?>
