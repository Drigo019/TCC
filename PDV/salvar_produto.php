<?php

include 'conexao.php';

header('Content-Type: application/json; charset=utf-8');


// =====================================================
// RECEBER DADOS DO FORMULÁRIO
// =====================================================

$nome = $_POST['nome'] ?? '';
$valor = $_POST['preco'] ?? '';
$estoque = $_POST['estoque'] ?? '';
$codigo = $_POST['codigo_barras'] ?? '';
$armazenamento = $_POST['armazenamento'] ?? '';
$categoria = $_POST['categoria'] ?? '';


// =====================================================
// VERIFICAR CAMPOS
// =====================================================

if (
    $nome === '' ||
    $valor === '' ||
    $estoque === '' ||
    $codigo === '' ||
    $armazenamento === '' ||
    $categoria === ''
) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Preencha todos os campos do produto."
    ]);

    exit;
}


// =====================================================
// VERIFICAR IMAGEM
// =====================================================

if (
    !isset($_FILES['arquivo']) ||
    $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK
) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Nenhuma imagem foi enviada."
    ]);

    exit;
}


$arquivo = $_FILES['arquivo'];


// =====================================================
// PASTA DE UPLOAD
// =====================================================

$pastaUpload = '../imagens/';


// =====================================================
// CRIAR NOME ÚNICO PARA A IMAGEM
// =====================================================

$nomeDoArquivo =
    uniqid() . "_" . basename($arquivo['name']);


// Caminho da imagem

$imagem =
    $pastaUpload . $nomeDoArquivo;


// =====================================================
// SALVAR IMAGEM
// =====================================================

if (
    !move_uploaded_file(
        $arquivo['tmp_name'],
        $imagem
    )
) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao salvar a imagem."
    ]);

    exit;
}


// =====================================================
// CADASTRAR PRODUTO
// =====================================================

$sql = "INSERT INTO produtos
(
    nome,
    codigoDeBarras,
    valor,
    estoque,
    imagem,
    categoria,
    armazenamento
)
VALUES
(
    '$nome',
    '$codigo',
    '$valor',
    '$estoque',
    '$imagem',
    '$categoria',
    '$armazenamento'
)";


if ($conn->query($sql)) {


    // =================================================
    // SUCESSO
    // =================================================

    echo json_encode([

        "sucesso" => true,

        "mensagem" =>
            "Produto cadastrado com sucesso!",

        "produto" => [

            "codigo" =>
                $codigo,

            "nome" =>
                $nome,

            "preco" =>
                $valor,

            "estoque" =>
                $estoque,

            "armazenamento" =>
                $armazenamento,

            "categoria" =>
                $categoria

        ]

    ], JSON_UNESCAPED_UNICODE);


} else {


    // =================================================
    // ERRO NO BANCO
    // =================================================

    // Remove a imagem caso o produto não seja salvo
    if (file_exists($imagem)) {
        unlink($imagem);
    }


    echo json_encode([

        "sucesso" => false,

        "mensagem" =>
            "Erro ao cadastrar produto: " .
            $conn->error

    ], JSON_UNESCAPED_UNICODE);

}


exit;
?>