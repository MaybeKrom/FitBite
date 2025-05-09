document.addEventListener('DOMContentLoaded', function() {
    
    const menuButton = document.getElementById('user-menu-button');
    const dropdownMenu = document.getElementById('user-menu-dropdown');

    if (menuButton && dropdownMenu) {
        menuButton.addEventListener('click', function(event) {
            dropdownMenu.classList.toggle('show-menu');
            event.stopPropagation(); 
        });

        document.addEventListener('click', function(event) {
            if (dropdownMenu.classList.contains('show-menu') && !menuButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                 dropdownMenu.classList.remove('show-menu');
            }
        });
    }

    const setupPasswordToggle = (buttonId, inputId) => {
        const toggleButton = document.getElementById(buttonId);
        const passwordInput = document.getElementById(inputId);

        if (toggleButton && passwordInput) {
            toggleButton.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                const icon = this.querySelector('i');
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });
        }
    };

    setupPasswordToggle('togglePassword', 'password'); 
    setupPasswordToggle('toggleConfirmPassword', 'confirm_password'); 


});