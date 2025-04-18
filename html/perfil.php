<?php
session_start();
include '../database/mysqli.php';

define('MAX_FILE_SIZE', 2097152); // 2MB em bytes
define('ALLOWED_MIME_TYPE', 'application/pdf');

if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['aluno', 'empresa'])) {
    header("Location: formlogin.php");
    exit();
}

$user_role = $_SESSION['user_role'];
$user_id = $user_role === 'aluno' ? $_SESSION['id_aluno'] : $_SESSION['id_empresas'];
$tabela = $user_role === 'aluno' ? 'alunos' : 'empresas';
$id_coluna = $user_role === 'aluno' ? 'id_aluno' : 'id_empresas';

$erro = $sucesso = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($user_role === 'aluno') {
        $nome = $_POST['nome'] ?? '';
        $turma = $_POST['turma'] ?? '';
        $data_nascimento = $_POST['data_nascimento'] ?? '';
        $curriculo = $_FILES['curriculo']['name'] ? 'curriculos/' . basename($_FILES['curriculo']['name']) : null;

        if ($curriculo) {
            move_uploaded_file($_FILES['curriculo']['tmp_name'], '../' . $curriculo);
        }

        $query = "UPDATE alunos SET Nome = ?, Turma = ?, Data_Nascimento = ?";
        $params = [$nome, $turma, $data_nascimento];
        $types = "sss";

        if ($curriculo) {
            $query .= ", Curriculo = ?";
            $params[] = $curriculo;
            $types .= "s";
        }

        $query .= " WHERE id_aluno = ?";
        $params[] = $user_id;
        $types .= "i";

        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
    } else {
        $nome_empresa = $_POST['nome_empresa'] ?? '';
        $responsavel = $_POST['responsavel'] ?? '';
        $telefone = $_POST['telefone'] ?? '';
        $morada = $_POST['morada'] ?? '';
        $cod_postal = $_POST['cod_postal'] ?? '';
        $localidade = $_POST['localidade'] ?? '';

        $stmt = $conn->prepare("UPDATE empresas SET nome_empresa=?, responsavel=?, telefone=?, morada=?, cod_postal=?, localidade=? WHERE id_empresas=?");
        $stmt->bind_param("ssssssi", $nome_empresa, $responsavel, $telefone, $morada, $cod_postal, $localidade, $user_id);
    }

    if ($stmt->execute()) {
        $sucesso = "Dados atualizados com sucesso!";
    } else {
        $erro = "Erro ao atualizar os dados.";
    }

    $stmt->close();
}

// Obter dados do utilizador
$stmt = $conn->prepare("SELECT * FROM $tabela WHERE $id_coluna = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$dados = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Perfil</title>
    <link rel="stylesheet" href="assets/css/allcss.css">
    <link rel="stylesheet" href="assets/elements/header.css">
    <link rel="stylesheet" href="assets/elements/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include 'assets/elements/header.php'; ?>

<div class="perfil-form-container">
    <h1>Editar Perfil</h1>

    <?php if ($erro): ?>
        <div class="popup error-popup"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>
    <?php if ($sucesso): ?>
        <div class="popup success-popup"><?= htmlspecialchars($sucesso) ?></div>
    <?php endif; ?>

    <?php if ($dados): ?>
        <form method="POST" enctype="multipart/form-data" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
            <?php if ($user_role === 'aluno'): ?>
                <div class="perfil-field-group">
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($dados['Nome'] ?? '') ?>" required>
                </div>
                <div class="perfil-field-group">
                    <label for="turma">Turma</label>
                    <input type="text" id="turma" name="turma" value="<?= htmlspecialchars($dados['Turma'] ?? '') ?>" required>
                </div>
                <div class="perfil-field-group">
                    <label for="data_nascimento">Data de Nascimento</label>
                    <input type="date" id="data_nascimento" name="data_nascimento" value="<?= htmlspecialchars($dados['Data_Nascimento'] ?? '') ?>" required>
                </div>
                <div class="perfil-field-group">
                    <label for="curriculo">Currículo (PDF, Max <?= MAX_FILE_SIZE / 1024 / 1024 ?>MB)</label>
                    <input type="file" id="curriculo" name="curriculo" accept="<?= htmlspecialchars(ALLOWED_MIME_TYPE) ?>">
                    <?php if (!empty($dados['Curriculo'])): ?>
                        <a href="../uploads/<?= htmlspecialchars($dados['Curriculo']) ?>" target="_blank" class="perfil-cv-link">
                            <i class="fas fa-download"></i> Transferir Currículo Atual
                        </a>
                    <?php endif; ?>
                </div>
            <?php elseif ($user_role === 'empresa'): ?>
                <div class="perfil-field-group">
                    <label for="nome_empresa">Nome da Empresa</label>
                    <input type="text" id="nome_empresa" name="nome_empresa" value="<?= htmlspecialchars($dados['nome_empresa'] ?? '') ?>" required>
                </div>
                <div class="perfil-field-group">
                    <label for="responsavel">Responsável</label>
                    <input type="text" id="responsavel" name="responsavel" value="<?= htmlspecialchars($dados['responsavel'] ?? '') ?>" required>
                </div>
                <div class="perfil-field-group">
                    <label for="telefone">Telefone</label>
                    <input type="text" id="telefone" name="telefone" value="<?= htmlspecialchars($dados['telefone'] ?? '') ?>">
                </div>
                <div class="perfil-field-group">
                    <label for="morada">Morada</label>
                    <input type="text" id="morada" name="morada" value="<?= htmlspecialchars($dados['morada'] ?? '') ?>">
                </div>
                <div class="perfil-field-group">
                    <label for="cod_postal">Código Postal</label>
                    <input type="text" id="cod_postal" name="cod_postal" value="<?= htmlspecialchars($dados['cod_postal'] ?? '') ?>">
                </div>
                <div class="perfil-field-group">
                    <label for="localidade">Localidade</label>
                    <input type="text" id="localidade" name="localidade" value="<?= htmlspecialchars($dados['localidade'] ?? '') ?>">
                </div>
            <?php endif; ?>

            <button type="submit" class="btn-submit">Guardar Alterações</button>
            <a href="<?= $user_role === 'aluno' ? 'aluno_dashboard.php' : 'empresa_dashboard.php' ?>" class="btn-voltar-dashboard">← Voltar ao Painel de Controlo</a>
        </form>
    <?php else: ?>
        <p>Não foi possível carregar os dados para edição. Por favor, tente novamente mais tarde ou contacte o suporte.</p>
    <?php endif; ?>
</div>

<?php include 'assets/elements/footer.php'; ?>
</body>
</html>