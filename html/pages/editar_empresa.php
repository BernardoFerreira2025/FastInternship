<?php
session_start(); // Inicia a sessão para mensagens
include '../../database/mysqli.php'; // Inclui o arquivo de conexão

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id_empresas'];
    $nome = $_POST['nome_empresa'];
    $email = $_POST['email'];
    $responsavel = $_POST['responsavel'];
    $telefone = $_POST['telefone'];
    $morada = $_POST['morada'];
    $cod_postal = $_POST['cod_postal'];
    $Localidade = $_POST['Localidade'];

    // Atualizar dados no banco de dados
    $stmt = $conn->prepare("UPDATE empresas SET responsavel = ?, nome_empresa = ?, email = ?,  telefone = ? WHERE id_empresas = ?");
    $stmt->bind_param("ssssi", $responsavel, $nome, $email, $telefone, $id);

    if ($stmt->execute()) {
        // Define mensagem de sucesso na sessão
        $_SESSION['mensagem_sucesso'] = 'Alterações guardadas com sucesso!';
        // Redireciona para gestao_utilizadores.php
        header('Location: ../admin_dashboard.php?page=gestao_utilizadores');
        exit();
    } else {
        echo "Erro ao atualizar os dados.";
    }
    } else {
    // Verificar se o ID foi passado
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // Buscar os dados da empresa
        $result = $conn->query("SELECT * FROM empresas WHERE id_empresas = $id");
        if ($result->num_rows > 0) {
            $empresa = $result->fetch_assoc();
        } else {
            echo "Empresa não encontrada.";
            exit();
        }
    } else {
        echo "ID da empresa não especificado.";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Empresa</title>
    <link rel="stylesheet" href="../assets/css/allcss.css"> <!-- Caminho para o CSS -->
</head>
<body>
    <div class="form-container">
        <h1>Editar Empresa</h1>
        <form action="editar_empresa.php" method="POST">
            <input type="hidden" name="id_empresas" value="<?php echo htmlspecialchars($empresa['id_empresas']); ?>">
            <div>
                <label for="responsavel">Responsável:</label>
                <input type="responsavel" id="responsavel" name="responsavel" value="<?php echo htmlspecialchars($empresa['responsavel']); ?>" required>
            </div>
            <div>
                <label for="nome_empresa">Nome da Empresa:</label>
                <input type="text" id="nome_empresa" name="nome_empresa" value="<?php echo htmlspecialchars($empresa['nome_empresa']); ?>" required>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($empresa['email']); ?>" required>
            </div>
            <div>
                <label for="telefone">Telefone:</label>
                <input type="telefone" id="telefone" name="telefone" value="<?php echo htmlspecialchars($empresa['telefone']); ?>" required>
            </div>
            <div>
                <label for="morada">Morada:</label>
                <input type="morada" id="morada" name="morada" value="<?php echo htmlspecialchars($empresa['morada']); ?>" required>
            </div>
            <div>
                <label for="cod_postal">Código Postal:</label>
                <input type="cod_postal" id="cod_postal" name="cod_postal" value="<?php echo htmlspecialchars($empresa['cod_postal']); ?>" required>
            </div>
            <div>
                <label for="Localidade">Localidade:</label>
                <input type="Localidade" id="Localidade" name="Localidade" value="<?php echo htmlspecialchars($empresa['Localidade']); ?>" required>
            </div>
            <button type="submit">Guardar Alterações</button>
        </form>
    </div>
</body>
</html>
