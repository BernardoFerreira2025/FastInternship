<?php
session_start();
include '../../database/mysqli.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['toast_message'] = "Erro: ID do professor não especificado.";
    $_SESSION['toast_type'] = "error";
    header("Location: ../admin_dashboard.php?page=gestao_utilizadores");
    exit();
}

$id_professor = intval($_GET['id']);

// Verifica se o professor existe
$result = $conn->prepare("SELECT id_professor FROM professores WHERE id_professor = ?");
$result->bind_param("i", $id_professor);
$result->execute();
$result->store_result();

if ($result->num_rows === 0) {
    $_SESSION['toast_message'] = "Erro: Professor não encontrado.";
    $_SESSION['toast_type'] = "error";
    $result->close();
    header("Location: ../admin_dashboard.php?page=gestao_utilizadores");
    exit();
}
$result->close();

// Exclui o professor
$stmt = $conn->prepare("DELETE FROM professores WHERE id_professor = ?");
$stmt->bind_param("i", $id_professor);

if ($stmt->execute()) {
    $_SESSION['toast_message'] = "Professor excluído com sucesso!";
    $_SESSION['toast_type'] = "error"; // 🔴 Sempre vermelho, mesmo em sucesso
} else {
    $_SESSION['toast_message'] = "Erro ao excluir professor.";
    $_SESSION['toast_type'] = "error";
}

$stmt->close();
header("Location: ../admin_dashboard.php?page=gestao_utilizadores");
exit();
?>
