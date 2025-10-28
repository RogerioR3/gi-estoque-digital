
<?php

// Conexão com o banco de dados    
$dbHost = 'localhost';
$dbUsername = 'root';
$dbPassword = "";
$dbName = 'gj_estoque';

$conexao = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

// // Checar se a conexão deu certo **DEU CERTO**
/*
if($conexao->connect_errno)
{
    echo "Erro";
}
else
{
    echo "Conexão efetuada com sucesso!";
}
// */

return $conexao;

?>