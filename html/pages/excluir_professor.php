<?php
include '../database/mysqli.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Erro: ID do professor não especificado.");
}

$id_professor = intval($_GET['id']);

// Verifica se o professor existe
$result = $conn->query("SELECT * FROM professores WHERE id_professor = $id_professor");
if ($result->num_rows == 0) {
    die("Erro: Professor não encontrado.");
}

// Exclui o professor
$delete = $conn->query("DELETE FROM professores WHERE id_professor = $id_professor");

if ($delete) {
    echo "<script>alert('Professor excluído com sucesso!'); window.location.href='../html/admin_dashboard.php?page=gestao_utilizadores';</script>";
} else {
    echo "<script>alert('Erro ao excluir professor.');</script>";
}
?>
