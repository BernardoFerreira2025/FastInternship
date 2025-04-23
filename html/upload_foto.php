<?php
session_start();
include '../database/mysqli.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['user_role']) || !isset($_SESSION['username'])) {
    $_SESSION['error'] = "Sessão inválida. Faça login novamente.";
    header("Location: formlogin.php");
    exit();
}

$user_role = $_SESSION['user_role'];
$user_id = null;
$table = '';
$id_column = '';
$redirect_page = 'formlogin.php';

// Define a tabela, a coluna de ID e a página de redirecionamento com base no tipo de usuário
switch ($user_role) {
    case 'professor':
        $user_id = $_SESSION['id_professor'];
        $table = 'professores';
        $id_column = 'id_professor';
        $redirect_page = 'professor_dashboard.php';
        break;
    case 'aluno':
        $user_id = $_SESSION['id_aluno'];
        $table = 'alunos';
        $id_column = 'id_aluno';
        $redirect_page = 'aluno_dashboard.php';
        break;
    case 'admin':
        $user_id = $_SESSION['id_admin'];
        $table = 'utilizadores';
        $id_column = 'id_utilizador';
        $redirect_page = 'admin_dashboard.php';
        break;
    case 'empresa':
        $user_id = $_SESSION['id_empresas'];
        $table = 'empresas';
        $id_column = 'id_empresas';
        $redirect_page = 'empresa_dashboard.php';
        break;
    default:
        $_SESSION['error'] = "Tipo de usuário inválido.";
        header("Location: formlogin.php");
        exit();
}

// Verifica se um arquivo foi enviado
if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $foto = $_FILES['foto'];
    $extensao = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
    $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];

    // Verifica se a extensão é permitida
    if (!in_array($extensao, $extensoes_permitidas)) {
        $_SESSION['error'] = "Formato de imagem não permitido. Use JPG, PNG ou GIF.";
        header("Location: $redirect_page");
        exit();
    }

    // Define o nome do arquivo com base no ID do usuário para garantir que cada usuário tenha sua própria foto
    $novo_nome = $user_role . "_" . $user_id . "_" . uniqid() . "." . $extensao;
    $caminho_destino = "../images/" . $novo_nome;

    // Move o arquivo para a pasta de imagens
    if (move_uploaded_file($foto['tmp_name'], $caminho_destino)) {
        // Atualiza o caminho da foto no banco de dados
        $sql = "UPDATE $table SET foto = ? WHERE $id_column = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $novo_nome, $user_id);

        if ($stmt->execute()) {
            $_SESSION['foto'] = $novo_nome; // Atualiza a foto na sessão
            $_SESSION['success'] = "Foto de perfil atualizada com sucesso!";
        } else {
            $_SESSION['error'] = "Erro ao atualizar o banco de dados.";
        }
    } else {
        $_SESSION['error'] = "Erro ao enviar o arquivo.";
    }
} else {
    $_SESSION['error'] = "Nenhuma imagem foi enviada.";
}


header("Location: $redirect_page");
exit();
