<?php
require_once '../database/mysqli.php';

// Ativar exibição de erros para depuração (remova em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar se a sessão está ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se o aluno está autenticado
$id_aluno = $_SESSION['id_aluno'] ?? null;

if (!$id_aluno) {
    echo json_encode(['success' => false, 'message' => 'Você precisa estar autenticado para cancelar esta candidatura.']);
    exit();
}

// Processar remoção de candidatura via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_candidatura'])) {
    $id_candidatura = intval($_POST['id_candidatura']);

    $sql_delete = "DELETE FROM candidaturas WHERE id_candidatura = ? AND id_aluno = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("ii", $id_candidatura, $id_aluno);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Candidatura cancelada com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao cancelar a candidatura.']);
    }

    $stmt->close();
    exit();
}

// Requisição inválida
echo json_encode(['success' => false, 'message' => 'Requisição inválida.']);
exit();
