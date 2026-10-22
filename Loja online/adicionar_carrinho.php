
<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

function responder($sucesso, $mensagem, $quantidade = null) {
    echo json_encode([
        'sucesso' => $sucesso,
        'mensagem' => $mensagem,
        'quantidade' => $quantidade
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if (empty($_SESSION['cliente_id'])) {
    responder(false, 'Apenas usuários cadastrados podem realizar compras. Entre na sua conta ou cadastre-se.', null);
}

$idProduto = filter_input(INPUT_POST, 'idProduto', FILTER_VALIDATE_INT);

if (!$idProduto || $idProduto < 1) {
    responder(false, 'Produto inválido.');
}

$conn = new mysqli('localhost', 'root', '', 'containerdoqueijo', 3308);

if ($conn->connect_error) {
    responder(false, 'Não foi possível conectar ao banco de dados.');
}

$conn->set_charset('utf8mb4');

$stmt = $conn->prepare(
    'SELECT idProduto, nome, valor, imagem, estoque
     FROM produtos
     WHERE idProduto = ?'
);

if (!$stmt) {
    responder(false, 'Não foi possível consultar o produto.');
}

$stmt->bind_param('i', $idProduto);
$stmt->execute();

$resultado = $stmt->get_result();
$produto = $resultado->fetch_assoc();
$stmt->close();

if (!$produto) {
    responder(false, 'Produto não encontrado.');
}

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

$quantidadeAtual = (int) (
    $_SESSION['carrinho'][$idProduto]['quantidade'] ?? 0
);

$estoque = (int) $produto['estoque'];

if ($quantidadeAtual >= $estoque) {
    responder(false, 'Você já adicionou todo o estoque disponível.');
}

if ($quantidadeAtual > 0) {
    $_SESSION['carrinho'][$idProduto]['quantidade']++;
} else {
    $_SESSION['carrinho'][$idProduto] = [
        'quantidade' => 1,
        'nome' => $produto['nome'],
        'valor' => $produto['valor'],
        'imagem' => $produto['imagem']
    ];
}

$quantidadeTotal = 0;

foreach ($_SESSION['carrinho'] as $item) {
    $quantidadeTotal += (int) $item['quantidade'];
}

responder(
    true,
    $produto['nome'] . ' foi adicionado ao carrinho!',
    $quantidadeTotal
);
