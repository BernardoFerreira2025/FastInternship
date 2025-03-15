<?php
// Conexão com o banco de dados
include '../database/mysqli.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o professor está logado
if (!isset($_SESSION['id_professor'])) {
    die("Acesso negado.");
}

$id_professor = $_SESSION['id_professor'];

// Verifica se a requisição foi enviada corretamente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_candidatura'], $_POST['acao'])) {
    $id_candidatura = $_POST['id_candidatura'];
    $acao = $_POST['acao'];

    // Define o novo status com base na ação do professor
    if ($acao === 'aceitar') {
        $status_professor = 'aceite';
    } elseif ($acao === 'rejeitar') {
        $status_professor = 'rejeitado';
    } else {
        die("Ação inválida.");
    }

    // Atualiza o status da candidatura
    $query = "UPDATE candidaturas SET status_professor = ? WHERE id_candidatura = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status_professor, $id_candidatura);
    
    if ($stmt->execute()) {
        $_SESSION['mensagem_sucesso'] = "Candidatura atualizada com sucesso.";
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao atualizar a candidatura.";
    }

    // Redireciona de volta para a página de candidatos
    header("Location: alunos_candidatos.php?oferta_id=" . $_POST['oferta_id']);
    exit();
} else {
    die("Requisição inválida.");
}
