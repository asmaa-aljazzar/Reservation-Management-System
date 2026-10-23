(() => {
    const root = document.documentElement;
    const themeButton = document.querySelector('[data-theme-toggle]');
    const themeLabel = document.querySelector('[data-theme-label]');
    const themeIcon = document.querySelector('[data-theme-icon]');
    const navButton = document.querySelector('[data-nav-toggle]');
    const navMenu = document.querySelector('[data-nav-menu]');

    const setTheme = (theme) => {
        const isDark = theme === 'dark';
        root.dataset.theme = isDark ? 'dark' : 'light';
        themeButton?.setAttribute('aria-pressed', String(isDark));
        if (themeLabel) themeLabel.textContent = isDark ? 'Light mode' : 'Dark mode';
        if (themeIcon) themeIcon.textContent = isDark ? '☼' : '◐';
    };

    let savedTheme = null;
    try { savedTheme = localStorage.getItem('study-room-theme'); } catch (_error) { /* Storage may be disabled. */ }
    setTheme(savedTheme === 'dark' ? 'dark' : 'light');

    themeButton?.addEventListener('click', () => {
        const nextTheme = root.dataset.theme === 'dark' ? 'light' : 'dark';
        setTheme(nextTheme);
        try { localStorage.setItem('study-room-theme', nextTheme); } catch (_error) { /* Keep the current page theme. */ }
    });

    navButton?.addEventListener('click', () => {
        const open = navButton.getAttribute('aria-expanded') === 'true';
        navButton.setAttribute('aria-expanded', String(!open));
        navMenu?.classList.toggle('is-open', !open);
    });
    navMenu?.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => {
        navButton?.setAttribute('aria-expanded', 'false');
        navMenu.classList.remove('is-open');
    }));

    document.querySelectorAll('[data-flash-dismiss]').forEach((button) => {
        button.addEventListener('click', () => button.closest('.flash-toast')?.remove());
    });

    const revealItems = document.querySelectorAll('[data-reveal]');
    if ('IntersectionObserver' in window && revealItems.length) {
        root.classList.add('reveal-ready');
        const observer = new IntersectionObserver((entries, currentObserver) => entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                currentObserver.unobserve(entry.target);
            }
        }), { threshold: 0.12 });
        revealItems.forEach((item) => observer.observe(item));
    } else {
        revealItems.forEach((item) => item.classList.add('is-visible'));
    }
})();
