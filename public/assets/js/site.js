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

    const syncThemeButton = () => {
        if (!themeButton) return;
        const darkTheme = root.classList.contains('dark');
        themeButton.setAttribute('aria-pressed', darkTheme ? 'true' : 'false');
        themeButton.setAttribute('aria-label', darkTheme ? 'Đang dùng giao diện tối; chuyển sang sáng' : 'Đang dùng giao diện sáng; chuyển sang tối');
        themeButton.title = darkTheme ? 'Chuyển sang giao diện sáng' : 'Chuyển sang giao diện tối';
    };

    syncThemeButton();

    const navigationToggle = document.querySelector('[data-nav-toggle]');
    const navigationDrawer = document.getElementById('primary-navigation');
    const navigationBackdrop = document.querySelector('[data-nav-close]');
    const mobileNavigation = window.matchMedia('(max-width: 900px)');
    let previousBodyOverflow = '';

    const setNavigationOpen = (open, returnFocus = false) => {
        if (!navigationToggle || !navigationDrawer || !mobileNavigation.matches) return;

        document.body.classList.toggle('nav-open', open);
        navigationToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        navigationToggle.setAttribute('aria-label', open ? 'Đóng menu điều hướng' : 'Mở menu điều hướng');
        navigationDrawer.setAttribute('aria-hidden', open ? 'false' : 'true');
        navigationDrawer.inert = !open;

        if (navigationBackdrop) {
            navigationBackdrop.setAttribute('aria-hidden', open ? 'false' : 'true');
            navigationBackdrop.tabIndex = open ? 0 : -1;
        }

        if (open) {
            previousBodyOverflow = document.body.style.overflow;
            document.body.style.overflow = 'hidden';
            window.setTimeout(() => navigationDrawer.querySelector('a')?.focus(), 260);
        } else {
            document.body.style.overflow = previousBodyOverflow;
            if (returnFocus) navigationToggle.focus();
        }
    };

    const syncNavigationViewport = () => {
        if (!navigationToggle || !navigationDrawer) return;
        if (mobileNavigation.matches) {
            navigationToggle.setAttribute('aria-expanded', 'false');
            navigationToggle.setAttribute('aria-label', 'Mở menu điều hướng');
            navigationDrawer.setAttribute('aria-hidden', 'true');
            navigationDrawer.inert = true;
            if (navigationBackdrop) navigationBackdrop.setAttribute('aria-hidden', 'true');
        } else {
            document.body.classList.remove('nav-open');
            document.body.style.overflow = previousBodyOverflow;
            navigationToggle.setAttribute('aria-expanded', 'false');
            navigationDrawer.setAttribute('aria-hidden', 'false');
            navigationDrawer.inert = false;
            if (navigationBackdrop) navigationBackdrop.setAttribute('aria-hidden', 'true');
        }
    };

    syncNavigationViewport();
    mobileNavigation.addEventListener('change', syncNavigationViewport);
    navigationToggle?.addEventListener('click', () => {
        setNavigationOpen(navigationToggle.getAttribute('aria-expanded') !== 'true');
    });
    navigationBackdrop?.addEventListener('click', () => setNavigationOpen(false, true));
    navigationDrawer?.addEventListener('click', (event) => {
        if (event.target.closest('a')) setNavigationOpen(false);
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && navigationToggle?.getAttribute('aria-expanded') === 'true') {
            setNavigationOpen(false, true);
            return;
        }

        if (event.key === 'Tab' && navigationToggle?.getAttribute('aria-expanded') === 'true') {
            const drawerLinks = [...navigationDrawer.querySelectorAll('a')];
            const firstLink = drawerLinks[0];
            const lastLink = drawerLinks[drawerLinks.length - 1];
            if (event.shiftKey && (document.activeElement === firstLink || document.activeElement === navigationBackdrop)) {
                event.preventDefault();
                navigationToggle.focus();
            } else if (!event.shiftKey && document.activeElement === navigationToggle) {
                event.preventDefault();
                firstLink?.focus();
            } else if (event.shiftKey && document.activeElement === navigationToggle) {
                event.preventDefault();
                lastLink?.focus();
            } else if (!event.shiftKey && document.activeElement === lastLink) {
                event.preventDefault();
                navigationToggle.focus();
            }
        }
    });

    window.addEventListener('storage', (event) => {
        if (event.key !== 'portfolio-theme') return;
        if (event.newValue === 'light') {
            root.classList.remove('dark');
        } else if (event.newValue === 'dark') {
            root.classList.add('dark');
        } else {
            root.classList.toggle('dark', !window.matchMedia('(prefers-color-scheme: light)').matches);
        }
        syncThemeButton();
    });

    if (themeButton) {
        themeButton.addEventListener('click', () => {
            const applyTheme = () => {
                root.classList.toggle('dark');
                syncThemeButton();
                try {
                    localStorage.setItem('portfolio-theme', root.classList.contains('dark') ? 'dark' : 'light');
                } catch (_) {
                    // The selected theme still applies for this page view.
                }
            };

            if (!reducedMotion && typeof document.startViewTransition === 'function') {
                const bounds = themeButton.getBoundingClientRect();
                const originX = bounds.left + bounds.width / 2;
                const originY = bounds.top + bounds.height / 2;
                const farthestX = Math.max(originX, window.innerWidth - originX);
                const farthestY = Math.max(originY, window.innerHeight - originY);
                const radius = Math.ceil(Math.hypot(farthestX, farthestY));
                root.style.setProperty('--theme-origin-x', `${originX}px`);
                root.style.setProperty('--theme-origin-y', `${originY}px`);
                root.style.setProperty('--theme-reveal-radius', `${radius}px`);
                const transition = document.startViewTransition(applyTheme);
                transition.finished.then(() => {
                    root.style.removeProperty('--theme-origin-x');
                    root.style.removeProperty('--theme-origin-y');
                    root.style.removeProperty('--theme-reveal-radius');
                }, () => {});
            } else {
                applyTheme();
            }
        });
    }

    const revealItems = document.querySelectorAll('.content-section .section-wrap');
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
