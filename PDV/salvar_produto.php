    <?php

    include 'conexao.php';

    header('Content-Type: application/json; charset=utf-8');


    // =====================================================
    // RECEBER DADOS DO FORMULÁRIO
    // =====================================================

    $nome = $_POST['nome'] ?? '';
    $precoRecebido = $_POST['preco'] ?? '0';

    // Remove separadores de milhar e converte a vírgula decimal
    $valor = str_replace(',', '.', str_replace('.', '', $precoRecebido));

    // Converte para número decimal
    $valor = (float) $valor;
    $estoque = $_POST['estoque'] ?? '';
    $codigo = $_POST['codigo_barras'] ?? '';
    $armazenamento = $_POST['armazenamento'] ?? '';
    $categoria = $_POST['categoria'] ?? '';


    // =====================================================
    // VERIFICAR CAMPOS
    // =====================================================

    if (
        trim($nome) === '' ||
        $precoRecebido === '' ||
        $valor <= 0 ||
        $estoque === '' ||
        $codigo === '' ||
        $armazenamento === '' ||
        $categoria === ''
    ) {
        echo json_encode([
            "sucesso" => false,
            "mensagem" => "Preencha todos os campos corretamente."
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
VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    if (file_exists($imagem)) {
        unlink($imagem);
    }

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao preparar cadastro: " . $conn->error
    ], JSON_UNESCAPED_UNICODE);

    exit;
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

    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Produto cadastrado com sucesso!",
        "produto" => [
            "codigo" => $codigo,
            "nome" => $nome,
            "preco" => $valor,
            "estoque" => $estoque,
            "armazenamento" => $armazenamento,
            "categoria" => $categoria
        ]
    ], JSON_UNESCAPED_UNICODE);

} else {

    if (file_exists($imagem)) {
        unlink($imagem);
    }

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao cadastrar produto: " . $stmt->error
    ], JSON_UNESCAPED_UNICODE);
}

$stmt->close();



    exit;
    ?>