<?php
session_start(); // Inicia a sessão para exibir mensagens
include '../../database/mysqli.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Certifique-se de que é um número inteiro válido

    $stmt = $conn->prepare("DELETE FROM alunos WHERE id_aluno = ?");
    if ($stmt) {
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            // Define uma mensagem de sucesso na sessão
            $_SESSION['mensagem_sucesso'] = "Aluno excluído com sucesso!";
            header("Location: ../admin_dashboard.php?page=gestao_utilizadores");
            exit();
        } else {
            echo "Erro ao excluir o aluno: " . $stmt->error;
        }
    } else {
        echo "Erro ao preparar a consulta: " . $conn->error;
    }
} else {
    echo "ID do aluno não fornecido.";
}
?>
