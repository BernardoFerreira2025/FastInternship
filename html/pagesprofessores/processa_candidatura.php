<?php
// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Iniciar buffer para evitar envio de headers antes do redirecionamento
ob_start();

// Conexão com o banco de dados
require_once '../../database/mysqli.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';

// Verifica se o professor está logado
if (!isset($_SESSION['id_professor'])) {
    die("Acesso negado.");
}

$id_professor = $_SESSION['id_professor'];

// Verifica se os dados foram recebidos
if (!isset($_POST['id_candidatura']) || !isset($_POST['acao']) || !isset($_POST['oferta_id'])) {
    die("Erro: Dados incompletos.");
}

$id_candidatura = intval($_POST['id_candidatura']);
$acao = $_POST['acao'];
$id_oferta = intval($_POST['oferta_id']);

// Determinar status
if ($acao === 'aceitar') {
    $status_professor = 'aprovado';
} elseif ($acao === 'rejeitar') {
    $status_professor = 'rejeitado';
} elseif ($acao === 'cancelar') {
    $status_professor = 'pendente';
} else {
    die("Ação inválida.");
}

// Atualizar o status no banco de dados
$query = "UPDATE candidaturas SET status_professor = ? WHERE id_candidatura = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $status_professor, $id_candidatura);
$stmt->execute();

// Verificar se o botão "Cancelar" foi clicado
if ($acao === 'cancelar') {
    // Limpar o buffer de saída antes de redirecionar para a mesma página de candidatos
    ob_end_clean();
    header("Location: ../pagesprofessores/alunos_candidatos.php?oferta_id=$id_oferta");
    exit();
}

// Se a candidatura foi aceita, enviar e-mail
if ($acao === 'aceitar') {
    $query_info = "SELECT a.nome, e.nome_empresa, e.email FROM candidaturas c 
                  INNER JOIN alunos a ON c.id_aluno = a.id_aluno 
                  INNER JOIN ofertas_empresas oe ON c.id_oferta = oe.id_oferta 
                  INNER JOIN empresas e ON oe.id_empresa = e.id_empresas 
                  WHERE c.id_candidatura = ?";
    $stmt_info = $conn->prepare($query_info);
    $stmt_info->bind_param("i", $id_candidatura);
    $stmt_info->execute();
    $result_info = $stmt_info->get_result();
    $info = $result_info->fetch_assoc();

    if ($info) {
        $nome_aluno = htmlspecialchars($info['nome']);
        $nome_empresa = htmlspecialchars($info['nome_empresa']);
        $email_empresa = htmlspecialchars($info['email']);
        $email_destino = 'testesfastpap@gmail.com';
        $senha_predefinida = '1234';
        $link_login = 'http://localhost/pap/html/formlogin.php';

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'fastinternship@gmail.com';
            $mail->Password = 'zesa fvuw lsbt tuni';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('fastinternship@gmail.com', 'FastInternship');
            $mail->addAddress($email_destino, 'Equipa FastInternship');

            $mail->isHTML(true);
            $mail->Subject = "Candidatura Recebida";
            $mail->Body = "<div style='font-family: Arial, sans-serif; color: #333;'>
                            <h2 style='color: #0056b3;'>📢 Nova Candidatura Recebida!</h2>
                            <p>Olá,</p>
                            <p>O aluno <strong>$nome_aluno</strong> candidatou-se a uma oferta de estágio da empresa <strong>$nome_empresa</strong>.</p>
                            <hr>
                            <p>💼 <strong>Empresa:</strong> $nome_empresa</p>
                            <p>✉️ <strong>Email da Empresa:</strong> $email_empresa</p>
                            <p>🔑 <strong>Senha Padrão:</strong> $senha_predefinida</p>
                            <br>
                            <p>🔗 <a href='$link_login' style='color: #0056b3;'>Clique aqui para fazer login</a></p>
                            <br>
                            <p>Com os melhores cumprimentos,<br><strong>Equipa FastInternship</strong></p>
                          </div>";

            $mail->SMTPDebug = 0;
            $mail->send();
        } catch (Exception $e) {
            echo "Erro ao enviar e-mail: " . $mail->ErrorInfo;
            exit();
        }
    }
}

// Limpar o buffer de saída antes de redirecionar para o dashboard
ob_end_clean();
header("Location: ../professor_dashboard.php?page=dashboard");
exit();
