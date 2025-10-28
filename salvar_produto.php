<?php
session_start(); // Inicia a sessão no topo do arquivo
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['salvar'])) {
    // 1. Coleta e sanitiza os dados
    $quantidade = mysqli_real_escape_string($conexao, $_POST['quantidade']);
    $nome = mysqli_real_escape_string($conexao, $_POST['nome']);
    $marca = mysqli_real_escape_string($conexao, $_POST['marca']);
    $descricao = mysqli_real_escape_string($conexao, $_POST['descricao']);

    $preco = $_POST['preco'];
    if (!is_numeric($preco)) {
        $_SESSION['mensagem'] = [
            'tipo' => 'erro',
            'texto' => 'Erro: O campo \'Valor Unitário\' deve conter apenas números.'
        ];
        mysqli_close($conexao);
        header('Location: index.php'); // Redireciona de volta para a listagem
        exit();
    }
    $preco = (float)$preco;

    $quantidade = $_POST['quantidade'];
    if (!filter_var($quantidade, FILTER_VALIDATE_INT)) {
        $_SESSION['mensagem'] = [
            'tipo' => 'erro',
            'texto' => 'Erro: O campo \'Quantidade\' deve conter apenas números inteiros.'
        ];
        mysqli_close($conexao);
        header('Location: index.php'); // Redireciona de volta para a listagem
        exit();
    }
    $quantidade = (int)$quantidade;

    if (isset($_POST['codigo_editar']) && !empty($_POST['codigo_editar'])) {
        $codigo_editar = mysqli_real_escape_string($conexao, $_POST['codigo_editar']);
        
        $sql_atualizar = "UPDATE produtos SET quantidade = ?, nome = ?, marca = ?, preco = ?, descricao = ? WHERE codigo = ?";
        
        $stmt = mysqli_prepare($conexao, $sql_atualizar);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "issdsi", $quantidade, $nome, $marca, $preco, $descricao, $codigo_editar);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['mensagem'] = [
                    'tipo' => 'sucesso',
                    'texto' => 'Produto atualizado com sucesso!'
                ];
            } else {
                $_SESSION['mensagem'] = [
                    'tipo' => 'erro',
                    'texto' => 'Erro ao atualizar o produto: ' . mysqli_error($conexao)
                ];
            }
            mysqli_stmt_close($stmt);
        } else {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Erro na preparação da declaração de atualização: ' . mysqli_error($conexao)
            ];
        }

    } else {
        $sql_inserir = "INSERT INTO produtos (quantidade, nome, marca, preco, descricao) VALUES (?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conexao, $sql_inserir);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "issds", $quantidade, $nome, $marca, $preco, $descricao);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['mensagem'] = [
                    'tipo' => 'sucesso',
                    'texto' => 'Produto cadastrado com sucesso!'
                ];
            } else {
                $_SESSION['mensagem'] = [
                    'tipo' => 'erro',
                    'texto' => 'Erro ao cadastrar o produto: ' . mysqli_error($conexao)
                ];
            }
            mysqli_stmt_close($stmt);
        } else {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Erro na preparação da declaração de inserção: ' . mysqli_error($conexao)
            ];
        }
    }
} else {
    // Se a requisição não for POST ou 'salvar' não estiver setado (acesso direto ao salvar_produto.php)
    $_SESSION['mensagem'] = [
        'tipo' => 'aviso',
        'texto' => 'Acesso inválido à página de salvamento.'
    ];
}

mysqli_close($conexao);

// Redireciona para a página principal (ex: index.php) onde a mensagem será exibida
header('Location: index.php');
exit();

?>
