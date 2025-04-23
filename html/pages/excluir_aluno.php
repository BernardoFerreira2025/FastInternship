<?php
session_start(); // Inicia a sessão para exibir mensagens
include '../../database/mysqli.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Garante que é um número inteiro válido

    $stmt = $conn->prepare("DELETE FROM alunos WHERE id_aluno = ?");
    if ($stmt) {
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            $_SESSION['toast_message'] = "Aluno excluído com sucesso!";
        } else {
            $_SESSION['toast_message'] = "Erro ao excluir o aluno: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['toast_message'] = "Erro ao preparar a consulta: " . $conn->error;
    }
} else {
    $_SESSION['toast_message'] = "ID do aluno não fornecido.";
}

header("Location: ../admin_dashboard.php?page=gestao_utilizadores");
exit();
?>
