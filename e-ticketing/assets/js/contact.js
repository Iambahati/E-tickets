const contactForm = document.getElementById("contactForm");
const msgContact = document.querySelector('#contactus small');

const displayMessage = (message, type) => {
  msgContact.innerHTML = `
    <div class="alert ${type}">
        <p class="text">
            <strong>${message}</strong>
        </p>
    </div>`;
};

contactForm.addEventListener('submit', async (e) => {
  e.preventDefault(); // Prevent form submission

  const emailFromEl = contactForm['email_from'].value.trim();
  const subjectEl = contactForm['subject'].value.trim();
  const messageEl = contactForm['message'].value.trim();

  // Clear previous messages
  msgContact.innerHTML = '';

  // Input validations
  if (!emailFromEl) {
    displayMessage('Email cannot be blank.', 'error');
    return;
  }

  const emailRegex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  if (!emailRegex.test(emailFromEl)) {
    displayMessage('Email is not valid.', 'error');
    return;
  }

  if (!subjectEl || !messageEl) {
    displayMessage('Subject or Message cannot be blank.', 'error');
    return;
  }

  const alphaRegex = /^[a-zA-Z\s]+$/;
  if (!alphaRegex.test(subjectEl) || !alphaRegex.test(messageEl)) {
    displayMessage('Subject or Message should only contain letters.', 'error');
    return;
  }

  // Preparing request body
  const requestBody = {
    formType: 'contact',
    email: emailFromEl,
    subject: subjectEl,
    message: messageEl
  };

  // Sending POST request
  try {
    const response = await fetch('assets/php/action.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(requestBody)
    });

    if (response.ok) {
      const responseData = await response.json();
      if (responseData.success) {
        displayMessage(responseData.message, 'success');
      } else {
        displayMessage(responseData.message, 'error');
      }
    }
  } catch (error) {
    console.log(error);
    displayMessage('An error occurred during the network call.', 'error');
  }
});
