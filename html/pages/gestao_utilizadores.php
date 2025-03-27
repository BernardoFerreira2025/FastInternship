<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../database/mysqli.php';

$toast_message = "";
if (isset($_SESSION['toast_message'])) {
    $toast_message = $_SESSION['toast_message'];
    unset($_SESSION['toast_message']);
}

$empresas = $conn->query("SELECT id_empresas, nome_empresa, email, telefone, responsavel, morada, cod_postal, Localidade, foto, id_curso FROM empresas");
$alunos = $conn->query("SELECT id_aluno, Nome, Email, id_curso, Turma, nr_processo, Curriculo, foto FROM alunos");
$professores = $conn->query("
    SELECT p.id_professor, p.nome, p.email, p.foto, c.nome AS curso_nome
    FROM professores p
    INNER JOIN cursos c ON p.id_curso = c.id_curso
");

?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/allcss.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Gestão de Utilizadores</title>
</head>
<body>
<?php if (!empty($toast_message)) { ?>
    <div id="toast" class="toast-message toast-success"><?php echo htmlspecialchars($toast_message); ?></div>
<?php } ?>

<div class="users-container">
    <h2 class="users-header">Gestão de Utilizadores</h2>
    <div class="filter-buttons">
        <button class="filter-btn" onclick="showSection('alunos')"><i class="fas fa-user-graduate"></i> Alunos</button>
        <button class="filter-btn" onclick="showSection('professores')"><i class="fas fa-chalkboard-teacher"></i> Professores</button>
        <button class="filter-btn" onclick="showSection('empresas')"><i class="fas fa-building"></i> Empresas</button>
    </div>

<!-- Seção Alunos -->
<div id="alunos" class="section active">
    <h2 class="section-header">Alunos</h2>
    <div class="users-grid">
        <?php while ($aluno = $alunos->fetch_assoc()) { ?>
            <div class="user-card">
                <div class="profile-pic-container">
                    <img src="<?php echo !empty($aluno['foto']) ? '../images/'.$aluno['foto'] : '../images/student_default.png'; ?>" alt="Foto do Aluno">
                </div>
                <h3><?php echo htmlspecialchars($aluno['Nome']); ?></h3>
                <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <?php echo htmlspecialchars($aluno['Email']); ?></p>
                <p><i class="fas fa-id-badge"></i> <strong>Nº Processo:</strong> <?php echo htmlspecialchars($aluno['nr_processo']); ?></p>
                <p><i class="fas fa-users"></i> <strong>Turma:</strong> <?php echo htmlspecialchars($aluno['Turma']); ?></p>

                <p>
                    <strong><i class="fas fa-file-pdf"></i> Currículo:</strong>
                    <?php if (!empty($aluno['Curriculo'])): ?>
                        <a href="../uploads/<?php echo htmlspecialchars($aluno['Curriculo']); ?>" download target="_blank" title="Transferir Currículo">
                            <i class="fas fa-file-download" style="color: #4f8cff; font-size: 1.2rem; margin-left: 8px;"></i>
                        </a>
                    <?php else: ?>
                        <span class="no-cv">Nenhum currículo enviado</span>
                    <?php endif; ?>
                </p>

                <div class="user-actions">
                    <a href='admin_dashboard.php?page=editar_aluno&id=<?php echo $aluno['id_aluno']; ?>' class="edit"><i class="fas fa-pen-to-square action-icon"></i> Editar</a>
                    <a href='pages/excluir_aluno.php?id=<?php echo $aluno['id_aluno']; ?>' class="delete"><i class="fas fa-trash action-icon"></i> Excluir</a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<div id="empresas" class="section">
    <h2 class="section-header">Empresas</h2>
    <div class="users-grid">
        <?php while ($empresa = $empresas->fetch_assoc()) { ?>
            <div class="user-card">
                <div class="profile-pic-container">
                    <img src="<?php echo !empty($empresa['foto']) ? '../images/'.$empresa['foto'] : '../images/company_default.png'; ?>" alt="Foto da Empresa">
                </div>
                <h3><?php echo htmlspecialchars($empresa['nome_empresa']); ?></h3>
                <p><i class="fas fa-user-tie"></i> <strong>Responsável:</strong> <?php echo htmlspecialchars($empresa['responsavel']); ?></p>
                <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <?php echo htmlspecialchars($empresa['email']); ?></p>
                <p><i class="fas fa-phone"></i> <strong>Telefone:</strong> <?php echo htmlspecialchars($empresa['telefone']); ?></p>
                <p><i class="fas fa-map-marked-alt"></i> <strong>Morada:</strong> <?php echo htmlspecialchars($empresa['morada']); ?></p>
                <p><i class="fas fa-mail-bulk"></i> <strong>Código Postal:</strong> <?php echo htmlspecialchars($empresa['cod_postal']); ?></p>
                <p><i class="fas fa-map-marker-alt"></i> <strong>Localidade:</strong> <?php echo htmlspecialchars($empresa['Localidade']); ?></p>
                <div class="user-actions">
                    <a href='admin_dashboard.php?page=editar_empresa&id=<?php echo $empresa['id_empresas']; ?>' class="edit"><i class="fas fa-pen-to-square action-icon"></i> Editar</a>
                    <a href='pages/excluir_empresa.php?id=<?php echo $empresa['id_empresas']; ?>' class="delete"><i class="fas fa-trash action-icon"></i> Excluir</a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<div id="professores" class="section">
    <h2 class="section-header">Professores</h2>
    <div class="users-grid">
        <?php while ($professor = $professores->fetch_assoc()) { ?>
            <div class="user-card">
                <div class="profile-pic-container">
                    <img src="<?php echo !empty($professor['foto']) ? '../images/'.$professor['foto'] : '../images/professor_default.png'; ?>" alt="Foto do Professor">
                </div>
                <h3><?php echo htmlspecialchars($professor['nome']); ?></h3>
                <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <?php echo htmlspecialchars($professor['email']); ?></p>
                <p><i class="fas fa-graduation-cap"></i> <strong>Curso:</strong>
                    <?php
                        $curso = $professor['curso_nome'];
                        if ($curso === 'Técnico(a) de Gestão e Programação de Sistemas Informáticos') {
                            echo 'TGPSI';
                        } else {
                            echo htmlspecialchars($curso);
                        }
                    ?>
                </p>
                <div class="user-actions">
                    <a href='admin_dashboard.php?page=editar_professor&id=<?php echo $professor['id_professor']; ?>' class="edit"><i class="fas fa-pen-to-square action-icon"></i> Editar</a>
                    <a href='pages/excluir_professor.php?id=<?php echo $professor['id_professor']; ?>' class="delete"><i class="fas fa-trash action-icon"></i> Excluir</a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<script>
    function showSection(sectionId) {
        document.querySelectorAll('.section').forEach(section => section.classList.remove('active'));
        document.getElementById(sectionId).classList.add('active');
    }

    document.addEventListener("DOMContentLoaded", function() {
        let toast = document.getElementById("toast");
        if (toast) {
            setTimeout(function() {
                toast.style.display = "none";
            }, 4000);
        }
    });
</script>
</body>
</html>
