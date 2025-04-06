<?php
session_start();
require_once '../database/mysqli.php';

// Verifica se o ID foi enviado
if (isset($_GET['id'])) {
    $id_oferta = intval($_GET['id']); // Converte para inteiro

    // Query de exclusão
    $sql = "DELETE FROM ofertas_empresas WHERE id_oferta = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $id_oferta);
        if ($stmt->execute()) {
            $_SESSION['error'] = "Oferta excluída com sucesso!";
        } else {
            $_SESSION['mensagem_sucesso'] = "Erro ao excluir a oferta.";
        }
        $stmt->close();
    } else {
        $_SESSION['mensagem_sucesso'] = "Erro ao preparar a consulta.";
    }
}

// Redireciona de volta para a página de gestão
header("Location: ../professor_dashboard.php?page=gestao_ofertas");
exit;
?>
