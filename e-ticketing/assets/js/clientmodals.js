//Buying Tickets
const buyingModal = document.getElementById("buying");
const buyingBtns = document.querySelectorAll("#buyingBtn");
const buyingClose = document.getElementById("buyingClose");

// Function to open modal
buyingBtns.forEach(buyingBtn => {
   buyingBtn.addEventListener("click", (event) => {
       const card = event.target.closest(".column-item");
       const name = card.querySelector(".card-title").textContent;
       const ticketPrice = card.querySelector("ul li:nth-child(4)").textContent.slice(18).trim();
       const eventId = card.closest("td").dataset.id;

       // Select the form and its fields in the modal and set their values
       const form = buyingModal.querySelector("form");
       form.querySelector('input[name="eventId"]').value = eventId;
       form.querySelector('input[name="ticket_price"]').value = ticketPrice.replace(/[^\d]/g, "");
       buyingModal.querySelector("#event-name-buy").textContent = `Purchase ticket(s) for "${name}" event`;

       // Display the modal
       buyingModal.style.display = "block";

       // Call updateTotalPrice to set the initial total price in the modal
       updateTotalPrice();
   });
});

const increaseQuantityBtns = document.querySelectorAll(".increase-quantity");
const decreaseQuantityBtns = document.querySelectorAll(".decrease-quantity");

increaseQuantityBtns.forEach(btn => {
   btn.addEventListener("click", (event) => {
       const card = event.target.closest(".quantity-selector");
       const quantityInput = card.querySelector(".quantity");
       const currentQuantity = parseInt(quantityInput.value);
       if (currentQuantity < 5) {
           quantityInput.value = currentQuantity + 1;
           updateTotalPrice();
       }
   });
});

decreaseQuantityBtns.forEach(btn => {
   btn.addEventListener("click", (event) => {
       const card = event.target.closest(".quantity-selector");
       const quantityInput = card.querySelector(".quantity");
       const currentQuantity = parseInt(quantityInput.value);
       if (currentQuantity > 1) {
           quantityInput.value = currentQuantity - 1;
           updateTotalPrice();
       }
   });
});

function updateTotalPrice() {
   const quantity = parseInt(buyingModal.querySelector(".quantity").value);
   const price = parseFloat(buyingModal.querySelector('input[name="ticket_price"]').value);
   const total = quantity * price;
   buyingModal.querySelector('#total-price-display').textContent = total.toFixed(2);
}
// Function to close modal
buyingClose.onclick = function () {
   buyingModal.style.display = "none";
};





