document.addEventListener('DOMContentLoaded', function () {

    // Menu side bar
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

    // Dashboard side bar
    const adminToggle = document.querySelector('.admin-sidebar-toggle');
    const adminSidebar = document.querySelector('.sidebar.admin-panel');
    const adminContainer = document.querySelector('.container.page.dashboard');
    if (adminToggle && adminSidebar) {

    adminToggle.addEventListener('click', function () {
        adminSidebar.classList.toggle('close');
        adminToggle.classList.toggle('close');
        adminContainer.classList.toggle('close');
    });

    adminToggle.addEventListener('click', function () {
        adminSidebar.classList.toggle('open');
        adminToggle.classList.toggle('open');
        adminContainer.classList.toggle('open');
    });

    }


});
