// Toggle Password Visibility
function togglePassword(inputId, button) {
    const input = document.getElementById(inputId);
    if (input.type === "password") {
        input.type = "text";
        button.style.color = "#38aaff";
    } else {
        input.type = "password";
        button.style.color = "#7890a6";
    }
}

// Cursor Light Motion
document.addEventListener('DOMContentLoaded', () => {
    const cursorLight = document.getElementById('cursorLight');
    if (cursorLight) {
        document.addEventListener('mousemove', (e) => {
            cursorLight.style.left = e.clientX + 'px';
            cursorLight.style.top = e.clientY + 'px';
        });
    }
});