const signinForm = document.getElementById('signinForm');
const signinEmail = document.querySelector('[name="signinemail"]');
const signinPassword = document.querySelector('[name="signinpassword"]');

const setError2 = (element, message) => {
  const inputField = element.parentElement;
  const errorDisplay = inputField.querySelector('.error');

  errorDisplay.innerText = message;
  inputField.classList.add('error');
  inputField.classList.remove('success');
};

const setSuccess2 = element => {
  const inputField = element.parentElement;
  const errorDisplay = inputField.querySelector('.error');

  errorDisplay.innerText = '';
  inputField.classList.add('success');
  inputField.classList.remove('error');
};

const isValidEmail2 = email => {
  const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(String(email).toLowerCase());
};

const isPasswordValid2 = element => {
  const re = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}$/;
  return re.test(String(element));
};

const validateInputs2 = () => {
  const sEmailValue = signinEmail.value.trim();
  const sPasswordValue = signinPassword.value.trim();
  let isValid = true;

  if (sEmailValue === '') {
    setError2(signinEmail, 'Email is required');
    isValid = false;
  } else if (!isValidEmail2(sEmailValue)) {
    setError2(signinEmail, 'Provide a valid email address.');
    isValid = false;
  } else {
    setSuccess2(signinEmail);
  }

  if (sPasswordValue === '') {
    setError2(signinPassword, 'Password is required');
    isValid = false;
  } else if (!isPasswordValid2(sPasswordValue)) {
    setError2(signinPassword, 'Password must have at least 8 characters that include at least 1 lowercase character, 1 uppercase character, 1 number, and 1 special character.');
    isValid = false;
  } else {
    setSuccess2(signinPassword);
  }

  return isValid;
};

signinForm.addEventListener('submit', e => {
  e.preventDefault();
  if (validateInputs2()) {
    signinForm.submit();
  }
});
