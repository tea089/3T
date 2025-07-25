document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const errorMessage = document.getElementById('errorMessage');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            
            // Clear previous errors
            errorMessage.textContent = '';
            errorMessage.style.display = 'none';
            
            // Validate password
            if (password.length < 8) {
                e.preventDefault();
                errorMessage.textContent = 'Password must be at least 8 characters';
                errorMessage.style.display = 'block';
                return false;
            }
            
            // Add other validations as needed
            return true;
        });
    }
});