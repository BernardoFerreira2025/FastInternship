<?php
session_start();
require_once __DIR__ . '/../../database/mysqli.php';

if (isset($_GET['id'])) {
    $id_oferta = intval($_GET['id']);

    $sql = "DELETE FROM ofertas_empresas WHERE id_oferta = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $id_oferta);
        if ($stmt->execute()) {
            $_SESSION['toast_message'] = "Oferta excluída com sucesso!";
        } else {
            $_SESSION['toast_message'] = "Erro ao excluir a oferta.";
        }
        $stmt->close();
    } else {
        $_SESSION['toast_message'] = "Erro ao preparar a consulta.";
    }
}

// Redireciona para a dashboard do professor
header("Location: ../professor_dashboard.php?page=gestao_ofertas");
exit;
?>
