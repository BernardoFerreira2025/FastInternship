<?php
require_once '../../database/mysqli.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_empresas'])) {
    die("Acesso negado.");
}

if (!isset($_POST['id_candidatura']) || !isset($_POST['acao'])) {
    die("Erro: Dados incompletos.");
}

$id_candidatura = intval($_POST['id_candidatura']);
$acao = $_POST['acao'];

$status_empresa = ($acao === 'aceitar') ? 'aprovado' : ($acao === 'rejeitar' ? 'rejeitado' : 'pendente');

$query = "UPDATE candidaturas SET status_empresa = ? WHERE id_candidatura = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $status_empresa, $id_candidatura);
$stmt->execute();

header("Location: ../empresa_dashboard.php?page=alunos_candidatos");
exit();
?>
