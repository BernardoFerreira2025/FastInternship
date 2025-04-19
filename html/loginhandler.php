<?php
include '../database/mysqli.php'; // Conexão com o banco de dados

// Garante que a sessão está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Função para verificar login e carregar dados na sessão
    function verify_login($query, $email, $password, $role, $redirect, $conn) {
        global $user_role, $redirect_page;

        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Verifica a senha hash
            if (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $role === 'empresa' ? $user['nome_empresa'] : ($user['nome'] ?? $user['username']);
                $_SESSION['user_role'] = $role;

                // Define ID e foto do usuário na sessão
                switch ($role) {
                    case 'professor':
                        $_SESSION['id_professor'] = $user['id_professor'];
                        $_SESSION['id_curso'] = $user['id_curso']; // <- esta linha é fundamental!
                        $_SESSION['foto'] = !empty($user['foto']) ? "../images/" . $user['foto'] : "../images/professor.png";
                        break;                 
                    case 'aluno':
                        $_SESSION['id_aluno'] = $user['id_aluno'];
                        $_SESSION['foto'] = !empty($user['foto']) ? "../images/" . $user['foto'] : "../images/default.png";
                        break;
                    case 'admin':
                        $_SESSION['id_utilizador'] = $user['id_utilizador'];    
                        $_SESSION['foto'] = !empty($user['foto']) ? "../images/" . $user['foto'] : "../images/default.png";
                        break;
                    case 'empresa':
                        $_SESSION['id_empresas'] = $user['id_empresas'];
                        $_SESSION['foto'] = !empty($user['foto']) ? "../images/" . $user['foto'] : "../images/default.png";
                        break;
                }

                // Define a role e a página de redirecionamento
                $user_role = $role;
                $redirect_page = $redirect;
                
                header("Location: $redirect_page");
                exit();
            } else {
                $_SESSION['error'] = "Palavra-passe incorreta, tente novamente.";
                $_SESSION['email_temp'] = $email; // Salva o e-mail para reutilizar
                header("Location: formlogin.php");
                exit();
            }            
        } else {
            $_SESSION['error'] = "E-mail não encontrado.";
            header("Location: formlogin.php");
            exit();
        }
    }

    // Verifica o domínio do e-mail e executa a função de login correspondente
    if (str_ends_with($email, "@escolaaugustogomes.pt")) {
        verify_login("SELECT id_aluno, nome, password, foto FROM alunos WHERE email = ?", $email, $password, 'aluno', 'aluno_dashboard.php', $conn);
    } elseif (str_ends_with($email, "@esag-edu.net")) {
        verify_login("SELECT id_professor, nome, password, foto, id_curso FROM professores WHERE email = ?", $email, $password, 'professor', 'professor_dashboard.php', $conn);
    } elseif (str_ends_with($email, "@admin.pt")) {
        verify_login("SELECT id_utilizador, username, password, foto FROM utilizadores WHERE email = ?", $email, $password, 'admin', 'admin_dashboard.php', $conn);
    } elseif (str_ends_with($email, "@gmail.com")) {
        verify_login("SELECT id_empresas, nome_empresa, password, foto FROM empresas WHERE email = ?", $email, $password, 'empresa', 'empresa_dashboard.php', $conn);
    } else {
        $_SESSION['error'] = "Domínio de e-mail inválido.";
        header("Location: formlogin.php");
        exit();
    }
} else {
    die("Método inválido.");
}
