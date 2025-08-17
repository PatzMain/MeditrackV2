// Create floating particles
function createParticles() {
    const particleContainer = document.getElementById('particles');
    const particleCount = 20;

    for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.animationDelay = Math.random() * 15 + 's';
        particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
        particleContainer.appendChild(particle);
    }
}

// Toggle password visibility
function togglePassword() {
    const passwordField = document.getElementById('password');
    const showPasswordSpan = document.querySelector('.show-password');

    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        showPasswordSpan.textContent = '👁️‍🗨️';
        showPasswordSpan.style.color = 'var(--error-color)';
    } else {
        passwordField.type = 'password';
        showPasswordSpan.textContent = '👁️';
        showPasswordSpan.style.color = 'var(--text-secondary)';
    }
}

// Initialize effects on page load
document.addEventListener('DOMContentLoaded', function () {
    createParticles();

    // Add subtle parallax effect to background shapes
    document.addEventListener('mousemove', function (e) {
        const shapes = document.querySelectorAll('.shape');
        const x = e.clientX / window.innerWidth;
        const y = e.clientY / window.innerHeight;

        shapes.forEach((shape, index) => {
            const speed = (index + 1) * 0.5;
            const xPos = (x - 0.5) * speed;
            const yPos = (y - 0.5) * speed;
            shape.style.transform = `translate(${xPos}px, ${yPos}px)`;
        });
    });

    // Enhanced input focus effects
    const inputs = document.querySelectorAll('input[type="text"], input[type="password"]');
    inputs.forEach((input, index) => {
        input.addEventListener('focus', function () {
            this.parentElement.style.transform = 'scale(1.02) translateY(-2px)';
            this.parentElement.parentElement.querySelector('label').style.color = 'var(--focus-color)';
        });

        input.addEventListener('blur', function () {
            this.parentElement.style.transform = 'scale(1) translateY(0)';
            this.parentElement.parentElement.querySelector('label').style.color = 'var(--text-primary)';
        });

        // Add staggered animation delay
        input.closest('.form-group').style.setProperty('--delay', (index * 0.1) + 's');
    });
});

// Clear error messages after animation
setTimeout(function () {
    const messages = document.querySelectorAll('.error-message, .success-message');
    messages.forEach(msg => {
        setTimeout(() => msg.remove(), 5000);
    });
}, 100);