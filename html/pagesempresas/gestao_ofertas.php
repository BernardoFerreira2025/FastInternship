<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../database/mysqli.php';

// Verifica se o usuário está logado e é uma empresa
if (!isset($_SESSION['id_empresas'])) {
    $_SESSION['error'] = "Sessão inválida. Faça login novamente.";
    header("Location: ../formlogin.php");
    exit();
}

$id_empresas = $_SESSION['id_empresas'];

// Obtém o curso da empresa logada
$query = "SELECT id_curso FROM empresas WHERE id_empresas = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_empresas);
$stmt->execute();
$result = $stmt->get_result();
$empresa = $result->fetch_assoc();
$id_curso = $empresa['id_curso'] ?? null;

if (!$id_curso) {
    die("<p>Erro: A empresa não tem um curso associado.</p>");
}

// Consulta para as ofertas associadas ao curso
$queryOfertas = "
    SELECT 
        ofertas_empresas.*, 
        empresas.nome_empresa, 
        empresas.responsavel 
    FROM 
        ofertas_empresas
    INNER JOIN 
        empresas 
    ON 
        ofertas_empresas.id_empresa = empresas.id_empresas
    WHERE 
        ofertas_empresas.id_curso = ?
";

$stmtOfertas = $conn->prepare($queryOfertas);
$stmtOfertas->bind_param("i", $id_curso);
$stmtOfertas->execute();
$resultOfertas = $stmtOfertas->get_result();
?>

<div class="form-background">
    <div class="form-wrapper">
        <h1 class="users-header">Gerir Ofertas Publicadas</h1>

        <!-- Grade de Ofertas -->
        <div class="users-grid">
        <?php
        if ($resultOfertas->num_rows > 0) {
            while ($oferta = $resultOfertas->fetch_assoc()) {
                // Formatar datas para o padrão português
                $data_inicio = date('d-m-Y', strtotime($oferta['data_inicio']));
                $data_fim = date('d-m-Y', strtotime($oferta['data_fim']));

                echo "<div class='user-card'>";
                echo "<h3><i class='fas fa-briefcase'></i> " . htmlspecialchars($oferta['titulo']) . "</h3>";
                echo "<p><i class='fas fa-user-tie'></i> <strong>Responsável:</strong> " . htmlspecialchars($oferta['responsavel']) . "</p>";
                echo "<p><i class='fas fa-align-left'></i> <strong>Descrição:</strong> " . htmlspecialchars($oferta['descricao']) . "</p>";
                echo "<p><i class='fas fa-calendar-alt'></i> <strong>Data Início:</strong> " . $data_inicio . "</p>";
                echo "<p><i class='fas fa-calendar-check'></i> <strong>Data Fim:</strong> " . $data_fim . "</p>";
                echo "<p><i class='fas fa-users'></i> <strong>Vagas:</strong> " . htmlspecialchars($oferta['vagas']) . "</p>";
                echo "<div class='user-actions'>";
                echo "<a href='empresa_dashboard.php?page=editar_oferta&id=" . $oferta['id_oferta'] . "' class='edit'>";
                echo "<i class='fas fa-pen-to-square action-icon'></i> Editar</a>";
                echo "<a href='pagesempresas/excluir_oferta.php?id=" . $oferta['id_oferta'] . "' class='delete'>";
                echo "<i class='fas fa-trash action-icon'></i> Excluir</a>";
                echo "</div>";

                echo "</div>";
            }
        } else {
            echo "<p>Nenhuma oferta encontrada para o seu curso.</p>";
        }
        ?>
        </div>
    </div>
</div>

<?php
// Exibe mensagens de sucesso ou erro
if (isset($_SESSION['mensagem_sucesso'])) {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                const toast = document.createElement('div');
                toast.className = 'toast-message toast-success';
                toast.textContent = '" . addslashes($_SESSION['mensagem_sucesso']) . "';
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 4000);
            });
          </script>";
    unset($_SESSION['mensagem_sucesso']);
}

if (isset($_SESSION['mensagem_erro'])) {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                const toast = document.createElement('div');
                toast.className = 'toast-message toast-error';
                toast.textContent = '" . addslashes($_SESSION['mensagem_erro']) . "';
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 4000);
            });
          </script>";
    unset($_SESSION['mensagem_erro']);
}
?>
