<?php
include 'php/conexao.php';

/* TOTAL VENDIDO HOJE */

$sql_vendas = "
SELECT SUM(total) AS total_hoje
FROM vendas
WHERE DATE(data) = CURDATE()
";

$result_vendas = mysqli_query($conn, $sql_vendas);

$dados_vendas = mysqli_fetch_assoc($result_vendas);

$total_hoje = $dados_vendas['total_hoje'];

if($total_hoje == null){
    $total_hoje = 0;
}
$sql_produtos = "SELECT COUNT(*) AS total_produtos FROM produtos";

$result_produtos = mysqli_query($conn, $sql_produtos);

$dados_produtos = mysqli_fetch_assoc($result_produtos);

$total_produtos = $dados_produtos['total_produtos'];

$sql_funcionarios = "
SELECT COUNT(*) AS total_funcionarios
FROM funcionarios
";

$result_funcionarios = mysqli_query($conn, $sql_funcionarios);

$dados_funcionarios = mysqli_fetch_assoc($result_funcionarios);

$total_funcionarios = $dados_funcionarios['total_funcionarios'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>PDV</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- CSS -->
  <link rel="stylesheet" href="css/estilo.css">

  <style>

    body{
      margin:0;
      background:#f4f6fb;
      font-family:'Segoe UI', sans-serif;
    }
  

    /* SIDEBAR */

    .sidebar{
      width:240px;
      height:100vh;
      position:fixed;

      background:linear-gradient(180deg,#111827,#1f2937);

      padding:30px 20px;

      border-radius:0 20px 20px 0;

      box-shadow:5px 0 20px rgba(0,0,0,0.1);
    }

    .logo{
      color:white;
      font-weight:bold;
      margin-bottom:40px;
      font-size:32px;
      font-size: 20px;
    }

    .sidebar a{
      display:flex;
      align-items:center;
      gap:12px;

      color:#d1d5db;

      padding:14px 16px;

      text-decoration:none;

      border-radius:14px;

      margin-bottom:10px;

      transition:0.3s;
      font-size:16px;
    }

    .sidebar a:hover{
      background:#374151;
      color:white;
      transform:translateX(5px);
    }

    .sidebar i{
      font-size:20px;
    }

    /* CONTEÚDO */

    .content{
      margin-left:260px;
      padding:35px;
    }

    .titulo{
      font-size:38px;
      font-weight:bold;
      color:#111827;
      margin-bottom:5px;
    }

    .subtitulo{
      color:#6b7280;
      margin-bottom:35px;
    }

    /* CARDS */

    .card-dashboard{
      background:white;

      border-radius:22px;

      padding:25px;

      display:flex;
      justify-content:space-between;
      align-items:center;

      box-shadow:0 10px 25px rgba(0,0,0,0.08);

      transition:0.3s;

      height:140px;
    }

    .card-dashboard:hover{
      transform:translateY(-6px);
    }

    .card-dashboard h6{
      color:#6b7280;
      margin-bottom:10px;
    }

    .card-dashboard h3{
      font-size:30px;
      font-weight:bold;
      margin:0;
    }

    .icon-card{
      width:70px;
      height:70px;

      display:flex;
      align-items:center;
      justify-content:center;

      border-radius:20px;

      font-size:32px;

      color:white;
    }

    .bg-vendas{
      background:linear-gradient(135deg,#4f46e5,#6366f1);
    }

    .bg-produtos{
      background:linear-gradient(135deg,#f59e0b,#fbbf24);
    }

    .bg-funcionarios{
      background:linear-gradient(135deg,#10b981,#34d399);
    }

    /* PAINEL */

    .painel{
      background:white;
      border-radius:22px;
      padding:25px;
      margin-top:35px;

      box-shadow:0 10px 25px rgba(0,0,0,0.08);
    }

    .painel h4{
      margin-bottom:20px;
      font-weight:bold;
    }

    table{
      margin-top:15px;
    }


  </style>
</head>

<body > 

  <!-- SIDEBAR -->

  <div class="sidebar">

    <h2 class="logo"  class="fonte">Container do Queijo </h2>

    <a href="pdv.html">
      <i class="bi bi-cart"></i>
      PDV
    </a>

    <a href="produtos.html">
      <i class="bi bi-box-seam"></i>
      Produtos
    </a>

    <a href="funcionarios.html">
      <i class="bi bi-people"></i>
      Funcionários
    </a>

  </div>

  <!-- CONTEÚDO -->

  <div class="content">

    <h1 class="titulo">Dashboard</h1>
    <p class="subtitulo">Bem-vindo ao sistema!</p>

    <!-- CARDS -->

    <div class="row g-4">

      <div class="col-md-4">

        <div class="card-dashboard">

          <div>
            <h6>Vendas Hoje</h6>
            <h3>
    R$ <?= number_format($total_hoje, 2, ',', '.') ?>
</h3>
          </div>

          <div class="icon-card bg-vendas">
            <i class="bi bi-cash-stack"></i>
          </div>

        </div>

      </div>

      <div class="col-md-4">

        <div class="card-dashboard">

          <div>
            <h6>Produtos</h6>
            <h3><?= $total_produtos ?></h3>
          </div>

          <div class="icon-card bg-produtos">
            <i class="bi bi-box-seam"></i>
          </div>

        </div>

      </div>

      <div class="col-md-4">

        <div class="card-dashboard">

          <div>
            <h6>Funcionários</h6>
            <h3><?= $total_funcionarios ?></h3>
          </div>

          <div class="icon-card bg-funcionarios">
            <i class="bi bi-people"></i>
          </div>

        </div>

      </div>

    </div>

    <!-- PAINEL -->

    <div class="painel">

      <h4>Últimas Vendas</h4>

      <table class="table table-hover">

        <thead>
          <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Valor</th>
            <th>Data</th>
          </tr>
        </thead>

        <tbody>

<?php

$sql_ultimas = "
SELECT *
FROM vendas
ORDER BY id DESC
LIMIT 5
";

$result_ultimas = mysqli_query($conn, $sql_ultimas);

while($venda = mysqli_fetch_assoc($result_ultimas)){

?>

<tr>

<td>#<?= $venda['id'] ?></td>

<td>Cliente não registrado</td>

<td>
    R$ <?= number_format($venda['total'], 2, ',', '.') ?>
</td>

<td>
    <?= date('d/m/Y', strtotime($venda['data'])) ?>
</td>

</tr>

<?php } ?>

</tbody>

      </table>

    </div>

  </div>

<script src="js/script.js"></script>
</body>
</html>