<?php
require_once '../config.php';

if (isset($_GET['id_produto'])) {
    $id_produto = $conexao->real_escape_string($_GET['id_produto']);
    
    // Inicia transação para garantir integridade
    $conexao->begin_transaction();
    
    try {
        // Primeiro, verifica se existem registros relacionados no estoque
        $sql_check_estoque = "SELECT COUNT(*) as total FROM estoque WHERE id_produto = ?";
        $stmt_check = $conexao->prepare($sql_check_estoque);
        $stmt_check->bind_param("i", $id_produto);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $row_check = $result_check->fetch_assoc();
        $stmt_check->close();
        
        if ($row_check['total'] > 0) {
            // Se existem registros no estoque, não permite exclusão
            throw new Exception("Não é possível excluir o produto pois existem registros relacionados no estoque. Exclua primeiro os registros do estoque.");
        }
        
        // Exclui o produto (apenas se não houver registros relacionados)
        $sql_produto = "DELETE FROM produtos WHERE id_produto = ?";
        $stmt_produto = $conexao->prepare($sql_produto);
        $stmt_produto->bind_param("i", $id_produto);
        
        if ($stmt_produto->execute()) {
            $conexao->commit();
            echo "<script>
                alert('Produto excluído com sucesso!');
                window.location.href = '../index.php?pagina=produtos';
            </script>";
        } else {
            throw new Exception("Erro ao excluir produto: " . $stmt_produto->error);
        }
        
        $stmt_produto->close();
        
    } catch (Exception $e) {
        $conexao->rollback();
        echo "<script>
            alert('" . addslashes($e->getMessage()) . "');
            window.location.href = '../index.php?pagina=produtos';
        </script>";
    }
} else {
    echo "<script>
        alert('ID do produto não especificado!');
        window.location.href = '../index.php?pagina=produtos';
    </script>";
}

$conexao->close();
?>