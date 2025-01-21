<form action="geriroferta_handler.php" method="POST" class="form-container" id="ofertaForm">
    <h1>Inserir Nova Oferta</h1>
    <div class="form-group">
        <label for="titulo">Título da Oferta:</label>
        <input type="text" id="titulo" name="titulo" placeholder="Ex: Nome da Empresa" required>
    </div>
    <div class="form-group">
        <label for="descricao">Descrição:</label>
        <textarea id="descricao" name="descricao" placeholder="Descreva os detalhes da oferta..." required></textarea>
    </div>
    <div class="form-group">
        <label for="requisitos">Requisitos:</label>
        <textarea id="requisitos" name="requisitos" placeholder="Liste os requisitos necessários..." required></textarea>
    </div>
    <div class="form-group">
        <label for="id_empresa">Empresa:</label>
        <select id="id_empresa" name="id_empresa" required>
            <option value="">Selecione uma empresa</option>
            <?php
            include '../database/mysqli.php';
            $result = $conn->query("SELECT id_empresas, nome_empresa FROM empresas");
            while ($empresa = $result->fetch_assoc()) {
                echo "<option value='{$empresa['id_empresas']}'>{$empresa['nome_empresa']}</option>";
            }
            ?>
        </select>
    </div>
    <div class="form-group">
        <label for="vagas">Número de Vagas:</label>
        <input type="number" id="vagas" name="vagas" min="1" placeholder="Ex: 5" required>
    </div>
    <div class="form-group">
        <label for="curso_relacionado">Curso Relacionado:</label>
        <select id="curso_relacionado" name="curso_relacionado" required>
            <option value="">Selecione um curso</option>
            <option value="Técnico(a) de Multimédia">Técnico(a) de Multimédia</option>
            <option value="Técnico(a) de Turismo">Técnico(a) de Turismo</option>
            <option value="Técnico(a) de Gestão e Programação de Sistemas Informáticos">
                Técnico(a) de Gestão e Programação de Sistemas Informáticos
            </option>
        </select>
    </div>
    <div class="form-group">
        <label for="data_inicio">Data de Início:</label>
        <input type="date" id="data_inicio" name="data_inicio" required>
    </div>
    <div class="form-group">
        <label for="data_fim">Data de Fim:</label>
        <input type="date" id="data_fim" name="data_fim" required>
    </div>
    <button type="submit" class="btn-submit">Salvar Oferta</button>
</form>

<script>
    document.getElementById('ofertaForm').addEventListener('submit', function (event) {
        const dataInicio = new Date(document.getElementById('data_inicio').value);
        const dataFim = new Date(document.getElementById('data_fim').value);
        const titulo = document.getElementById('titulo').value.trim();
        const descricao = document.getElementById('descricao').value.trim();
        const requisitos = document.getElementById('requisitos').value.trim();
        const idEmpresa = document.getElementById('id_empresa').value.trim();
        const vagas = document.getElementById('vagas').value.trim();
        const cursoRelacionado = document.getElementById('curso_relacionado').value.trim();

        if (!titulo || !descricao || !requisitos || !idEmpresa || !vagas || !cursoRelacionado) {
            showToast('Por favor, preencha todos os campos.', 'error');
            event.preventDefault();
            return;
        }

        if (dataFim < dataInicio) {
            showToast('Tente Novamente, verifique as datas.', 'error');
            event.preventDefault();
            return;
        }
    });

    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `toast-${type}`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    }
</script>
