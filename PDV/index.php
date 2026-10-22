
<?php
include 'conexao.php';


/* MARCAR CREDIÁRIO COMO PAGO */
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['marcar_pago'])
) {
    $idVendaPagar = (int) $_POST['marcar_pago'];

    if ($idVendaPagar > 0) {
        $sqlPagar = "
            UPDATE vendas
            SET statusCrediario = 'Pago',
                dataPagamento = CURDATE()
            WHERE idVendas = ?
              AND formaDePagamento = 'Crediario'
        ";

        $stmtPagar = $conn->prepare($sqlPagar);

        if (!$stmtPagar) {
            die('Erro ao preparar atualização: ' . $conn->error);
        }

        $stmtPagar->bind_param('i', $idVendaPagar);

        if (!$stmtPagar->execute()) {
            die('Erro ao atualizar pagamento: ' . $stmtPagar->error);
        }

        $linhasAtualizadas = $stmtPagar->affected_rows;
        $stmtPagar->close();

        if ($linhasAtualizadas === 0) {
            // Verifica se a venda existe e já está paga.
            $verificar = $conn->prepare("
                SELECT statusCrediario
                FROM vendas
                WHERE idVendas = ?
                  AND formaDePagamento = 'Crediario'
            ");

            $verificar->bind_param('i', $idVendaPagar);
            $verificar->execute();
            $resultadoVerificacao = $verificar->get_result();
            $vendaVerificada = $resultadoVerificacao->fetch_assoc();
            $verificar->close();

            if (!$vendaVerificada) {
                die('Não foi encontrada uma venda no crediário com esse ID.');
            }

            if ($vendaVerificada['statusCrediario'] !== 'Pago') {
                die(
                    'A venda foi encontrada, mas o status não foi atualizado. ' .
                    'Status atual: ' .
                    htmlspecialchars(
                        (string) $vendaVerificada['statusCrediario'],
                        ENT_QUOTES,
                        'UTF-8'
                    )
                );
            }
        }
    }

    header('Location: index.php');
    exit;
}


/* TOTAL VENDIDO HOJE */
$sql_vendas = "
    SELECT COALESCE(SUM(valor), 0) AS total_hoje
    FROM vendas
    WHERE DATE(data) = CURDATE()
";

$result_vendas = mysqli_query($conn, $sql_vendas);
$dados_vendas = mysqli_fetch_assoc($result_vendas);
$total_hoje = (float) $dados_vendas['total_hoje'];

/* TOTAL DE PRODUTOS */
$sql_produtos = "SELECT COUNT(*) AS total_produtos FROM produtos";
$result_produtos = mysqli_query($conn, $sql_produtos);
$dados_produtos = mysqli_fetch_assoc($result_produtos);
$total_produtos = (int) $dados_produtos['total_produtos'];

/* TOTAL DE CREDIÁRIO EM ABERTO */
$sql_crediario_total = "
    SELECT COALESCE(SUM(valor), 0) AS total_pendente
    FROM vendas
    WHERE formaDePagamento = 'Crediario'
      AND statusCrediario = 'Pendente'
";

$result_crediario_total = mysqli_query($conn, $sql_crediario_total);
$dados_crediario_total = mysqli_fetch_assoc($result_crediario_total);
$total_pendente = (float) $dados_crediario_total['total_pendente'];

/* LISTA DE CREDIÁRIOS */
$sql_crediario = "
    SELECT idVendas, nomeCliente, valor, data,
           dataVencimento, statusCrediario, dataPagamento
    FROM vendas
    WHERE formaDePagamento = 'Crediario'
    ORDER BY
        CASE
            WHEN statusCrediario = 'Pendente'
                 AND dataVencimento < CURDATE() THEN 0
            WHEN statusCrediario = 'Pendente' THEN 1
            ELSE 2
        END,
        dataVencimento ASC,
        idVendas DESC
";

$result_crediario = mysqli_query($conn, $sql_crediario);
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

      width: 100%;
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
      width: 100%;
    box-sizing: border-box;
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


.content {
    margin-left: 260px;
    padding: 35px;
    transition: margin-left 0.3s ease;
}

.content.sidebar-fechada {
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

      <div class="col-md-6">

        <div class="card-dashboard">

          <div>
            
<h6>Vendas Hoje</h6>

<h3>
    R$ <?= number_format($total_hoje, 2, ',', '.') ?>
</h3>

<div style="
    color: #d49b00;
    font-size: 13px;
    font-weight: 600;
    margin-top: 8px;
">
    <i class="bi bi-clock-history"></i>
    Crediário em aberto:
    R$ <?= number_format($total_pendente, 2, ',', '.') ?>
</div>
          </div>

          <div class="icon-card bg-vendas">
            <i class="bi bi-cash-stack"></i>
          </div>

        </div>

      </div>

      <div class="col-md-6">

        <div class="card-dashboard">

          <div>
            <h6>Produtos</h6>
            <h3><?= $total_produtos ?></h3>
          </div>

          
          <div class="icon-card bg-produtos">
            <i class="bi bi-box-seam"></i>
          </div>

        </div> <!-- Fecha card-dashboard -->
      </div> <!-- Fecha col-md-6 de Produtos -->

    </div> <!-- Fecha a linha dos cards -->

    <!-- PAINEL -->


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
    <?= htmlspecialchars(
        $venda['nomeCliente'] ?: 'Cliente não registrado',
        ENT_QUOTES,
        'UTF-8'
    ) ?>
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

<!-- PAINEL DE CREDIÁRIO -->
<div class="painel">

    <h4>
        <i class="bi bi-journal-text"></i>
        Vendas no Crediário
    </h4>

    <p style="color: #6b7280;">
        Acompanhe os pagamentos, vencimentos e dívidas pendentes.
    </p>

    <div class="tabela-vendas">
        <table class="table table-hover align-middle">

            <thead>
                <tr>
                    <th>Venda</th>
                    <th>Cliente</th>
                    <th>Valor</th>
                    <th>Data da venda</th>
                    <th>Vencimento</th>
                    <th>Situação</th>
                    <th>Ação</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($result_crediario && mysqli_num_rows($result_crediario) > 0): ?>

                    <?php while ($crediario = mysqli_fetch_assoc($result_crediario)): ?>

                        <?php
                        $pago = $crediario['statusCrediario'] === 'Pago';
                        $vencido = !$pago
                            && !empty($crediario['dataVencimento'])
                            && $crediario['dataVencimento'] < date('Y-m-d');

                        if ($pago) {
                            $corLinha = '#d1fae5';
                            $corTexto = '#047857';
                            $situacao = 'Pago';
                            $classeBadge = 'bg-success';
                        } elseif ($vencido) {
                            $corLinha = '#fee2e2';
                            $corTexto = '#b91c1c';
                            $situacao = 'Vencido';
                            $classeBadge = 'bg-danger';
                        } else {
                            $corLinha = '#fef3c7';
                            $corTexto = '#92400e';
                            $situacao = 'Pendente';
                            $classeBadge = 'bg-warning text-dark';
                        }
                        ?>

                        <tr style="--bs-table-bg: <?= $corLinha ?>; background-color: <?= $corLinha ?>;">

                            <td>
                                #<?= (int) $crediario['idVendas'] ?>
                            </td>

                            <td style="color: <?= $corTexto ?>; font-weight: 600;">
                                <?= htmlspecialchars(
                                    $crediario['nomeCliente'] ?: 'Cliente não informado',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </td>

                            <td>
                                R$ <?= number_format(
                                    (float) $crediario['valor'],
                                    2,
                                    ',',
                                    '.'
                                ) ?>
                            </td>

                            <td>
                                <?= !empty($crediario['data'])
                                    ? date('d/m/Y', strtotime($crediario['data']))
                                    : '—' ?>
                            </td>

                            <td style="color: <?= $corTexto ?>; font-weight: 600;">
                                <?= !empty($crediario['dataVencimento'])
                                    ? date('d/m/Y', strtotime($crediario['dataVencimento']))
                                    : 'Não informado' ?>
                            </td>

                            <td>
                                <span class="badge <?= $classeBadge ?>">
                                    <?= $situacao ?>
                                </span>

                                <?php if ($pago && !empty($crediario['dataPagamento'])): ?>
                                    <div style="font-size: 12px; margin-top: 5px; color: #047857;">
                                        Pago em <?= date(
                                            'd/m/Y',
                                            strtotime($crediario['dataPagamento'])
                                        ) ?>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if (!$pago): ?>
                                    <form method="POST" class="form-marcar-pago">

                                        <input type="hidden"
                                               name="marcar_pago"
                                               value="<?= (int) $crediario['idVendas'] ?>">

                                        <button type="button"
        class="btn btn-success btn-sm"
        onclick="abrirPopupPagamento(this)"
        data-cliente="<?= htmlspecialchars($crediario['nomeCliente'] ?: 'Cliente não informado', ENT_QUOTES, 'UTF-8') ?>"
        data-valor="<?= number_format((float)$crediario['valor'], 2, ',', '.') ?>">
    <i class="bi bi-check-circle"></i>
    Marcar como pago
</button>

                                    </form>
                                <?php else: ?>
                                    <span class="text-success">
                                        <i class="bi bi-check-circle-fill"></i>
                                        Quitado
                                    </span>
                                <?php endif; ?>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-journal-check"></i>
                            Nenhuma venda no crediário registrada.
                        </td>
                    </tr>

                <?php endif; ?>
            </tbody>

        </table>
    </div>

</div>
  </div>

<!-- POP-UP DE CONFIRMAÇÃO DE PAGAMENTO -->
<div id="popupPagamento" class="popup-pagamento">
    <div class="popup-pagamento-card">
        <div class="popup-pagamento-icone">
            <i class="bi bi-cash-coin"></i>
        </div>

        <h3>Confirmar pagamento?</h3>

        <p id="popupTextoPagamento">
            Deseja confirmar que esta dívida foi paga?
        </p>

        <div class="popup-pagamento-botoes">
            <button type="button"
                    class="popup-btn-cancelar"
                    onclick="fecharPopupPagamento()">
                Cancelar
            </button>

            <button type="button"
                    class="popup-btn-confirmar"
                    onclick="confirmarPagamento()">
                <i class="bi bi-check-circle"></i>
                Sim, confirmar
            </button>
        </div>
    </div>
</div>

<style>
.popup-pagamento {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(5px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.popup-pagamento.aberto {
    display: flex;
    animation: aparecerFundo 0.2s ease;
}

.popup-pagamento-card {
    background: white;
    width: 100%;
    max-width: 420px;
    padding: 32px;
    border-radius: 22px;
    text-align: center;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
    animation: subirPopup 0.25s ease;
}

.popup-pagamento-icone {
    width: 72px;
    height: 72px;
    margin: 0 auto 18px;
    border-radius: 50%;
    background: #dcfce7;
    color: #15803d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 34px;
}

.popup-pagamento-card h3 {
    font-size: 23px;
    font-weight: 700;
    color: #111827;
    margin-bottom: 12px;
}

.popup-pagamento-card p {
    color: #6b7280;
    line-height: 1.6;
    margin-bottom: 26px;
}

.popup-pagamento-botoes {
    display: flex;
    gap: 12px;
}

.popup-pagamento-botoes button {
    flex: 1;
    border: none;
    border-radius: 12px;
    padding: 12px 10px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
}

.popup-btn-cancelar {
    background: #f3f4f6;
    color: #374151;
}

.popup-btn-cancelar:hover {
    background: #e5e7eb;
}

.popup-btn-confirmar {
    background: #16a34a;
    color: white;
}

.popup-btn-confirmar:hover {
    background: #15803d;
    transform: translateY(-2px);
}

@keyframes aparecerFundo {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes subirPopup {
    from {
        opacity: 0;
        transform: translateY(12px) scale(0.97);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}
</style>

<script>
let formularioPagamentoAtual = null;

function abrirPopupPagamento(botao) {
    formularioPagamentoAtual = botao.closest('form');

    const cliente = botao.dataset.cliente;
    const valor = botao.dataset.valor;

    document.getElementById('popupTextoPagamento').textContent =
        `Confirma que ${cliente} pagou a dívida de R$ ${valor}?`;

    document.getElementById('popupPagamento').classList.add('aberto');
}

function fecharPopupPagamento() {
    document.getElementById('popupPagamento').classList.remove('aberto');
    formularioPagamentoAtual = null;
}

function confirmarPagamento() {
    if (formularioPagamentoAtual) {
        formularioPagamentoAtual.submit();
    }
}

document.getElementById('popupPagamento').addEventListener('click', function(event) {
    if (event.target === this) {
        fecharPopupPagamento();
    }
});
</script>


<script src="js/script.js"></script>

<script>

function alternarSidebar() {

    const sidebar =
        document.querySelector(".sidebar");

    const conteudo =
    document.querySelector(".content");

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