<?php
session_start();
include '../database/mysqli.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = trim($_POST['titulo']);
    $descricao = trim($_POST['descricao']);
    $requisitos = trim($_POST['requisitos']);
    $id_empresa = intval($_POST['id_empresa']);
    $vagas = intval($_POST['vagas']);
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];

    // Validação da sessão do professor
    if (!isset($_SESSION['id_professor'])) {
        $_SESSION['mensagem_erro'] = 'Sessão inválida. Faça login novamente.';
        header('Location: ../formlogin.php');
        exit();
    }

    $id_professor = $_SESSION['id_professor'];

    // Obtém o curso do professor logado
    $query = "SELECT id_curso FROM professores WHERE id_professor = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_professor);
    $stmt->execute();
    $result = $stmt->get_result();
    $professor = $result->fetch_assoc();
    $id_curso = $professor['id_curso'] ?? null;

    if (!$id_curso) {
        $_SESSION['mensagem_erro'] = 'Erro ao identificar o curso do professor.';
        header('Location: ../pages/gerir_ofertas.php');
        exit();
    }

    // Validação dos campos
    if (empty($titulo) || empty($descricao) || empty($requisitos) || empty($id_empresa) || empty($vagas) || empty($data_inicio) || empty($data_fim)) {
        $_SESSION['mensagem_erro'] = 'Preencha todos os campos obrigatórios.';
        header('Location: ../pages/gerir_ofertas.php');
        exit();
    }

    // Formatar datas para garantir o formato correto (YYYY-MM-DD)
    $data_inicio_formatada = date('Y-m-d', strtotime($data_inicio));
    $data_fim_formatada = date('Y-m-d', strtotime($data_fim));

    if ($data_fim_formatada < $data_inicio_formatada) {
        $_SESSION['mensagem_erro'] = 'Tente novamente, verifique as datas.';
        header('Location: ../pages/gerir_ofertas.php');
        exit();
    }

    // Inserindo a oferta vinculada ao curso correto
    $stmt = $conn->prepare("INSERT INTO ofertas_empresas (titulo, descricao, requisitos, id_empresa, vagas, id_curso, data_inicio, data_fim) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiiiss", $titulo, $descricao, $requisitos, $id_empresa, $vagas, $id_curso, $data_inicio_formatada, $data_fim_formatada);

    if ($stmt->execute()) {
        $_SESSION['mensagem_sucesso'] = 'Oferta publicada com sucesso!';
        header('Location: professor_dashboard.php?page=gestao_ofertas');
        exit();
    } else {
        $_SESSION['mensagem_erro'] = 'Erro ao publicar a oferta.';
        header('Location: ../pages/gerir_ofertas.php');
        exit();
    }
}
?>
