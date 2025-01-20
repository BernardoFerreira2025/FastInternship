<?php
session_start(); // Inicia a sessão
include '../../database/mysqli.php'; // Corrija o caminho conforme necessário

if (!$conn) {
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
}

// Verifica se o ID foi enviado
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Converte para inteiro

    // Prepara a consulta de exclusão
    $stmt = $conn->prepare("DELETE FROM empresas WHERE id_empresas = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            // Define uma mensagem de sucesso
            $_SESSION['mensagem_sucesso'] = "Empresa excluída com sucesso!";
            header("Location: ../admin_dashboard.php?page=gestao_utilizadores");
            exit();
        } else {
            echo "Erro ao excluir a empresa: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Erro ao preparar a consulta: " . $conn->error;
    }
} else {
    echo "ID da empresa não fornecido.";
}
?>
