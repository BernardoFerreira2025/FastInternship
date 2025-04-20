<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../database/mysqli.php';

// Verifica se o professor está autenticado
if (!isset($_SESSION['id_professor']) || $_SESSION['user_role'] !== 'professor') {
    header("Location: ../formlogin.php");
    exit();
}

$id_curso_professor = $_SESSION['id_curso'] ?? null;

// Verifica se o ID da empresa foi passado
if (!isset($_GET['id'])) {
    $_SESSION['toast_message'] = "Erro: ID da empresa não fornecido.";
    header("Location: ../professor_dashboard.php?page=gestao_empresas");
    exit();
}

$id_empresa = intval($_GET['id']);

// Verifica se a empresa pertence ao curso do professor
$stmt = $conn->prepare("SELECT id_curso FROM empresas WHERE id_empresas = ?");
$stmt->bind_param("i", $id_empresa);
$stmt->execute();
$res = $stmt->get_result();
$empresa = $res->fetch_assoc();

if (!$empresa || $empresa['id_curso'] != $id_curso_professor) {
    $_SESSION['toast_message'] = "Erro: Não tens permissão para excluir esta empresa.";
    header("Location: ../professor_dashboard.php?page=gestao_empresas");
    exit();
}

// Exclui a empresa
$stmt = $conn->prepare("DELETE FROM empresas WHERE id_empresas = ?");
$stmt->bind_param("i", $id_empresa);

if ($stmt->execute()) {
    $_SESSION['toast_message'] = "Empresa excluída com sucesso!";
} else {
    $_SESSION['toast_message'] = "Erro ao excluir a empresa.";
}

header("Location: ../professor_dashboard.php?page=gestao_empresas");
exit();
