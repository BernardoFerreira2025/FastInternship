<?php
require_once '../database/mysqli.php';

// Verificar se a sessão está ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se o aluno está autenticado
$id_aluno = $_SESSION['id_aluno'] ?? null;

if (!$id_aluno) {
    die("Você precisa estar autenticado para visualizar esta página.");
}

// Obter o histórico de candidaturas do aluno
$sql_historico = "SELECT c.id_candidatura, o.titulo, o.descricao, o.data_inicio, o.data_fim, 
                         e.nome_empresa, c.status_professor, c.status_empresa
                  FROM candidaturas c
                  INNER JOIN ofertas_empresas o ON c.id_oferta = o.id_oferta
                  INNER JOIN empresas e ON o.id_empresa = e.id_empresas
                  WHERE c.id_aluno = ?";
$stmt = $conn->prepare($sql_historico);
$stmt->bind_param("i", $id_aluno);
$stmt->execute();
$result = $stmt->get_result();
$candidaturas = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Remover candidatura (caso o botão seja clicado via POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_candidatura'])) {
    $id_candidatura = $_POST['id_candidatura'];

    $sql_delete = "DELETE FROM candidaturas WHERE id_candidatura = ? AND id_aluno = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("ii", $id_candidatura, $id_aluno);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Candidatura cancelada com sucesso!', 'id_candidatura' => $id_candidatura]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao cancelar a candidatura.']);
    }

    $stmt->close();
    exit(); // Terminar o script ao processar o POST
}

// Fechar conexão com o banco de dados
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/allcss.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Histórico de Candidaturas</title>
</head>
<body>
    <h1>Histórico de Candidaturas</h1>
    <div class="historico-section">
        <?php if (!empty($candidaturas)): ?>
            <?php foreach ($candidaturas as $candidatura): ?>
                <div class="historico-card" id="candidatura-<?php echo $candidatura['id_candidatura']; ?>">
                    <h3><?php echo htmlspecialchars($candidatura['titulo']); ?></h3>
                    <p><strong>Empresa:</strong> <?php echo htmlspecialchars($candidatura['nome_empresa']); ?></p>
                    <p><strong>Descrição:</strong> <?php echo htmlspecialchars($candidatura['descricao']); ?></p>
                    <p><strong>Data de Início:</strong> <?php echo htmlspecialchars($candidatura['data_inicio']); ?></p>
                    <p><strong>Data de Fim:</strong> <?php echo htmlspecialchars($candidatura['data_fim']); ?></p>
                    <p><strong>Status do Professor:</strong> 
                        <span class="status <?php echo htmlspecialchars($candidatura['status_professor']); ?>">
                            <?php echo htmlspecialchars($candidatura['status_professor']); ?>
                        </span>
                    </p>
                    <p><strong>Status da Empresa:</strong> 
                        <span class="status <?php echo htmlspecialchars($candidatura['status_empresa']); ?>">
                            <?php echo htmlspecialchars($candidatura['status_empresa']); ?>
                        </span>
                    </p>
                    <button class="btn-cancel" onclick="confirmCancel(<?php echo $candidatura['id_candidatura']; ?>)">Cancelar Candidatura</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Você ainda não se candidatou a nenhuma oferta.</p>
        <?php endif; ?>
    </div>

    <script>
        function confirmCancel(idCandidatura) {
            Swal.fire({
                title: 'Tem certeza?',
                text: "Deseja realmente cancelar esta candidatura?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FF4F4F',
                cancelButtonColor: '#4CAF50',
                confirmButtonText: 'Sim, cancelar',
                cancelButtonText: 'Não'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submeter a requisição para cancelar candidatura
                    fetch('aluno_dashboard.php?page=historico.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({ id_candidatura: idCandidatura })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: data.message,
                                confirmButtonColor: '#4CAF50'
                            }).then(() => {
                                // Remover o card do DOM
                                const card = document.getElementById(`candidatura-${data.id_candidatura}`);
                                if (card) card.remove();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: data.message,
                                confirmButtonColor: '#FF4F4F'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro ao processar a solicitação.',
                            text: error.message,
                            confirmButtonColor: '#FF4F4F'
                        });
                    });
                }
            });
        }
    </script>
</body>
</html>
