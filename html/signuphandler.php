<?php
include '../database/mysqli.php'; // Inclui a conexão com a base de dados

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar os dados do formulário
    $nome = trim($_POST['name']);
    $nr_processo = trim($_POST['processNumber']);
    $turma = trim($_POST['class']);
    $curso = trim($_POST['course']);
    $data_nascimento = $_POST['birthDate'];
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    // Validar se as senhas coincidem
    if ($password !== $confirm_password) {
        die("As senhas não coincidem! Tente novamente.");
    }

    // Hash da senha para armazenamento seguro
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // Upload do currículo
    $curriculo = $_FILES['resume'];
    $curriculo_nome = $curriculo['name'];
    $curriculo_tmp = $curriculo['tmp_name'];
    $curriculo_tamanho = $curriculo['size'];
    $curriculo_extensao = strtolower(pathinfo($curriculo_nome, PATHINFO_EXTENSION));
    $extensoes_permitidas = ['pdf', 'doc', 'docx'];

    if (!in_array($curriculo_extensao, $extensoes_permitidas)) {
        die("Apenas arquivos PDF, DOC ou DOCX são permitidos.");
    }

    if ($curriculo_tamanho > 2 * 1024 * 1024) { // Limite de 2MB
        die("O arquivo ultrapassa o limite de 2MB.");
    }

    // Garantir que o diretório de upload exista
    $destino_diretorio = "../uploads/curriculos/";
    if (!is_dir($destino_diretorio)) {
        mkdir($destino_diretorio, 0777, true);
    }

    $destino_curriculo = $destino_diretorio . uniqid() . "." . $curriculo_extensao;
    if (!move_uploaded_file($curriculo_tmp, $destino_curriculo)) {
        die("Erro ao fazer upload do arquivo. Tente novamente.");
    }

    // Inserir os dados no banco de dados
    $sql = "INSERT INTO alunos (Nome, nr_processo, Email, Password, Turma, Curso, Data_Nascimento, Curriculo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Erro na preparação da consulta: " . $conn->error);
    }

    $stmt->bind_param("ssssssss", $nome, $nr_processo, $email, $password_hashed, $turma, $curso, $data_nascimento, $destino_curriculo);

    if ($stmt->execute()) {
        echo "Registro realizado com sucesso!";
        header("Location: formlogin.php"); // Redirecionar para a página de login
        exit();
    } else {
        die("Erro ao registrar: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} else {
    die("Método inválido.");
}
