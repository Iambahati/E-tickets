// Get the elements
const form = document.querySelector('#signinForm');
const emailEl = form.elements['signin-email'];
const passwordEl = form.elements['signin-password'];
const msgEl = document.querySelector('#signin small');

// The URLs for redirection based on the response message
const URLs = {
  admin: 'http://127.0.0.1/ticketingapp/interfaces/admin.php',
  client: 'http://127.0.0.1/ticketingapp/interfaces/events.php',
};

// Validate input and show error or success
const validateAndShowStatus = (input, pattern, blankMsg, invalidMsg) => {
  const value = input.value.trim();
  const formField = input.parentElement;
  const errorEl = formField.querySelector('.error');

  formField.classList.remove('error', 'success');
  errorEl.innerText = '';

  if (!value) {
    formField.classList.add('error');
    errorEl.innerText = blankMsg;
    return false;
  } else if (!pattern.test(value)) {
    formField.classList.add('error');
    errorEl.innerText = invalidMsg;
    return false;
  } else {
    formField.classList.add('success');
    return true;
  }
};

// Display a message in the form
const displayMessage = (msg, isError = false) => {
  msgEl.innerHTML = `
    <div class="alert ${isError ? 'error' : 'success'}">
        <p class="text">
            <strong>${msg}</strong>
        </p>
    </div>`;
};

// Handle form submission
form.addEventListener('submit', async (e) => {
  e.preventDefault();

  msgEl.innerHTML = '';

  const isEmailValid = validateAndShowStatus(emailEl, /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/, 'Email cannot be blank.', 'Email is not valid.');
  const isPasswordValid = validateAndShowStatus(passwordEl, /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}$/, 'Password cannot be blank.', 'Password must have at least 8 characters that include at least 1 lowercase character, 1 uppercase character, 1 number, and 1 special character.');

  if (!isEmailValid || !isPasswordValid) return;

  const data = {
    formType: 'signin',
    email: emailEl.value,
    password: passwordEl.value,
  };

  try {
    const response = await fetch('assets/php/action.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data),
    });

    if (response.ok) {
      const { success, message } = await response.json();
      if (success) {
        const URL = URLs[message];
        if (URL) window.location.href = URL;
        else displayMessage(`Case ${message} not implemented yet`, true);
      } else {
        displayMessage(message, true);
      }
    }
  } catch {
    displayMessage('An error occurred during the network call.', true);
  }
});
