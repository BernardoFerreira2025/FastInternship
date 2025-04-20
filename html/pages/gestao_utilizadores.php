<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../database/mysqli.php';

// Mensagem de pop-up (toast)
$toast_message = "";
$toast_class = "toast-success"; // padrão

if (isset($_SESSION['toast_message'])) {
    $toast_message = $_SESSION['toast_message'];

    // Se contiver a palavra "erro" OU "excluíd" (para exclusões serem a vermelho)
    if (stripos($toast_message, 'erro') !== false || stripos($toast_message, 'exclu') !== false) {
        $toast_class = "toast-error";
    }

    unset($_SESSION['toast_message']);
}

$empresas = $conn->query("
    SELECT e.id_empresas, e.nome_empresa, e.email, e.telefone, e.responsavel, 
           e.morada, e.cod_postal, e.Localidade, e.foto, e.id_curso, 
           c.nome AS curso_nome
    FROM empresas e
    LEFT JOIN cursos c ON e.id_curso = c.id_curso
");

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
    <title>Gestão de Utilizadores</title>
    <link rel="stylesheet" href="../assets/css/allcss.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php if (!empty($toast_message)) { ?>
    <div id="toast" class="toast-message <?= $toast_class ?>">
        <?= htmlspecialchars($toast_message) ?>
    </div>
<?php } ?>

<div class="oferta-container">
    <h2 class="oferta-titulo">Gestão de Utilizadores</h2>
    <div class="filter-buttons">
        <button class="filter-btn" onclick="showSection('alunos')"><i class="fas fa-user-graduate"></i> Alunos</button>
        <button class="filter-btn" onclick="showSection('professores')"><i class="fas fa-chalkboard-teacher"></i> Professores</button>
        <button class="filter-btn" onclick="showSection('empresas')"><i class="fas fa-building"></i> Empresas</button>
    </div>

   <!-- Alunos -->
<div id="alunos" class="section active">
    <h2 class="section-header">Alunos</h2>
    <div class="oferta-grid">
        <?php while ($aluno = $alunos->fetch_assoc()) { ?>
            <div class="oferta-card">
                <div class="profile-pic-container">
                    <img src="<?= !empty($aluno['foto']) ? '../images/' . $aluno['foto'] : '../images/student_default.png' ?>" alt="Foto do Aluno">
                </div>
                <h3><?= htmlspecialchars($aluno['Nome']) ?></h3>
                <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <?= htmlspecialchars($aluno['Email']) ?></p>
                <p><i class="fas fa-id-badge"></i> <strong>Nº Processo:</strong> <?= htmlspecialchars($aluno['nr_processo']) ?></p>
                <p><i class="fas fa-users"></i> <strong>Turma:</strong> <?= htmlspecialchars($aluno['Turma']) ?></p>
                <p><strong><i class="fas fa-file-pdf"></i> Currículo:</strong>
                    <?php if (!empty($aluno['Curriculo'])): ?>
                        <a href="../uploads/<?= htmlspecialchars($aluno['Curriculo']) ?>" download target="_blank" title="Transferir Currículo">
                            <i class="fas fa-file-download" style="color: #4f8cff; font-size: 1.2rem; margin-left: 8px;"></i>
                        </a>
                    <?php else: ?>
                        <span class="no-cv">Nenhum currículo enviado</span>
                    <?php endif; ?>
                </p>
                <div class="oferta-actions">
                    <a href='admin_dashboard.php?page=editar_aluno&id=<?= $aluno['id_aluno'] ?>' class="btn-editar"><i class="fas fa-pen-to-square"></i> Editar</a>
                    <a href='pages/excluir_aluno.php?id=<?= $aluno['id_aluno'] ?>' class="btn-excluir"><i class="fas fa-trash"></i> Excluir</a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

    <!-- Empresas -->
<div id="empresas" class="section">
    <h2 class="section-header">Empresas</h2>
    <div class="oferta-grid">
        <?php while ($empresa = $empresas->fetch_assoc()) { ?>
            <div class="oferta-card">
                <div class="profile-pic-container" style="text-align: center; margin-bottom: 1rem;">
                    <img src="<?= !empty($empresa['foto']) ? '../images/' . $empresa['foto'] : '../images/company_default.png' ?>" alt="Foto da Empresa" style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%; border: 2px solid #fff;">
                </div>

                <h3><?= htmlspecialchars($empresa['nome_empresa']) ?></h3>
                <p><i class="fas fa-user-tie"></i> <strong>Responsável:</strong> <?= htmlspecialchars($empresa['responsavel']) ?></p>
                <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <?= htmlspecialchars($empresa['email']) ?></p>
                <p><i class="fas fa-phone"></i> <strong>Telefone:</strong> <?= htmlspecialchars($empresa['telefone']) ?></p>
                <p><i class="fas fa-map-marked-alt"></i> <strong>Morada:</strong> <?= htmlspecialchars($empresa['morada']) ?></p>
                <p><i class="fas fa-mail-bulk"></i> <strong>Código Postal:</strong> <?= htmlspecialchars($empresa['cod_postal']) ?></p>
                <p><i class="fas fa-map-marker-alt"></i> <strong>Localidade:</strong> <?= htmlspecialchars($empresa['Localidade']) ?></p>
                <p><i class="fas fa-graduation-cap"></i> <strong>Curso:</strong> 
    <?= ($empresa['curso_nome'] ?? '') === 'Técnico(a) de Gestão e Programação de Sistemas Informáticos' ? 'TGPSI' : htmlspecialchars($empresa['curso_nome'] ?? 'Não associado') ?>
</p>

                <div class="oferta-actions">
                    <a href='admin_dashboard.php?page=editar_empresa&id=<?= $empresa['id_empresas'] ?>' class="btn-editar">
                        <i class="fas fa-pen-to-square"></i> Editar
                    </a>
                    <a href='pages/excluir_empresa.php?id=<?= $empresa['id_empresas'] ?>' class="btn-excluir">
                        <i class="fas fa-trash"></i> Excluir
                    </a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

    <!-- Professores -->
<div id="professores" class="section">
    <h2 class="section-header">Professores</h2>
    <div class="oferta-grid">
        <?php while ($professor = $professores->fetch_assoc()) { ?>
            <div class="oferta-card">
                <div class="profile-pic-container" style="text-align: center; margin-bottom: 1rem;">
                    <img src="<?= !empty($professor['foto']) ? '../images/' . $professor['foto'] : '../images/professor_default.png' ?>" alt="Foto do Professor" style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%; border: 2px solid #fff;">
                </div>

                <h3><?= htmlspecialchars($professor['nome']) ?></h3>
                <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <?= htmlspecialchars($professor['email']) ?></p>
                <p><i class="fas fa-graduation-cap"></i> <strong>Curso:</strong>
                    <?= $professor['curso_nome'] === 'Técnico(a) de Gestão e Programação de Sistemas Informáticos' ? 'TGPSI' : htmlspecialchars($professor['curso_nome']) ?>
                </p>

                <div class="oferta-actions">
                    <a href='admin_dashboard.php?page=editar_professor&id=<?= $professor['id_professor'] ?>' class="btn-editar">
                        <i class="fas fa-pen-to-square"></i> Editar
                    </a>
                    <a href='pages/excluir_professor.php?id=<?= $professor['id_professor'] ?>' class="btn-excluir">
                        <i class="fas fa-trash"></i> Excluir
                    </a>
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

    document.addEventListener("DOMContentLoaded", function () {
        let toast = document.getElementById("toast");
        if (toast) {
            setTimeout(function () {
                toast.style.display = "none";
            }, 4000);
        }
    });
</script>
</body>
</html>