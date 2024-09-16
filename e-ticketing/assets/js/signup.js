// Select form elements
const signupForm = document.querySelector("#signupForm");
// Create an object with all elements needed from the form
const formElements = {
  formType: signupForm.querySelector('[name="formType-signup"]'),
  username: signupForm.querySelector('[name="username"]'),
  email: signupForm.querySelector('[name="email"]'),
  contact: signupForm.querySelector('[name="contact"]'),
  password: signupForm.querySelector('[name="password"]'),
  confirmPassword: signupForm.querySelector('[name="confirm_password"]'),
  message: document.querySelector('#signup small'),
};

// Set validation patterns for each input field
const patterns = {
  username: /^[a-zA-Z\s]{4,22}$/,
  email: /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
  contact: /^(\+254|0)?[071]\d{8}$/,
  password: /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}$/,
  confirmPassword: /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}$/
};


// Set error messages for each input field
const messages = {
  username: 'Username should only contain letters and be between 4 and 22 characters in length.',
  email: 'Email is not valid.',
  contact: 'Contact number must begin with +254 or 0 then followed by 1 or 7, and other 7 digits',
  password: 'Password must have at least 8 characters that include at least 1 lowercase character, 1 uppercase character, 1 number, and 1 special character.',
  confirmPassword: 'Passwords do not match.',
};

// Function to check if input value is required
const isRequired = value => Boolean(value);

// Function to validate input
// Check if input value is required and matches the pattern
// If not, it shows error
const validateInput = (input, pattern, message) => {
  const value = input.value.trim();
  if (!isRequired(value)) {
    showError(input, `${input.name} cannot be blank.`);
    return false;
  } else if (!pattern.test(value)) {
    showError(input, message);
    return false;
  } else {
    showSuccess(input);
    return true;
  }
};

// Function to show validation errors
// Changes the class of the form field and displays an error message
const showError = (element, message) => {
  const formField = element.parentElement;
  formField.classList.remove('success');
  formField.classList.add('error');
  formField.querySelector('.error').innerText = message;
};

// Function to show validation success
// Changes the class of the form field and clears any error message
const showSuccess = element => {
  const formField = element.parentElement;
  formField.classList.remove('error');
  formField.classList.add('success');
  formField.querySelector('.error').innerText = '';
};

// Function to validate form
// Iterates over each form field and checks if it is valid
// Checks if password and confirm password fields match
const validateForm = () => {
  let isValid = true;
  for (let name in formElements) {
    if (patterns[name]) {
      isValid &= validateInput(formElements[name], patterns[name], messages[name]);
    }
  }
  if (formElements.password.value !== formElements.confirmPassword.value) {
    showError(formElements.confirmPassword, messages.confirmPassword);
    isValid = false;
  }
  return isValid;
};

// Function to display message
// Displays a message on the form, success or error
const displaySuccessMessageOnSignUp = (message, isError = false) => {
  formElements.message.innerHTML = `
    <div class="alert ${isError ? 'error' : 'success'}">
      <p class="text">
        <strong>${message}</strong>
      </p>
    </div>`;
};

// Handle form submission
// On form submit, clears previous messages, validates form, and sends data to server
signupForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  formElements.message.innerHTML = ''; // Clear previous messages

  if (validateForm()) {
    const requestBody = {
      formType: formElements.formType.value,
      username: formElements.username.value,
      email: formElements.email.value,
      contact: formElements.contact.value,
      password: formElements.password.value,
    };

    try {
      const response = await fetch('assets/php/action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(requestBody)
      });

      const responseData = await response.json();
      if (response.ok) {
        displaySuccessMessageOnSignUp(responseData.message, !responseData.success);
      } else {
        throw new Error('Network response was not OK.');
      }
    } catch (error) {
      displaySuccessMessageOnSignUp('Some error occurred. Please try again.', true);
    }
  }
});
