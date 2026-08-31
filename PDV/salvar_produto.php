<?php

include 'conexao.php';

// Recebe os dados do formulário
$nome = $_POST['nome'];
$valor = $_POST['preco'];
$estoque = $_POST['estoque'];
$codigo = $_POST['codigo_barras'];
$armazenamento = $_POST['armazenamento'];
$categoria = $_POST['categoria'];

// Verifica se uma imagem foi enviada
if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {

    $arquivo = $_FILES['arquivo'];

    // Pasta onde a imagem será salva
    $pastaUpload = '../imagens/';

    // Cria um nome único
    $nomeDoArquivo = uniqid() . "_" . basename($arquivo['name']);

    // Caminho da imagem
    $imagem = $pastaUpload . $nomeDoArquivo;

    // Move a imagem
    if (move_uploaded_file($arquivo['tmp_name'], $imagem)) {

        // Cadastra o produto
        $sql = "INSERT INTO produtos
        (nome, codigoDeBarras, valor, estoque, imagem, categoria, armazenamento)
        VALUES
        ('$nome', '$codigo', '$valor', '$estoque', '$imagem', '$categoria', '$armazenamento')";

        if ($conn->query($sql)) {
            echo "Produto cadastrado com sucesso!";
        } else {
            echo "Erro ao cadastrar produto: " . $conn->error;
        }

    } else {
        echo "Erro ao salvar a imagem.";
    }

} else {
    echo "Nenhuma imagem foi enviada.";
}

?>