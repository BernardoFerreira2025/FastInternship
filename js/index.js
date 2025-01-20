// index.js
document.addEventListener('DOMContentLoaded', () => {
    // Preloader
    const preloader = document.querySelector('.preloader');
    window.addEventListener('load', () => {
        preloader.style.opacity = '0';
        setTimeout(() => {
            preloader.style.display = 'none';
        }, 500);
    });

    // Cursor Personalizado
    const cursor = document.querySelector('.cursor-dot');
    const cursorOutline = document.querySelector('.cursor-outline');

    document.addEventListener('mousemove', (e) => {
        const posX = e.clientX;
        const posY = e.clientY;

        cursor.style.transform = `translate(${posX}px, ${posY}px)`;
        cursorOutline.style.transform = `translate(${posX - 15}px, ${posY - 15}px)`;
    });

    // Efeito Hover no Cursor
    document.querySelectorAll('a, button').forEach(element => {
        element.addEventListener('mouseenter', () => {
            cursor.style.transform = 'scale(1.5)';
            cursorOutline.style.transform = 'scale(1.5)';
        });

        element.addEventListener('mouseleave', () => {
            cursor.style.transform = 'scale(1)';
            cursorOutline.style.transform = 'scale(1)';
        });
    });

    // Parallax nos elementos flutuantes
    const handleParallax = () => {
        const elements = document.querySelectorAll('.float-element');
        elements.forEach(element => {
            const speed = element.getAttribute('data-speed');
            const x = (window.innerWidth - event.pageX * speed) / 100;
            const y = (window.innerHeight - event.pageY * speed) / 100;
            element.style.transform = `translateX(${x}px) translateY(${y}px)`;
        });
    };

    document.addEventListener('mousemove', handleParallax);

    // Animação dos Contadores
    const startCounterAnimation = (element) => {
        const target = parseInt(element.getAttribute('data-target'));
        const duration = 2000; // 2 segundos
        const step = target / duration * 10;
        let current = 0;

        const counter = setInterval(() => {
            current += step;
            if (current >= target) {
                element.textContent = target.toLocaleString() + '+';
                clearInterval(counter);
            } else {
                element.textContent = Math.floor(current).toLocaleString() + '+';
            }
        }, 10);
    };

    // Observer para animações baseadas em scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                if (entry.target.classList.contains('counter')) {
                    startCounterAnimation(entry.target);
                }
                entry.target.classList.add('animate');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observar elementos para animação
    document.querySelectorAll('.animate-on-scroll, .counter').forEach(element => {
        observer.observe(element);
    });

    // Efeito 3D nos cards
    VanillaTilt.init(document.querySelectorAll("[data-tilt]"), {
        max: 15,
        speed: 400,
        glare: true,
        "max-glare": 0.2
    });

    // Slider de Empresas Parceiras
    const sliderTrack = document.querySelector('.slider-track');
    if (sliderTrack) {
        const cloneSlides = () => {
            const slides = sliderTrack.children;
            for (let i = 0; i < slides.length; i++) {
                const clone = slides[i].cloneNode(true);
                sliderTrack.appendChild(clone);
            }
        };
        cloneSlides();
    }

    // Botão Voltar ao Topo
    const backToTop = document.querySelector('.back-to-top');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 500) {
            backToTop.style.opacity = '1';
            backToTop.style.pointerEvents = 'all';
        } else {
            backToTop.style.opacity = '0';
            backToTop.style.pointerEvents = 'none';
        }
    });

    backToTop.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // Animação de Partículas
    const createParticles = () => {
        const particles = document.querySelector('.hero-particles');
        const particleCount = 50;

        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            
            const size = Math.random() * 5 + 1;
            particle.style.width = `${size}px`;
            particle.style.height = `${size}px`;
            
            particle.style.left = `${Math.random() * 100}%`;
            particle.style.top = `${Math.random() * 100}%`;
            
            particles.appendChild(particle);
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

    createParticles();
});
