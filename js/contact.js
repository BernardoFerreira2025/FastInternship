document.addEventListener('DOMContentLoaded', () => {
    // Partículas de fundo
    const createParticles = () => {
        const container = document.querySelector('.contact-particles');
        const particleCount = 50;

        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            
            const size = Math.random() * 5 + 1;
            particle.style.width = `${size}px`;
            particle.style.height = `${size}px`;
            
            particle.style.left = `${Math.random() * 100}%`;
            particle.style.top = `${Math.random() * 100}%`;
            
            container.appendChild(particle);
            animateParticle(particle);
        }
    };

    const animateParticle = (particle) => {
        const animation = particle.animate([
            { transform: 'translate(0, 0)', opacity: 0 },
            { transform: `translate(${Math.random() * 200 - 100}px, ${Math.random() * 200 - 100}px)`, opacity: 1 },
            { transform: `translate(${Math.random() * 200 - 100}px, ${Math.random() * 200 - 100}px)`, opacity: 0 }
        ], {
            duration: Math.random() * 3000 + 2000,
            iterations: Infinity
        });
    };

    // Validação e animação do formulário
    const form = document.querySelector('.contact-form');
    const inputs = document.querySelectorAll('.form-input');

    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', () => {
            if (!input.value) {
                input.parentElement.classList.remove('focused');
            }
        });
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = form.querySelector('.submit-btn');
        const originalText = submitBtn.textContent;

        // Animação do botão
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading-spinner"></span>';

        try {
            // Simular envio do formulário
            await new Promise(resolve => setTimeout(resolve, 2000));
            
            // Animação de sucesso
            submitBtn.innerHTML = '✓ Sent Successfully';
            submitBtn.classList.add('success');

            // Resetar formulário
            setTimeout(() => {
                form.reset();
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                submitBtn.classList.remove('success');
            }, 3000);

        } catch (error) {
            submitBtn.innerHTML = '× Error';
            submitBtn.classList.add('error');

            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                submitBtn.classList.remove('error');
            }, 3000);
        }
    });

    // Efeito de hover nos cards de informação
    const infoItems = document.querySelectorAll('.info-item');
    
    infoItems.forEach(item => {
        item.addEventListener('mouseenter', () => {
            item.style.transform = 'translateX(10px)';
        });

        item.addEventListener('mouseleave', () => {
            item.style.transform = 'translateX(0)';
        });
    });

    // Inicializar partículas
    createParticles();
});
