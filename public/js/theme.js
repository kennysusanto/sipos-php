(() => {
    const STORAGE_KEY = 'theme';
    const root = document.documentElement;

    const applyTheme = (theme) => {
        const normalized = theme === 'dark' ? 'dark' : 'light';
        root.setAttribute('data-theme', normalized);
        localStorage.setItem(STORAGE_KEY, normalized);
        document.querySelectorAll('#theme-toggle').forEach((toggleInput) => {
            toggleInput.checked = normalized === 'dark';
        });
    };

    const savedTheme = localStorage.getItem(STORAGE_KEY);
    const preferredTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    const initialTheme = savedTheme === 'dark' || savedTheme === 'light' ? savedTheme : preferredTheme;
    applyTheme(initialTheme);

    document.querySelectorAll('#theme-toggle').forEach((toggleInput) => {
        toggleInput.addEventListener('change', () => {
            applyTheme(toggleInput.checked ? 'dark' : 'light');
        });
    });

    const profileButton = document.getElementById('profile-button');
    const profileDropdown = document.getElementById('profile-dropdown');

    if (profileButton && profileDropdown) {
        profileButton.addEventListener('click', (event) => {
            event.stopPropagation();
            const isOpen = profileDropdown.classList.toggle('is-open');
            profileButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        document.addEventListener('click', (event) => {
            if (!profileDropdown.contains(event.target) && event.target !== profileButton) {
                profileDropdown.classList.remove('is-open');
                profileButton.setAttribute('aria-expanded', 'false');
            }
        });
    }
})();
