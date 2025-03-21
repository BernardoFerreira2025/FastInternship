<?php
require_once '../../database/mysqli.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o admin está logado
if (!isset($_SESSION['id_utilizador'])) {
    die("Acesso negado.");
}

// Verifica se foi enviado um ID de aluno e um arquivo
if (!isset($_POST['id_aluno']) || !isset($_FILES['novo_curriculo'])) {
    die("Erro: Dados incompletos.");
}

$id_aluno = intval($_POST['id_aluno']);
$arquivo = $_FILES['novo_curriculo'];

// Diretório onde os currículos serão armazenados
$upload_dir = "../uploads/";

// Criar diretório se não existir
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Nome do arquivo
$extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
if ($extensao != "pdf") {
    die("Erro: Apenas arquivos PDF são permitidos.");
}

$novo_nome = "curriculo_" . $id_aluno . "." . $extensao;
$caminho_arquivo = $upload_dir . $novo_nome;

// Move o arquivo para o diretório
if (move_uploaded_file($arquivo['tmp_name'], $caminho_arquivo)) {
    // Atualiza o banco de dados
    $sql = "UPDATE alunos SET Curriculo = ? WHERE id_aluno = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $novo_nome, $id_aluno);
    $stmt->execute();

    // Define mensagem de sucesso
    $_SESSION['toast_message'] = "Currículo atualizado com sucesso!";
} else {
    $_SESSION['toast_message'] = "Erro ao fazer upload do currículo.";
}

// Redireciona de volta para a página de gestão de alunos
header("Location: ../admin_dashboard.php?page=gestao_utilizadores");
exit();
?>
