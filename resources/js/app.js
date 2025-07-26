document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.querySelector('.sidebar-toggle');
    const closeToggle = document.querySelector('.close-toggle');
    const sidebar = document.querySelector('.sidebar');
    if (toggle && sidebar) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });
    }
    if (closeToggle && sidebar) {
        closeToggle.addEventListener('click', function () {
            sidebar.classList.remove('open');
        });
    }
});
