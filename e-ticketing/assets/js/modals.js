//Signup
var signupModal = document.getElementById("signup");
var signupBtn = document.getElementById("signupBtn");
var signupClose = document.getElementById("signupClose");

//Function to open modal
signupBtn.onclick = function() {
   signupModal.style.display = "block";
};

//Function to close modal
signupClose.onclick = function() {
   signupModal.style.display = "none";
};

//Login
var signinModal = document.getElementById("signin");
var signinBtn = document.getElementById("signinBtn");
var signinClose = document.getElementById("signinClose");

//Function to open modal
signinBtn.onclick = function() {
   signinModal.style.display = "block";
};

//Function to close modal
signinClose.onclick = function() {
   signinModal.style.display = "none";
};

//Contact Us
var contactModal = document.getElementById("contactus");
var contact = document.getElementById("contactUsBtn");
var contactClose = document.getElementById("contactClose");

//Function to open modal
contact.onclick = function() {
   contactModal.style.display = "block";
};

//Function to close modal
contactClose.onclick = function() {
   contactModal.style.display = "none";
};

//Function to close modal when clicked outside
window.onclick = function(event) {
   // if (event.target === signupModal) {
   //    signupModal.style.display = "none";
   // }
   if (event.target === signinModal) {
      signinModal.style.display = "none";
   }
   if (event.target === contactModal) {
      contactModal.style.display = "none";
   }
};



