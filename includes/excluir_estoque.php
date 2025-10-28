<?php
require_once '../config.php';

if (isset($_GET['id_estoque'])) {
    $id_estoque = $conexao->real_escape_string($_GET['id_estoque']);
    
    // Inicia transação para garantir integridade
    $conexao->begin_transaction();
    
    try {
        // Primeiro, verifica se existem registros relacionados em vendas
        $sql_check_vendas = "SELECT COUNT(*) as total FROM vendas WHERE id_estoque = ?";
        $stmt_check = $conexao->prepare($sql_check_vendas);
        $stmt_check->bind_param("i", $id_estoque);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $row_check = $result_check->fetch_assoc();
        $stmt_check->close();
        
        if ($row_check['total'] > 0) {
            // Se existem registros em vendas, não permite exclusão
            throw new Exception("Não é possível excluir o item do estoque pois existem vendas relacionadas. Exclua primeiro os registros de vendas.");
        }
        
        // Exclui o item do estoque (apenas se não houver registros relacionados em vendas)
        $sql_estoque = "DELETE FROM estoque WHERE id_estoque = ?";
        $stmt_estoque = $conexao->prepare($sql_estoque);
        $stmt_estoque->bind_param("i", $id_estoque);
        
        if ($stmt_estoque->execute()) {
            $conexao->commit();
            echo "<script>
                alert('Item do estoque excluído com sucesso!');
                window.location.href = '../index.php?pagina=estoque';
            </script>";
        } else {
            throw new Exception("Erro ao excluir item do estoque: " . $stmt_estoque->error);
        }
        
        $stmt_estoque->close();
        
    } catch (Exception $e) {
        $conexao->rollback();
        echo "<script>
            alert('" . addslashes($e->getMessage()) . "');
            window.location.href = '../index.php?pagina=estoque';
        </script>";
    }
} else {
    echo "<script>
        alert('ID do estoque não especificado!');
        window.location.href = '../index.php?pagina=estoque';
    </script>";
}

$conexao->close();
?>