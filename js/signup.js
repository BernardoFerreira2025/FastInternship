document.addEventListener('DOMContentLoaded', () => {
    const signupForm = document.getElementById('signupForm');

    // Função para mostrar mensagens de erro ou sucesso
    const showMessage = (message, type = 'error') => {
        const messageDiv = document.createElement('div');
        messageDiv.className = `alert alert-${type}`;
        messageDiv.textContent = message;
        document.querySelector('.signup-card').prepend(messageDiv);

        setTimeout(() => {
            messageDiv.remove();
        }, 3000);
    };

    // Validar formulário no lado do cliente
    const validateForm = () => {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        const resume = document.getElementById('resume').files[0];

        if (password !== confirmPassword) {
            showMessage('As senhas não correspondem.', 'error');
            return false;
        }

        if (resume && !['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'].includes(resume.type)) {
            showMessage('Por favor, envie um currículo no formato PDF ou Word.', 'error');
            return false;
        }

        return true;
    };

    // Animação de carregamento durante o envio
    const showLoading = (isLoading) => {
        const button = document.querySelector('.signup-btn');
        if (isLoading) {
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> A enviar...';
        } else {
            button.disabled = false;
            button.textContent = 'Registar';
        }
    };

    // Enviar formulário
    signupForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Validar formulário antes do envio
        if (!validateForm()) return;

        const formData = new FormData(signupForm);
        showLoading(true);

        try {
            const response = await fetch('signup.php', {
                method: 'POST',
                body: formData,
            });

            const data = await response.json();
            showLoading(false);

            if (data.success) {
                showMessage('Conta criada com sucesso!', 'success');
                setTimeout(() => {
                    window.location.href = 'dashboard.html';
                }, 2000);
            } else {
                showMessage(data.message || 'Erro ao criar conta.', 'error');
            }
        } catch (error) {
            showLoading(false);
            showMessage('Ocorreu um erro. Por favor, tente novamente.', 'error');
        }
    });
});
