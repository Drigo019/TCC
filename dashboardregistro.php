<?php

include 'conexao.php';

$sql = "SELECT SUM(total) AS total_vendas FROM vendas";

$resultado = mysqli_query($conexao, $sql);

$dados = mysqli_fetch_assoc($resultado);

$total = $dados['total_vendas'];

?>