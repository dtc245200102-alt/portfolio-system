(() => {
    const root = document.documentElement;
    const button = document.getElementById('theme-toggle');
    const stored = localStorage.getItem('portfolio-theme');

    if (stored === 'light' || (!stored && window.matchMedia('(prefers-color-scheme: light)').matches)) {
        root.classList.remove('dark');
    }

    if (button) {
        button.addEventListener('click', () => {
            root.classList.toggle('dark');
            localStorage.setItem('portfolio-theme', root.classList.contains('dark') ? 'dark' : 'light');
        });
    }
})();
