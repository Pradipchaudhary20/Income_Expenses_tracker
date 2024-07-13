document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.querySelector('.bank-account-toggle');
    const submenu = document.querySelector('.sidebar-submenu');

    toggle.addEventListener('click', function(event) {
        event.preventDefault();
        submenu.classList.toggle('active');
    });
});
