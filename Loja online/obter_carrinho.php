
<?php
session_start();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

$itens = [];
$total = 0;
$quantidadeTotal = 0;

foreach ($_SESSION['carrinho'] as $id => $produto) {
    $quantidade = (int) $produto['quantidade'];
    $valor = (float) $produto['valor'];
    $subtotal = $quantidade * $valor;

    $itens[] = [
        'id' => (int) $id,
        'nome' => $produto['nome'],
        'valor' => $valor,
        'imagem' => $produto['imagem'] ?? '',
        'quantidade' => $quantidade,
        'subtotal' => $subtotal
    ];

    $total += $subtotal;
    $quantidadeTotal += $quantidade;
}

echo json_encode([
    'sucesso' => true,
    'itens' => $itens,
    'total' => $total,
    'quantidade' => $quantidadeTotal
], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
