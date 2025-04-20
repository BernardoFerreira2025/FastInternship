<?php
require_once '../database/mysqli.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se a empresa está logada
if (!isset($_SESSION['id_empresas'])) {
    header("Location: ../formlogin.php");
    exit();
}

$id_empresa = $_SESSION['id_empresas'];

$mapa_perguntas = [
    "experiencia" => "Tem experiência na área?",
    "conhecimentos" => "Tem conhecimentos relevantes para a vaga?",
    "trabalho_em_equipa" => "Sente-se confortável a trabalhar em equipa?"
];

// Buscar nome da empresa
$sql_empresa = "SELECT nome_empresa FROM empresas WHERE id_empresas = ?";
$stmt_empresa = $conn->prepare($sql_empresa);
$stmt_empresa->bind_param("i", $id_empresa);
$stmt_empresa->execute();
$result_empresa = $stmt_empresa->get_result();
$empresa = $result_empresa->fetch_assoc();
$nome_empresa = $empresa['nome_empresa'] ?? "Empresa Desconhecida";

// Verifica se foi passado o ID da oferta
if (!isset($_GET['oferta_id']) || empty($_GET['oferta_id'])) {
    header("Location: empresa_dashboard.php?page=gestao_ofertas");
    exit();
}

$id_oferta = intval($_GET['oferta_id']);

// Verifica se a oferta pertence MESMO à empresa logada
$sql_verifica_oferta = "SELECT id_oferta FROM ofertas_empresas WHERE id_oferta = ? AND id_empresa = ?";
$stmt_verifica = $conn->prepare($sql_verifica_oferta);
$stmt_verifica->bind_param("ii", $id_oferta, $id_empresa);
$stmt_verifica->execute();
$res_verifica = $stmt_verifica->get_result();

if ($res_verifica->num_rows === 0) {
    die("Acesso não autorizado a esta oferta.");
}

// Buscar os dados dos candidatos
$query = "SELECT c.id_candidatura, 
                a.nome, a.turma, a.nr_processo, a.email, a.curriculo,
                c.carta_motivacao, c.respostas, 
                c.status_empresa, c.status_professor,
                p.nome AS nome_professor, p.email AS email_professor
         FROM candidaturas c
         INNER JOIN alunos a ON c.id_aluno = a.id_aluno
         INNER JOIN ofertas_empresas oe ON c.id_oferta = oe.id_oferta
         INNER JOIN professores p ON a.id_curso = p.id_curso
         WHERE c.id_oferta = ? AND oe.id_empresa = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $id_oferta, $id_empresa);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidatos à Oferta</title>
    <link rel="stylesheet" href="../assets/css/allcss.css">
</head>
<body>

    <div class="users-container">
        <!-- Nome da Empresa no Título -->
        <h2 class="users-header"><?= htmlspecialchars($nome_empresa); ?></h2>

        <?php if ($result->num_rows > 0): ?>
            <div class="candidatos-grid">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="candidato-card">

                        <!-- Informações do Aluno -->
                        <div class="info-box">
                            <p><strong>Nome do Aluno:</strong> <?= htmlspecialchars($row['nome']); ?></p>
                            <p>
                                <strong>Currículo:</strong>
                                <?php if (!empty($row['curriculo'])): ?>
                                    <a href="../uploads/<?= htmlspecialchars($row['curriculo']); ?>" download target="_blank" class="curriculo-link-editar" title="Transferir Currículo">
                                        <i class="fas fa-file-download"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="no-cv">Nenhum currículo enviado</span>
                                <?php endif; ?>
                            </p>
                            <p><strong>Turma:</strong> <?= htmlspecialchars($row['turma']); ?></p>
                            <p><strong>Nº Processo:</strong> <?= htmlspecialchars($row['nr_processo']); ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($row['email']); ?></p>
                        </div>

                        <hr>

                        <!-- Informações do Professor -->
                        <div class="info-box">
                            <p><strong>Professor Responsável:</strong> <?= htmlspecialchars($row['nome_professor']); ?></p>
                            <p><strong>Email do Professor:</strong> <?= htmlspecialchars($row['email_professor']); ?></p>
                        </div>

                        <hr>

                        <!-- Carta de Motivação -->
                        <div class="info-box">
                            <p><strong>Carta de Motivação:</strong></p>
                            <div class="carta-motivacao"><?= nl2br(htmlspecialchars($row['carta_motivacao'])); ?></div>
                        </div>

                        <hr>

                        <!-- Exibição das Perguntas e Respostas -->
                        <div class="info-box">
                            <p><strong>Respostas do Aluno:</strong></p>
                            <table class="respostas-table">
                                <thead>
                                    <tr>
                                        <th>Pergunta</th>
                                        <th>Resposta</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $respostas = json_decode($row['respostas'], true);
                                        foreach ($mapa_perguntas as $chave_pergunta => $texto_pergunta) {
                                            $resposta = isset($respostas[$chave_pergunta]) ? htmlspecialchars($respostas[$chave_pergunta]) : "Não informado";
                                            echo "<tr><td><strong>$texto_pergunta</strong></td><td>$resposta</td></tr>";
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <hr>

                        <!-- Status do Professor e Empresa -->
                        <div class="status-container">
                            <p><strong>Status do Professor:</strong> 
                                <span class="status-<?= htmlspecialchars($row['status_professor']); ?>">
                                    <?= ucfirst($row['status_professor']); ?>
                                </span>
                            </p>

                            <p><strong>Status da Empresa:</strong> 
                                <span class="status-<?= htmlspecialchars($row['status_empresa']); ?>">
                                    <?= ucfirst($row['status_empresa']); ?>
                                </span>
                            </p>
                        </div>

                        <hr>

                        <!-- Botões de Aceitar/Rejeitar -->
                        <?php if ($row['status_professor'] == 'aprovado' && $row['status_empresa'] == 'pendente'): ?>
                            <div class="acoes-empresa">
                                <form method="post" action="pagesempresas/processa_candidatura_empresa.php">
                                    <input type="hidden" name="id_candidatura" value="<?= htmlspecialchars($row['id_candidatura']); ?>">
                                    <button type="submit" name="acao" value="aceitar" class="btn btn-success">Aceitar</button>
                                    <button type="submit" name="acao" value="rejeitar" class="btn btn-danger">Rejeitar</button>
                                </form>
                            </div>
                        <?php elseif ($row['status_professor'] == 'pendente'): ?>
                            <p class="alerta">⏳ Aguardar aprovação do professor antes de aceitar ou rejeitar.</p>
                        <?php endif; ?>

                        <!-- Botão de Cancelar (Se já aprovado ou rejeitado) -->
                        <?php if ($row['status_empresa'] == 'aprovado' || $row['status_empresa'] == 'rejeitado'): ?>
                            <div class="acoes-empresa">
                                <form method="post" action="pagesempresas/processa_candidatura_empresa.php">
                                    <input type="hidden" name="id_candidatura" value="<?= htmlspecialchars($row['id_candidatura']); ?>">
                                    <button type="submit" name="acao" value="cancelar" class="btn btn-warning">Cancelar</button>
                                </form>
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="no-data">Não foram encontradas candidaturas para esta oferta.</p>
        <?php endif; ?>
    </div>

</body>
</html>
