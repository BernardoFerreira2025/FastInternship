<?php
session_start();
include '../../database/mysqli.php';

// Verifica se o ID foi enviado
if (isset($_GET['id'])) {
    $id_oferta = intval($_GET['id']); // Converte para inteiro

    // Query de exclusão
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

// Redireciona de volta para a página de gestão
header("Location: ../empresa_dashboard.php?page=gestao_ofertas");
exit;
?>
