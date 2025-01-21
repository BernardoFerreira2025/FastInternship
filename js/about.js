document.addEventListener('DOMContentLoaded', () => {
    // Selecionar elementos com a classe fade-in
    const fadeInElements = document.querySelectorAll('.fade-in');

    // Configurar o IntersectionObserver para animar os elementos
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible'); // Adiciona uma classe para iniciar a animação
                observer.unobserve(entry.target); // Para observar após a animação iniciar
            }
        });
    }, { threshold: 0.3 }); // Inicia a animação quando 30% do elemento está visível

    // Observar cada elemento com a classe fade-in
    fadeInElements.forEach((el) => {
        observer.observe(el);
    });
});
