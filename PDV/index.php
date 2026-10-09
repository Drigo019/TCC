<?php
include 'conexao.php';

/* TOTAL VENDIDO HOJE */

$sql_vendas = "
SELECT SUM(valor) AS total_hoje
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
    

@font-face {
        font-family: RopaSans;
        src: url(fontes/RopaSans-Regular.ttf);
      }

      .tabela-vendas {
    max-height: 400px;
    overflow-y: auto;
    overflow-x: hidden;
    border-radius: 12px;
}

.tabela-vendas table {
    margin-top: 0;
}

.tabela-vendas thead th {
    position: sticky;
    top: 0;
    background: white;
    z-index: 2;
}

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

      background:linear-gradient(black);

      padding:30px 20px;

      border-radius:0 20px 20px 0;

      box-shadow:5px 0 20px rgba(0,0,0,0.1);

      transition: transform 0.3s ease;
    }

    .logo{
      color:white;
      font-weight:bold;
      margin-bottom:40px;
      font-size:32px;
      font-size: 20px;
      font-family: 'RopaSans'
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

    /* =========================================
   ABA ATIVA
========================================= */

.sidebar a.ativo {
    background: #374151;
    color: white;

    transform: scale(1.08);

    position: relative;
    z-index: 10;

    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.25);

    margin-left: 3px;
    margin-right: -3px;
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

    /* =========================================
   BOTÃO DA SIDEBAR
========================================= */

.botao-sidebar {

position: absolute;

top: 20px;

right: -18px;

width: 36px;

height: 36px;

border: none;

border-radius: 50%;

background: #212529;

color: white;

display: flex;

align-items: center;

justify-content: center;

cursor: pointer;

box-shadow: 0 4px 12px rgba(0,0,0,0.25);

transition: 0.3s;

z-index: 1001;
}


.botao-sidebar:hover {

transform: scale(1.1);

background: #343a40;

}


/* =========================================
SIDEBAR ESCONDIDA
========================================= */

.sidebar.escondida {

transform: translateX(-100%);

}


/* =========================================
CONTEÚDO QUANDO SIDEBAR ESTÁ ABERTA
========================================= */

.pdv-container {

margin-left: 260px;

transition: margin-left 0.3s ease;

}


/* =========================================
CONTEÚDO QUANDO SIDEBAR ESTÁ FECHADA
========================================= */

.pdv-container.sidebar-fechada {

margin-left: 20px;

}


/* =========================================
SETA QUANDO SIDEBAR ESTÁ FECHADA
========================================= */

.sidebar.escondida .botao-sidebar {

right: -54px;

}


.sidebar.escondida .botao-sidebar i {

transform: rotate(180deg);

}


  </style>
</head>

<body > 

  <!-- SIDEBAR -->

  <div class="sidebar">

    <img id="logo" src="../imagens/logo.jpeg" style="height: 100px; width: 180px; margin-left: 0px; margin-right: 0px; margin-top: -20px;">

    <a href="index.php" class="ativo">
    <i class="bi bi-house"></i>
    Dashboard
</a>
<button
    type="button"
    class="botao-sidebar"
    onclick="alternarSidebar()"
    title="Ocultar menu"
>
    <i class="bi bi-chevron-left"></i>
</button>

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

    <a href="estoque.php">
  <i class="bi bi-boxes"></i>
  Estoque
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

      <div class="tabela-vendas">

    <table class="table table-hover">

        <thead>
            <tr>

                <th>ID</th>

                <th>Cliente</th>

                <th>Valor</th>

                <th>Tipo de Venda</th>

                <th>Data</th>

            </tr>
        </thead>


        <tbody>

            <?php

            $sql_ultimas = "
            SELECT *
            FROM vendas
            ORDER BY idVendas DESC
            ";

            $result_ultimas =
                mysqli_query(
                    $conn,
                    $sql_ultimas
                );

            while (
                $venda =
                mysqli_fetch_assoc(
                    $result_ultimas
                )
            ) {

            ?>

            <tr>

                <td>
                    #<?= $venda['idVendas'] ?>
                </td>

                <td>
                    Cliente não registrado
                </td>

                <td>
                    R$
                    <?= number_format(
                        $venda['valor'],
                        2,
                        ',',
                        '.'
                    ) ?>
                </td>


                <td>

                <?php
$forma = $venda['formaDePagamento'] ?? '';

switch ($forma) {
    case 'Pix':
        echo '<span class="badge bg-success">
                <i class="bi bi-qr-code"></i> Pix
              </span>';
        break;

    case 'Dinheiro':
        echo '<span class="badge bg-warning text-dark">
                <i class="bi bi-cash-stack"></i> Dinheiro
              </span>';
        break;

    case 'Crediario':
        echo '<span class="badge bg-secondary">
                <i class="bi bi-journal-text"></i> Crediário
              </span>';
        break;

    case 'Debito':
        echo '<span class="badge bg-primary">
                <i class="bi bi-credit-card"></i> Débito
              </span>';
        break;

    case 'Credito':
        echo '<span class="badge bg-info text-dark">
                <i class="bi bi-credit-card-2-front"></i> Crédito
              </span>';
        break;

    default:
        echo htmlspecialchars($forma);
        break;
}
?>

                </td>


                <td>
                    <?= date(
                        'd/m/Y',
                        strtotime(
                            $venda['data']
                        )
                    ) ?>
                </td>

            </tr>

            <?php } ?>

        </tbody>

    </table>

</div>

    </div>

  </div>

<script src="js/script.js"></script>

<script>

function alternarSidebar() {

    const sidebar =
        document.querySelector(".sidebar");

    const conteudo =
        document.querySelector(".pdv-container");

    const botao =
        document.querySelector(".botao-sidebar");

    sidebar.classList.toggle("escondida");

    conteudo.classList.toggle("sidebar-fechada");


    const escondida =
        sidebar.classList.contains("escondida");


    if (escondida) {

        botao.title =
            "Mostrar menu";

    }

    else {

        botao.title =
            "Ocultar menu";

    }

}

</script>
</body>
</html>