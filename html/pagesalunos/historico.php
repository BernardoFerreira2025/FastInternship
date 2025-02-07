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

// Variável para armazenar mensagem de status
$toastMessage = null;
if (isset($_SESSION['toastMessage'])) {
    $toastMessage = $_SESSION['toastMessage'];
    unset($_SESSION['toastMessage']); // Remove a mensagem após usá-la
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
        $_SESSION['toastMessage'] = [
            'type' => 'success',
            'title' => 'Candidatura cancelada com sucesso!',
        ];
    } else {
        $_SESSION['toastMessage'] = [
            'type' => 'error',
            'title' => 'Erro ao cancelar a candidatura. Tente novamente.',
        ];
    }

    $stmt->close();
    
    $_SESSION['toastMessage'] = [
        'type' => 'success',
        'title' => 'Candidatura cancelada com sucesso!',
    ];
    // Redirecionar para verofertas.php com o pop-up configurado
    header("Location: aluno_dashboard.php?page=verofertas");
    exit();
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
                <div class="historico-card">
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
                    <form method="post" onsubmit="return confirmCancel(event, this)">
                        <input type="hidden" name="id_candidatura" value="<?php echo $candidatura['id_candidatura']; ?>">
                        <button type="submit" class="btn-cancel">Cancelar Candidatura</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Você ainda não se candidatou a nenhuma oferta.</p>
        <?php endif; ?>
    </div>

    <script>
        function confirmCancel(event, form) {
            event.preventDefault(); // Evita o envio automático do formulário
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
                    form.submit(); // Submete o formulário se confirmado
                }
            });
        }
    </script>
</body>
</html>
