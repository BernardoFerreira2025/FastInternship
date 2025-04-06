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

$mapa_perguntas = [
    "experiencia" => "Tem experiência na área?",
    "conhecimentos" => "Tem conhecimentos relevantes para a vaga?",
    "trabalho_em_equipa" => "Sente-se confortável a trabalhar em equipa?"
];

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
$query = "SELECT c.id_candidatura, a.id_aluno, a.nome, a.turma, a.nr_processo, a.email, a.curriculo, c.carta_motivacao, c.respostas, c.status_professor, c.status_empresa, e.nome_empresa 
          FROM candidaturas c 
          INNER JOIN alunos a ON c.id_aluno = a.id_aluno 
          INNER JOIN ofertas_empresas oe ON c.id_oferta = oe.id_oferta 
          INNER JOIN empresas e ON oe.id_empresa = e.id_empresas 
          WHERE c.id_oferta = ? AND oe.id_curso = ? AND a.id_curso = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $id_oferta, $id_curso, $id_curso);
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
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

<div class="users-container">
    <h2 class="users-header">Candidatos à Oferta</h2>
    <?php if ($result->num_rows > 0): ?>
        <div class="candidatos-grid">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="candidato-card">
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
                        <p><strong>Nome da Empresa:</strong> <?= htmlspecialchars($row['nome_empresa']); ?></p>
                    </div>
                    <div class="carta-motivacao">
                        <p><strong>Carta de Motivação:</strong></p>
                        <?= nl2br(htmlspecialchars($row['carta_motivacao'])); ?>
                    </div>
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
                                foreach ($mapa_perguntas as $chave => $texto) {
                                    $resposta = isset($respostas[$chave]) ? htmlspecialchars($respostas[$chave]) : "Não informado";
                                    echo "<tr><td><strong>$texto</strong></td><td>$resposta</td></tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                    <div class="status-container">
                        <p><strong>Status Professor:</strong> 
                            <span class="status-<?= htmlspecialchars($row['status_professor']); ?>">
                                <?= ucfirst($row['status_professor']); ?>
                            </span>
                        </p>
                        <p><strong>Status Empresa:</strong> 
                            <span class="status-<?= htmlspecialchars($row['status_empresa']); ?>">
                                <?= ucfirst($row['status_empresa']); ?>
                            </span>
                        </p>
                    </div>
                    <div class="acoes-professor">
                        <form method="post" action="pagesprofessores/processa_candidatura.php">
                            <input type="hidden" name="id_candidatura" value="<?= htmlspecialchars($row['id_candidatura']); ?>">
                            <input type="hidden" name="oferta_id" value="<?= htmlspecialchars($id_oferta); ?>">
                            <?php if ($row['status_professor'] == 'pendente'): ?>
                                <button type="submit" name="acao" value="aceitar" class="btn btn-success">Aceitar</button>
                                <button type="submit" name="acao" value="rejeitar" class="btn btn-danger">Rejeitar</button>
                            <?php else: ?>
                                <button type="submit" name="acao" value="cancelar" class="btn-danger">Cancelar</button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php else: ?>
        <p class="no-data">Nenhuma candidatura encontrada para esta oferta.</p>
    <?php endif; ?>
</div>

</body>
</html>
