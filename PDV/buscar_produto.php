<?php

include 'conexao.php';

header('Content-Type: application/json; charset=utf-8');

$codigo = $_GET['codigo'] ?? '';

$codigo = trim($codigo);

if ($codigo === '') {

    echo json_encode([
        "erro" => true,
        "mensagem" => "Código não informado"
    ]);

    exit;

}

$sql = "SELECT *
        FROM produtos
        WHERE codigoDeBarras = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    echo json_encode([
        "erro" => true,
        "mensagem" => "Erro no banco: " . $conn->error
    ]);

    exit;

}

$stmt->bind_param("s", $codigo);

$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {

    $produto = $resultado->fetch_assoc();

    echo json_encode($produto);

} else {

    echo json_encode([
        "erro" => true,
        "mensagem" => "Produto não encontrado"
    ]);

}

$stmt->close();
$conn->close();

?>