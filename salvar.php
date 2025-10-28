<?php

require_once 'config.php';

// Função para sanitizar/limpar os dados recebidos
function sanitizarDados($conexao, $dado) {
    return mysqli_real_escape_string($conexao, trim($dado));
}

// Recebendo e sanitizando/limpando os dados do formulário
$quantidade = sanitizarDados($conexao, $_POST['quantidade'] ?? '');
$nome = sanitizarDados($conexao, $_POST['nome'] ?? '');
$marca = sanitizarDados($conexao, $_POST['marca'] ?? '');
$preco = str_replace(',', '.', sanitizarDados($conexao, $_POST['preco'] ?? ''));
$descricao = sanitizarDados($conexao, $_POST['descricao'] ?? '');

// Validando se os campos obrigatórios estão sendo preenchidos
if (empty($quantidade) || empty($nome) || empty($marca) || empty($preco) || empty($descricao)) {
    echo "<strong style='color: red;'>Erro: Por favor, preencha todos os campos do formulário.</strong>";
    exit; // Interrompe a execução do script se houver campos vazios
}

// Validação do preço (certifica-se de que é um número)
if (!is_numeric($preco)) {
    echo "<strong style='color: red;'>Erro: O preço deve ser um valor numérico.</strong>";
    exit;
}

// Preparando a função para inserir os dados no banco
$sql = "INSERT INTO produtos (codigo, quantidade, nome, marca, preco, descricao) VALUES (NULL, $quantidade, '$nome', '$marca', $preco, '$descricao')";

// Executando a função
if (mysqli_query($conexao, $sql)) {
    echo "<strong style='color: green;'>Cadastrado com sucesso!</strong>";
} else {
    echo "<strong style='color: red;'>Erro ao cadastrar:</strong> " . mysqli_error($conexao);
}

//Fechando a conexão
mysqli_close($conexao);

?>