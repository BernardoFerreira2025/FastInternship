<?php
include '../database/mysqli.php'; // Inclui a conexão com a base de dados
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Função para verificar login
    function verify_login($query, $email, $password, $role, $redirect, $conn) {
        global $user_role, $redirect_page;

        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['Password'])) {
                $_SESSION['username'] = $role === 'empresa' ? $user['nome_empresa'] : $user['Nome'] ?? $user['username'];
                $_SESSION['user_role'] = $role;
                $user_role = $role;
                $redirect_page = $redirect;
                return true;
            } else {
                $_SESSION['error'] = "Senha incorreta.";
                return false;
            }
        } else {
            $_SESSION['error'] = "E-mail não encontrado.";
            return false;
        }
    }

    // Login de Aluno
    if (str_ends_with($email, "@escolaaugustogomes.pt")) {
        $sql_aluno = "SELECT id_aluno, Nome, Password FROM alunos WHERE Email = ?";
        $stmt = $conn->prepare($sql_aluno);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['Password'])) {
                $_SESSION['id_aluno'] = $user['id_aluno']; // Adiciona o ID do aluno à sessão
                $_SESSION['username'] = $user['Nome'];
                $_SESSION['user_role'] = 'aluno';
                header("Location: aluno_dashboard.php");
                exit();
            } else {
                $_SESSION['error'] = "Senha incorreta.";
            }
        } else {
            $_SESSION['error'] = "E-mail não encontrado.";
        }
            exit();
        }
    }

    // Login de Professor/Admin
    if (str_ends_with($email, "@esag-edu.net")) {
        $sql_professor = "SELECT username, Password FROM utilizadores WHERE email = ?";
        if (verify_login($sql_professor, $email, $password, 'admin', 'admin_dashboard.php', $conn)) {
            header("Location: $redirect_page");
            exit();
        }
    }

    // Login de Empresa
    if (str_ends_with($email, "@gmail.com")) {
        $sql_empresa = "SELECT nome_empresa, Password FROM empresas WHERE email = ?";
        if (verify_login($sql_empresa, $email, $password, 'empresa', 'empresa_dashboard.php', $conn)) {
            header("Location: $redirect_page");
            exit();
        }
    }

    // Caso nenhum dos casos seja válido
    $_SESSION['error'] = "Credenciais inválidas. Tente novamente.";
    header("Location: formlogin.php");
    exit();
{
    die("Método inválido.");
}
?>
