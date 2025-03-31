<?php
// Conexão com o banco de dados
require_once '../database/mysqli.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o professor está logado
if (!isset($_SESSION['id_professor'])) {
    die("Acesso negado.");
}

$id_professor = $_SESSION['id_professor'];

// Buscar o curso do professor logado
$sql_curso = "SELECT id_curso FROM professores WHERE id_professor = ?";
$stmt_curso = $conn->prepare($sql_curso);
$stmt_curso->bind_param("i", $id_professor);
$stmt_curso->execute();
$result_curso = $stmt_curso->get_result();

if ($result_curso->num_rows === 0) {
    die("Erro: Curso do professor não encontrado.");
}

$row_curso = $result_curso->fetch_assoc();
$id_curso = $row_curso['id_curso'];

// Verifica se foi passado o ID da oferta
if (!isset($_GET['oferta_id']) || empty($_GET['oferta_id'])) {
    die("Erro: ID da oferta não especificado.");
}

$id_oferta = intval($_GET['oferta_id']);

// Buscar candidatos SOMENTE se a oferta e o aluno pertencem ao curso do professor
$query = "SELECT c.id_candidatura, 
                 a.nome, a.turma, a.nr_processo, a.email,
                 c.carta_motivacao, c.respostas, 
                 c.status_professor, c.status_empresa, 
                 e.nome_empresa 
          FROM candidaturas c
          INNER JOIN alunos a ON c.id_aluno = a.id_aluno
          INNER JOIN ofertas_empresas oe ON c.id_oferta = oe.id_oferta
          INNER JOIN empresas e ON oe.id_empresa = e.id_empresas
          WHERE c.id_oferta = ? 
          AND oe.id_curso = ? 
          AND a.id_curso = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $id_oferta, $id_curso, $id_curso);
$stmt->execute();
$result = $stmt->get_result();

// Buscar o nome da empresa que publicou a oferta
$nome_empresa = "Empresa não encontrada"; // Valor padrão

$sql_nome_empresa = "SELECT e.nome_empresa 
                     FROM ofertas_empresas oe
                     INNER JOIN empresas e ON oe.id_empresa = e.id_empresas
                     WHERE oe.id_oferta = ?";
$stmt_nome = $conn->prepare($sql_nome_empresa);
$stmt_nome->bind_param("i", $id_oferta);
$stmt_nome->execute();
$result_nome = $stmt_nome->get_result();

if ($result_nome->num_rows > 0) {
    $row_nome = $result_nome->fetch_assoc();
    $nome_empresa = $row_nome['nome_empresa'];
}

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
        <!-- Nome da empresa que publicou a oferta -->
        <h2 class="users-header"><?= htmlspecialchars($nome_empresa); ?></h2>

        <?php if ($result->num_rows > 0): ?>
            <div class="candidatos-grid">
                <?php do { ?>
                    <div class="candidato-card">

                        <!-- Informações do Aluno -->
                        <div class="info-box">
                            <p><strong>Nome do Aluno:</strong> <?= htmlspecialchars($row_empresa['nome']); ?></p>
                            <p><strong>Turma:</strong> <?= htmlspecialchars($row_empresa['turma']); ?></p>
                            <p><strong>Nº Processo:</strong> <?= htmlspecialchars($row_empresa['nr_processo']); ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($row_empresa['email']); ?></p>
                        </div>

                        <hr>

                        <!-- Carta de Motivação -->
                        <div class="info-box">
                            <p><strong>Carta de Motivação:</strong></p>
                            <div class="carta-motivacao"><?= nl2br(htmlspecialchars($row_empresa['carta_motivacao'])); ?></div>
                        </div>

                        <hr>

                        <!-- Respostas do Aluno -->
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
                                        $mapa_perguntas = [
                                            "experiencia" => "Possui experiência na área?",
                                            "conhecimentos" => "Quais conhecimentos relevantes possui para a vaga?",
                                            "trabalho_em_equipa" => "Sente-se confortável a trabalhar em equipa?"
                                        ];

                                        $respostas = json_decode($row_empresa['respostas'], true);
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
                            <p><strong>Status Professor:</strong> 
                                <span class="status-<?= htmlspecialchars($row_empresa['status_professor']); ?>">
                                    <?= ucfirst($row_empresa['status_professor']); ?>
                                </span>
                            </p>

                            <p><strong>Status Empresa:</strong> 
                                <span class="status-<?= htmlspecialchars($row_empresa['status_empresa']); ?>">
                                    <?= ucfirst($row_empresa['status_empresa']); ?>
                                </span>
                            </p>
                        </div>

                        <hr>

                        <!-- Botões de Aceitar/Rejeitar e Cancelar -->
                        <div class="acoes-professor">
                            <form method="post" action="pagesprofessores/processa_candidatura.php">
                                <input type="hidden" name="id_candidatura" value="<?= htmlspecialchars($row_empresa['id_candidatura']); ?>">
                                <input type="hidden" name="oferta_id" value="<?= htmlspecialchars($id_oferta); ?>">

                                <?php if ($row_empresa['status_professor'] == 'pendente'): ?>
                                    <!-- O botão Cancelar não aparece se o status já for pendente -->
                                    <button type="submit" name="acao" value="aceitar" class="btn btn-success">Aceitar</button>
                                    <button type="submit" name="acao" value="rejeitar" class="btn btn-danger">Rejeitar</button>

                                <?php elseif ($row_empresa['status_professor'] == 'aprovado' || $row_empresa['status_professor'] == 'rejeitado'): ?>
                                    <button type="submit" name="acao" value="cancelar" class="btn-cancel">Cancelar</button>
                                <?php endif; ?>
                            </form>
                        </div>

                    </div>
                <?php } while ($row_empresa = $result->fetch_assoc()); ?>
            </div>
        <?php else: ?>
            <p class="no-data">Nenhuma candidatura encontrada para esta oferta.</p>
        <?php endif; ?>
    </div>

</body>
</html>
