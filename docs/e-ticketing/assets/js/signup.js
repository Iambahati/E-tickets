const username = document.querySelector('[name="username"]');
const email = document.querySelector('[name="email"]');
const contact = document.querySelector('[name="contact"]');
const password = document.querySelector('[name="password"]');
const confirm_password = document.querySelector('[name="confirm_password"]');
const signUpForm = document.getElementById('signupForm');

const setError = (element, message) => {
  const inputField = element.parentElement;
  const errorDisplay = inputField.querySelector('.error');

  inputField.classList.add('error');
  inputField.classList.remove('success');
  errorDisplay.innerText = message;

};

const setSuccess = element => {
  const inputField = element.parentElement;
  const errorDisplay = inputField.querySelector('.error');

  errorDisplay.innerText = '';
  inputField.classList.add('success');
  inputField.classList.remove('error');
};

const isEmailValid = element => {
  const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(String(element).toLowerCase());
};

const isContactValid = element => {
  const re = /^(\+254|0)?[071]\d{8}$/;
  return re.test(String(element));
};

const isPasswordValid = element => {
  const re = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}$/;
  return re.test(String(element));
};

const validateInputs = () => {
  const usernameValue = username.value.trim();
  const emailValue = email.value.trim();
  const contactValue = contact.value.trim();
  const passwordValue = password.value.trim();
  const confirm_passwordValue = confirm_password.value.trim();
  let isValid = true;

  if (usernameValue === '') {
    setError(username, 'Username is required.');
    isValid = false;
  } else {
    setSuccess(username);
  }

  if (emailValue === '') {
    setError(email, 'Email is required');
    isValid = false;
  } else if (!isEmailValid(emailValue)) {
    setError(email, 'Provide a valid email address.');
    isValid = false;
  } else {
    setSuccess(email);
  }

  if (contactValue === '') {
    setError(contact, 'Contact is required');
    isValid = false;
  } else if (!isContactValid(contactValue)) {
    setError(contact, 'contact number must begin with +254 or 0 then followed by 1 or 7, and 7 digits');
    isValid = false;
  } else {
    setSuccess(contact);
  }

  if (passwordValue === '') {
    setError(password, 'Password is required');
    isValid = false;
  } else if (!isPasswordValid(passwordValue)) {
    setError(password, 'Password must have at least 8 characters that include at least 1 lowercase character, 1 uppercase character, 1 number, and 1 special character.');
    isValid = false;
  } else {
    setSuccess(password);
  }

  if (confirm_passwordValue === '') {
    setError(confirm_password, 'Please confirm your password.');
    isValid = false;
  } else if (confirm_passwordValue !== passwordValue) {
    setError(confirm_password, "Passwords don't match!");
    isValid = false;
  } else {
    setSuccess(confirm_password);
  }

  return isValid;
};

signUpForm.addEventListener('submit', e => {
  e.preventDefault();
  if (validateInputs()) {
    // All inputs are valid, proceed with form submission
    signUpForm.submit();
  }
});
