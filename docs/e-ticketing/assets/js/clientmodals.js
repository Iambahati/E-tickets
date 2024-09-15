//Buying Tickets
var buyingModal = document.getElementById("buying");
var buyingBtn = document.getElementById("buyingBtn");
var buyingClose = document.getElementById("buyingClose");

//Function to open modal
buyingBtn.onclick = function() {
   buyingModal.style.display = "block";
}
//Function to close modal
buyingClose.onclick = function() {
   buyingModal.style.display = "none";
}
//Function to close modal when clicked outside
window.onclick = function(event) {
   if (event.target === ByteLengthQueuingStrategyModal) {
      buyingModal.style.display = "none";
   }
}

//QR Code 

var qrModal = document.getElementById("qr");
var qrCodeBtn = document.getElementById("qrCodeBtn");
var qrClose = document.getElementById("qrClose");

//Function to open modal
qrCodeBtn.onclick = function() {
   qrModal.style.display = "block";
}
//Function to close modal
qrClose.onclick = function() {
   qrModal.style.display = "none";
}
//Function to close modal when clicked outside
window.onclick = function(event) {
   if (event.target === ByteLengthQueuingStrategyModal) {
      qrModal.style.display = "none";
   }
}




//Spinner 
 // Function to show the spinner and disable the button
 function showSpinner() {
    document.getElementById('spinner').style.display = 'block';
    document.getElementById('myButton').disabled = true;
  }

  // Function to hide the spinner and enable the button
  function hideSpinner() {
    document.getElementById('spinner').style.display = 'none';
    document.getElementById('myButton').disabled = false;
  }

  // Add event listener to the button
  document.getElementById('myButton').addEventListener('click', function() {
    showSpinner();

    // Simulating some asynchronous operation
    setTimeout(function() {
      hideSpinner();
    }, 2000); // Hide spinner after 2 seconds (replace with your own logic)
  });