<?php
// Conexão com o banco de dados
include 'C:/xampp/htdocs/pap/database/mysqli.php';

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
                 a.nome, a.turma, a.nr_processo, 
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
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidatos à Oferta</title>
    <link rel="stylesheet" href="../assets/css/allcss.css"> <!-- Garantindo o CSS correto -->
</head>
<body>

    <div class="users-container">
        <h2 class="users-header">Candidatos à Oferta</h2>

        <?php if ($result->num_rows > 0): ?>
            <div class="candidatos-grid">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="candidato-card">
                        <!-- Nome da empresa como título -->
                        <h3 class="candidato-nome"><?= htmlspecialchars($row['nome_empresa']); ?></h3>

                        <?php 
                            // Separar o nome para pegar apenas primeiro e último
                            $partes_nome = explode(" ", trim($row['nome']));
                            $primeiro_nome = $partes_nome[0]; // Primeiro nome
                            $ultimo_nome = end($partes_nome); // Último nome
                            $nome_formatado = $primeiro_nome . " " . $ultimo_nome;
                        ?>

                        <!-- Nome do aluno (primeiro e último), turma e número de processo na mesma linha -->
                        <p><strong>Aluno:</strong> <?= htmlspecialchars($nome_formatado); ?> | <strong>Turma:</strong> <?= htmlspecialchars($row['turma']); ?> | <strong>Nº Processo:</strong> <?= htmlspecialchars($row['nr_processo']); ?></p>
                        
                        <p><strong>Carta de Motivação:</strong> <?= nl2br(htmlspecialchars($row['carta_motivacao'])); ?></p>

                        <p><strong>Respostas:</strong></p>
                        <ul>
                            <?php 
                                $respostas = json_decode($row['respostas'], true);
                                if (is_array($respostas)) {
                                    foreach ($respostas as $pergunta => $resposta) {
                                        echo "<li><strong>" . htmlspecialchars($pergunta) . ":</strong> " . htmlspecialchars($resposta) . "</li>";
                                    }
                                } else {
                                    echo "<li>Erro ao processar respostas.</li>";
                                }
                            ?>
                        </ul>

                        <p><strong>Status Empresa:</strong> <?= htmlspecialchars($row['status_empresa']); ?></p>

                        <div class="acoes-professor">
                            <?php if ($row['status_professor'] == 'pendente'): ?>
                                <form method="post" action="processa_candidatura.php">
                                    <input type="hidden" name="id_candidatura" value="<?= $row['id_candidatura']; ?>">
                                    <button type="submit" name="acao" value="aceitar" class="btn btn-success">Aceitar</button>
                                    <button type="submit" name="acao" value="rejeitar" class="btn btn-danger">Rejeitar</button>
                                </form>
                            <?php else: ?>
                                <p class="status-finalizado">Status: <?= ucfirst($row['status_professor']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="no-data">Nenhuma candidatura encontrada para esta oferta.</p>
        <?php endif; ?>
    </div>

</body>
</html>
