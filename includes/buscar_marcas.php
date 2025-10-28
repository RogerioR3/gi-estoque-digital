<?php
include('../config.php'); // Ajuste o caminho aqui

if (isset($_GET['nome']) && $_GET['nome'] !== '') {
    $nome = mysqli_real_escape_string($conexao, $_GET['nome']);
    $result = mysqli_query($conexao, "SELECT BuscarMarcasPorNome('$nome') AS marcas");

    if ($result && $row = mysqli_fetch_assoc($result)) {
        $marcas = explode(', ', $row['marcas']);
        foreach ($marcas as $m) echo "<option value='$m'>$m</option>";
    } else {
        echo "<option value=''>Nenhuma marca encontrada</option>";
    }
} else {
    echo "<option value=''>Selecione o nome primeiro</option>";
}
?>
