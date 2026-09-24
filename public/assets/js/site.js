(() => {
    const root = document.documentElement;
    const themeButton = document.getElementById('theme-toggle');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    try {
        const savedTheme = localStorage.getItem('portfolio-theme');
        if (savedTheme === 'light' || (!savedTheme && window.matchMedia('(prefers-color-scheme: light)').matches)) {
            root.classList.remove('dark');
        }
    } catch (_) {
        // Keep the default theme if browser storage is unavailable.
    }

    if (themeButton) {
        themeButton.addEventListener('click', () => {
            const applyTheme = () => {
                root.classList.toggle('dark');
                try {
                    localStorage.setItem('portfolio-theme', root.classList.contains('dark') ? 'dark' : 'light');
                } catch (_) {
                    // The selected theme still applies for this page view.
                }
            };

            if (!reducedMotion && typeof document.startViewTransition === 'function') {
                document.startViewTransition(applyTheme);
            } else {
                applyTheme();
            }
        });
    }

    const revealItems = document.querySelectorAll('.hero-copy, .hero-art, .content-section .section-wrap');
    if (!reducedMotion && 'IntersectionObserver' in window) {
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        revealItems.forEach((item) => {
            item.classList.add('reveal-item');
            revealObserver.observe(item);
        });
    }

    const card = document.querySelector('[data-tilt]');
    if (card && !reducedMotion) {
        card.addEventListener('pointermove', (event) => {
            if (event.pointerType === 'touch') return;
            const bounds = card.getBoundingClientRect();
            const x = Math.max(-1, Math.min(1, (event.clientX - bounds.left) / bounds.width * 2 - 1));
            const y = Math.max(-1, Math.min(1, (event.clientY - bounds.top) / bounds.height * 2 - 1));
            card.style.setProperty('--pointer-rotate-x', `${(-y * 4.5).toFixed(2)}deg`);
            card.style.setProperty('--pointer-rotate-y', `${(x * 6).toFixed(2)}deg`);
            card.style.setProperty('--pointer-x', `${((x + 1) * 50).toFixed(1)}%`);
            card.style.setProperty('--pointer-y', `${((y + 1) * 50).toFixed(1)}%`);
        });

        card.addEventListener('pointerleave', () => {
            card.style.setProperty('--pointer-rotate-x', '0deg');
            card.style.setProperty('--pointer-rotate-y', '0deg');
            card.style.setProperty('--pointer-x', '50%');
            card.style.setProperty('--pointer-y', '0%');
        });

        let scrollFrame = 0;
        const updateScrollMotion = () => {
            scrollFrame = 0;
            const bounds = card.getBoundingClientRect();
            const offset = (window.innerHeight * 0.52 - (bounds.top + bounds.height / 2)) / Math.max(window.innerHeight, 1);
            const progress = Math.max(-1, Math.min(1, offset));
            card.style.setProperty('--scroll-tilt', `${(progress * 1.4).toFixed(2)}deg`);
            card.style.setProperty('--scroll-lift', `${(-Math.abs(progress) * 5).toFixed(1)}px`);
        };

        window.addEventListener('scroll', () => {
            if (!scrollFrame) scrollFrame = window.requestAnimationFrame(updateScrollMotion);
        }, { passive: true });
        updateScrollMotion();
    }
})();
