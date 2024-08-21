console.log('cargand0');
document.addEventListener('DOMContentLoaded', function () {
    const togglePasswordButtons = document.querySelectorAll('[data-toggle="password"]');

    togglePasswordButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const passwordField = document.getElementById(button.dataset.target);
            const passwordFieldType = passwordField.getAttribute('type');
            const icon = this.querySelector('i');

            if (passwordFieldType === 'password') {
                passwordField.setAttribute('type', 'text');
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                passwordField.setAttribute('type', 'password');
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        });
    });
});
