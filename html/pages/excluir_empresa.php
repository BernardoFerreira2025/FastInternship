<?php
session_start();
include '../../database/mysqli.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['toast_message'] = "Erro: ID da empresa não fornecido.";
    header("Location: ../admin_dashboard.php?page=gestao_utilizadores");
    exit();
}

$id = intval($_GET['id']);

// Verifica se a empresa existe (opcional, mas recomendado)
$check = $conn->prepare("SELECT id_empresas FROM empresas WHERE id_empresas = ?");
$check->bind_param("i", $id);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
    $_SESSION['toast_message'] = "Erro: Empresa não encontrada.";
    $check->close();
    header("Location: ../admin_dashboard.php?page=gestao_utilizadores");
    exit();
}
$check->close();

// Executa exclusão
$stmt = $conn->prepare("DELETE FROM empresas WHERE id_empresas = ?");
if ($stmt) {
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['toast_message'] = "Empresa excluída com sucesso!";
    } else {
        $_SESSION['toast_message'] = "Erro ao excluir a empresa.";
    }
    $stmt->close();
} else {
    $_SESSION['toast_message'] = "Erro na preparação da exclusão.";
}

header("Location: ../admin_dashboard.php?page=gestao_utilizadores");
exit();
?>
