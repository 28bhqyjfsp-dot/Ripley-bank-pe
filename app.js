document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("loginForm");
  const emailInput = document.getElementById("email");
  const passwordInput = document.getElementById("password");
  const emailError = document.getElementById("emailError");
  const passwordError = document.getElementById("passwordError");
  const openAccountBtn = document.getElementById("openAccountBtn");
  const togglePasswordBtn = document.getElementById("togglePassword");

  // Toggle mostrar/ocultar contraseña
  togglePasswordBtn.addEventListener("click", () => {
    const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
    passwordInput.setAttribute("type", type);
    togglePasswordBtn.classList.toggle("active");
  });

  // Validación del formulario
  form.addEventListener("submit", (e) => {
    let isValid = true;
    const email = emailInput.value.trim();
    const password = passwordInput.value.trim();

    // Validar email
    if (!email) {
      e.preventDefault();
      emailError.textContent = "Ingresa tu correo electrónico.";
      emailInput.focus();
      isValid = false;
    } else {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        e.preventDefault();
        emailError.textContent = "El formato de correo no es válido.";
        emailInput.focus();
        isValid = false;
      } else {
        emailError.textContent = "";
      }
    }

    // Validar contraseña
    if (!password) {
      e.preventDefault();
      passwordError.textContent = "Ingresa tu contraseña.";
      if (isValid) passwordInput.focus();
      isValid = false;
    } else if (password.length < 6) {
      e.preventDefault();
      passwordError.textContent = "La contraseña debe tener al menos 6 caracteres.";
      if (isValid) passwordInput.focus();
      isValid = false;
    } else {
      passwordError.textContent = "";
    }

    // Si todo es válido, el formulario se envía al action del PHP
    // No se previene el evento, por lo que el form se envía normalmente
  });

  // Limpiar errores al escribir
  emailInput.addEventListener("input", () => {
    emailError.textContent = "";
  });

  passwordInput.addEventListener("input", () => {
    passwordError.textContent = "";
  });

  // Botón de registro
  openAccountBtn.addEventListener("click", () => {
    window.location.href = "registro.html";
  });
});