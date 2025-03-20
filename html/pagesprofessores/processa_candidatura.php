<?php
// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
if (!isset($_POST['id_candidatura']) || !isset($_POST['acao'])) {
    die("Erro: Dados incompletos.");
}

$id_candidatura = intval($_POST['id_candidatura']);
$acao = $_POST['acao']; // "aceitar", "rejeitar" ou "cancelar"

// Determinar status
if ($acao === 'aceitar') {
    $status_professor = 'aprovado';
} elseif ($acao === 'rejeitar') {
    $status_professor = 'rejeitado';
} elseif ($acao === 'cancelar') {
    $status_professor = 'pendente'; // Define o status de volta para pendente
} else {
    die("Ação inválida.");
}

// Atualizar o status no banco de dados
$query = "UPDATE candidaturas SET status_professor = ? WHERE id_candidatura = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $status_professor, $id_candidatura);
$stmt->execute();

// Se a candidatura foi aceita, enviar e-mail
if ($acao === 'aceitar') {
    // Buscar informações do aluno e da oferta
    $query_info = "SELECT a.nome, a.turma, a.nr_processo, e.nome_empresa, oe.titulo 
                   FROM candidaturas c
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
        $turma = htmlspecialchars($info['turma']);
        $nr_processo = htmlspecialchars($info['nr_processo']);
        $nome_empresa = htmlspecialchars($info['nome_empresa']);
        $titulo_oferta = htmlspecialchars($info['titulo']);
        $email_destino = "fastinternship@gmail.com"; // E-mail fixo para notificação

        // Enviar e-mail
        $mail = new PHPMailer(true);
        try {
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'fastinternship@gmail.com';
            $mail->Password   = 'zesa fvuw lsbt tuni'; // Proteja essa credencial
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Definir remetente e destinatário
            $mail->setFrom('fastinternship@gmail.com', 'FastInternship');
            $mail->addAddress($email_destino, 'FastInternship');

            $mail->isHTML(true);
            $mail->Subject = "Candidatura Aprovada - FastInternship";
            $mail->Body    = "
                <h2>Uma candidatura foi aprovada!</h2>
                <p><strong>Empresa:</strong> $titulo_oferta</p>
                <p><strong>Aluno:</strong> $nome_aluno</p>
                <p><strong>Turma:</strong> $turma</p>
                <p><strong>Nº Processo:</strong> $nr_processo</p>
                <p>O aluno agora pode continuar o processo diretamente com a empresa.</p>
                <br>
                <p>Atenciosamente,</p>
                <p>FastInternship</p>
            ";

            $mail->send();
            echo "E-mail enviado com sucesso!";
        } catch (Exception $e) {
            echo "Erro ao enviar e-mail: " . $mail->ErrorInfo;
            exit();
        }
    }
}

// Redirecionar para a dashboard após a ação
header("Location: ../professor_dashboard.php?page=dashboard");
exit();
