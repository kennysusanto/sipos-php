<script>
    (() => {
        const STORAGE_KEY = 'theme';
        const savedTheme = localStorage.getItem(STORAGE_KEY);
        const preferredTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        const theme = savedTheme === 'dark' || savedTheme === 'light' ? savedTheme : preferredTheme;
        document.documentElement.setAttribute('data-theme', theme);
    })();
</script>
