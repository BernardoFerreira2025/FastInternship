<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../database/mysqli.php';

// Verifica se o professor está autenticado
if (!isset($_SESSION['id_professor']) || $_SESSION['user_role'] !== 'professor') {
    header("Location: ../formlogin.php");
    exit();
}

$id_curso_professor = $_SESSION['id_curso'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_empresas'];
    $nome = $_POST['nome_empresa'];
    $email = $_POST['email'];
    $responsavel = $_POST['responsavel'];
    $telefone = $_POST['telefone'];
    $morada = $_POST['morada'];
    $cod_postal = $_POST['cod_postal'];
    $Localidade = $_POST['Localidade'];

    // Confirma se a empresa pertence ao curso do professor antes de atualizar
    $verifica = $conn->prepare("SELECT id_curso FROM empresas WHERE id_empresas = ?");
    $verifica->bind_param("i", $id);
    $verifica->execute();
    $res = $verifica->get_result();
    $dados = $res->fetch_assoc();

    if (!$dados || $dados['id_curso'] != $id_curso_professor) {
        echo "<p style='color: red;'>Não tens permissão para editar esta empresa.</p>";
        exit();
    }

    $stmt = $conn->prepare("UPDATE empresas SET responsavel = ?, nome_empresa = ?, email = ?, telefone = ?, morada = ?, cod_postal = ?, Localidade = ? WHERE id_empresas = ?");
    $stmt->bind_param("sssssssi", $responsavel, $nome, $email, $telefone, $morada, $cod_postal, $Localidade, $id);

    if ($stmt->execute()) {
        $_SESSION['toast_message'] = "Alterações guardadas com sucesso!";
        header("Location: professor_dashboard.php?page=gestao_empresas");
        exit();
    } else {
        echo "Erro ao atualizar os dados.";
    }
} else {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // Buscar empresa e confirmar que pertence ao curso do professor
        $stmt = $conn->prepare("SELECT * FROM empresas WHERE id_empresas = ? AND id_curso = ?");
        $stmt->bind_param("ii", $id, $id_curso_professor);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $empresa = $result->fetch_assoc();
        } else {
            echo "<p style='color: red;'>Empresa não encontrada ou não autorizada.</p>";
            exit();
        }
    } else {
        echo "<p style='color: red;'>ID da empresa não especificado.</p>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Editar Empresa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/allcss.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="form-box-editar">
    <h1>Editar Empresa</h1>
    <form method="POST" action="" id="editarEmpresaForm">
        <input type="hidden" name="id_empresas" value="<?= htmlspecialchars($empresa['id_empresas']) ?>">

        <div class="input-group-editar">
            <label for="responsavel">Responsável:</label>
            <input type="text" id="responsavel" name="responsavel" value="<?= htmlspecialchars($empresa['responsavel']) ?>" required>
        </div>

        <div class="input-group-editar">
            <label for="nome_empresa">Nome da Empresa:</label>
            <input type="text" id="nome_empresa" name="nome_empresa" value="<?= htmlspecialchars($empresa['nome_empresa']) ?>" required>
        </div>

        <div class="input-group-editar">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($empresa['email']) ?>" required>
        </div>

        <div class="input-group-editar">
            <label for="telefone">Telefone:</label>
            <input type="text" id="telefone" name="telefone" value="<?= htmlspecialchars($empresa['telefone']) ?>" required>
        </div>

        <div class="input-group-editar">
            <label for="morada">Morada:</label>
            <input type="text" id="morada" name="morada" value="<?= htmlspecialchars($empresa['morada']) ?>" required>
        </div>

        <div class="input-group-editar">
            <label for="cod_postal">Código Postal:</label>
            <input type="text" id="cod_postal" name="cod_postal" value="<?= htmlspecialchars($empresa['cod_postal']) ?>" required>
        </div>

        <div class="input-group-editar">
            <label for="Localidade">Localidade:</label>
            <input type="text" id="Localidade" name="Localidade" value="<?= htmlspecialchars($empresa['Localidade']) ?>" required>
        </div>

        <button type="submit" class="btn-editar-submit">Guardar Alterações</button>
    </form>
</div>

</body>
</html>
