<?php
$pdo = new PDO('mysql:host=localhost;dbname=containerdoqueijo', 'root', '');

if(isset($_FILES['arquivo'])){
    $arquivo = $_FILES['arquivo'];

    //Caminho onde a imagem será salva no servidor
    $pastaUpload = 'imagens/';
    $nomeDoArquivo = uniqid() . "_" . $arquivo['name'];
    $caminhoSalvar = $pastaUpload . $nomeDoArquivo;

    //Move a imagem da pasta temporário para a definitiva 
    if(move_uploaded_file($arquivo['tmp_name'], $caminhoSalvar)){
        //Insere o caminho da imagem no banco de dados 
        $stmt = $pdo->prepare("INSERT INTO imagens (caminho) VALUE (:caminho)");
        $stmt->bindParam(':caminho', $caminhoSalvar);
        $stmt->execute();

        echo "Imagem enviada e salva com sucesso";
    } else {
        echo"Erro as salvar a imagem";
    }
}
?>