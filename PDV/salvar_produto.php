
<?php

include 'conexao.php';

header('Content-Type: application/json; charset=utf-8');

function responder($sucesso, $mensagem, $produto = null) {
    echo json_encode([
        "sucesso" => $sucesso,
        "mensagem" => $mensagem,
        "produto" => $produto
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

// =====================================================
// RECEBER DADOS DO FORMULÁRIO
// =====================================================

$nome = trim($_POST['nome'] ?? '');
$precoRecebido = trim($_POST['preco'] ?? '');
$estoqueRecebido = $_POST['estoque'] ?? '';
$codigo = trim($_POST['codigo_barras'] ?? '');
$armazenamento = $_POST['armazenamento'] ?? '';
$categoria = $_POST['categoria'] ?? '';

// Converte o preço no formato brasileiro para número.
// Exemplo: 1.234,56 -> 1234.56
$valor = str_replace(
    ',',
    '.',
    str_replace('.', '', $precoRecebido)
);

$valor = (float) $valor;

// =====================================================
// VALIDAR CAMPOS
// =====================================================

if (
    $nome === '' ||
    $precoRecebido === '' ||
    $valor <= 0 ||
    $estoqueRecebido === '' ||
    !is_numeric($estoqueRecebido) ||
    (float)$estoqueRecebido < 0 ||
    (float)$estoqueRecebido != (int)$estoqueRecebido ||
    $codigo === '' ||
    $armazenamento === '' ||
    $categoria === ''
) {
    responder(
        false,
        "Preencha todos os campos corretamente."
    );
}

$estoque = (int) $estoqueRecebido;

// =====================================================
// VERIFICAR SE O CÓDIGO JÁ ESTÁ CADASTRADO
// =====================================================

$sqlVerificar = "
    SELECT idProduto
    FROM produtos
    WHERE codigoDeBarras = ?
    LIMIT 1
";

$stmtVerificar = $conn->prepare($sqlVerificar);

if (!$stmtVerificar) {
    responder(
        false,
        "Não foi possível verificar o código de barras."
    );
}

$stmtVerificar->bind_param("s", $codigo);
$stmtVerificar->execute();
$stmtVerificar->store_result();

if ($stmtVerificar->num_rows > 0) {
    $stmtVerificar->close();

    responder(
        false,
        "Já existe um produto cadastrado com o código de barras \""
        . $codigo .
        "\". Verifique o código e tente novamente."
    );
}

$stmtVerificar->close();

// =====================================================
// VERIFICAR IMAGEM
// =====================================================

if (
    !isset($_FILES['arquivo']) ||
    $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK
) {
    responder(
        false,
        "Selecione uma imagem válida para o produto."
    );
}

$arquivo = $_FILES['arquivo'];

// =====================================================
// SALVAR IMAGEM
// =====================================================

$pastaUpload = '../imagens/';

if (!is_dir($pastaUpload) || !is_writable($pastaUpload)) {
    responder(
        false,
        "A pasta de imagens não existe ou não permite gravação."
    );
}

$nomeDoArquivo =
    uniqid('', true) . "_" . basename($arquivo['name']);

$imagem = $pastaUpload . $nomeDoArquivo;

if (!move_uploaded_file($arquivo['tmp_name'], $imagem)) {
    responder(
        false,
        "Erro ao salvar a imagem do produto."
    );
}

// =====================================================
// CADASTRAR PRODUTO
// =====================================================

$sql = "
    INSERT INTO produtos
    (
        nome,
        codigoDeBarras,
        valor,
        estoque,
        imagem,
        categoria,
        armazenamento
    )
    VALUES (?, ?, ?, ?, ?, ?, ?)
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    if (file_exists($imagem)) {
        unlink($imagem);
    }

    responder(
        false,
        "Não foi possível preparar o cadastro do produto."
    );
}

$stmt->bind_param(
    "ssdisss",
    $nome,
    $codigo,
    $valor,
    $estoque,
    $imagem,
    $categoria,
    $armazenamento
);

if ($stmt->execute()) {

    $produto = [
        "codigo" => $codigo,
        "nome" => $nome,
        "preco" => $valor,
        "estoque" => $estoque,
        "armazenamento" => $armazenamento,
        "categoria" => $categoria
    ];

    $stmt->close();

    responder(
        true,
        "Produto cadastrado com sucesso!",
        $produto
    );

} else {

    // Código 1062: o MySQL detectou um valor duplicado
    $codigoDuplicado = ($stmt->errno === 1062);

    $stmt->close();

    // Remove a imagem caso o produto não tenha sido cadastrado
    if (file_exists($imagem)) {
        unlink($imagem);
    }

    if ($codigoDuplicado) {
        responder(
            false,
            "Este código de barras já pertence a outro produto. "
            . "Informe um código diferente."
        );
    }

    responder(
        false,
        "Erro ao cadastrar produto. Verifique os dados e tente novamente."
    );
}
?>
