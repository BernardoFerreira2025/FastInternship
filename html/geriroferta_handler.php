<?php
session_start();
include '../database/mysqli.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $requisitos = $_POST['requisitos'];
    $id_empresa = $_POST['id_empresa'];
    $vagas = $_POST['vagas'];
    $curso_relacionado = $_POST['curso_relacionado'];
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];

    if (empty($titulo) || empty($descricao) || empty($requisitos) || empty($id_empresa) || empty($vagas) || empty($curso_relacionado) || empty($data_inicio) || empty($data_fim)) {
        $_SESSION['mensagem_erro'] = 'Preencha todos os campos obrigatórios.';
        header('Location: ../pages/gerir_ofertas.php');
        exit();
    }

    if (strtotime($data_fim) < strtotime($data_inicio)) {
        $_SESSION['mensagem_erro'] = 'Tente novamente, verifique as datas.';
        header('Location: ../pages/gerir_ofertas.php');
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO ofertas_empresas (titulo, descricao, requisitos, id_empresa, vagas, curso_relacionado, data_inicio, data_fim) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssisss", $titulo, $descricao, $requisitos, $id_empresa, $vagas, $curso_relacionado, $data_inicio, $data_fim);

    if ($stmt->execute()) {
        $_SESSION['mensagem_sucesso'] = 'Oferta publicada com sucesso!';
        header('Location:  admin_dashboard.php?page=dashboard');
        exit();
    } else {
        $_SESSION['mensagem_erro'] = 'Erro ao publicar a oferta.';
        header('Location: ../pages/gerir_ofertas.php');
        exit();
    }
}
