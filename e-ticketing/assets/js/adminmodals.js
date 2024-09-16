const table = document.querySelector("table");
//Add Event
var addEventModal = document.getElementById("addevent");
var addBtn = document.getElementById("addBtn");
var addEventClose = document.getElementById("addEventClose");

//Function to open modal
addBtn.onclick = function () {
    addEventModal.style.display = "block";
}
//Function to close modal
addEventClose.onclick = function () {
    addEventModal.style.display = "none";
}

//Editing Events Modal
const editModal = document.getElementById("editEvent");
const editBtns = document.querySelectorAll("#editBtn");
const editClose = document.getElementById("editClose");

// Iterate over each button in the editBtns array.
editBtns.forEach(editBtn => {
    editBtn.addEventListener("click", (event) => {
        const card = event.target.closest(".column-item");

        const name = card.querySelector(".card-title").textContent;

        let dateInput = card.querySelector("input[name='date']");
        let fromDateInput = card.querySelector("input[name='from_date']");
        let toDateInput = card.querySelector("input[name='to_date']");

        let date, from_date, to_date;
        if (dateInput) {
            date = dateInput.value;
        } else if (fromDateInput && toDateInput) {
            from_date = fromDateInput.value;
            to_date = toDateInput.value;
        }

        const venue = card.querySelector(".venue").textContent;

        const time = card.querySelector("input[name='time']").value;

        const ticketPrice = card.querySelector("ul li:nth-child(4)").textContent.trim().replace(/[^\d]/g, "");

        const ticketCapacity = card.querySelector("ul li:nth-child(5)").textContent.slice(18).trim().replace(/[^\d]/g, "");

        //Reflect values of the card to unto the modal
        const modal = document.querySelector("#editEvent");
        modal.querySelector("#event-name-edt").textContent = `Edit event "${name}"`;
        modal.querySelector("input[name='eventId']").value = event.target.closest("td").dataset.id;
        modal.querySelector("input[name='event_name']").value = name;
        modal.querySelector("input[name='venue']").value = venue;
        modal.querySelector("input[name='time']").value = time;
        modal.querySelector("input[name='ticket_price']").value = ticketPrice;
        modal.querySelector("input[name='ticket_capacity']").value = ticketCapacity;

        let modalDateInput = modal.querySelector("input[name='date']");
        let modalFromDateInput = modal.querySelector("input[name='from_date']");
        let modalToDateInput = modal.querySelector("input[name='to_date']");

        let dateLabel = modal.querySelector("label[for='date']");
        let fromDateLabel = modal.querySelector("label[for='from_date']");
        let toDateLabel = modal.querySelector("label[for='to_date']");

        if (date) {
            if (modalDateInput) {
                modalDateInput.value = date;
                modalDateInput.style.display = "";
            }
            if (dateLabel) dateLabel.style.display = "";

            if (modalFromDateInput) modalFromDateInput.style.display = "none";
            if (fromDateLabel) fromDateLabel.style.display = "none";

            if (modalToDateInput) modalToDateInput.style.display = "none";
            if (toDateLabel) toDateLabel.style.display = "none";

        } else if (from_date && to_date) {
            if (modalFromDateInput) {
                modalFromDateInput.value = from_date;
                modalFromDateInput.style.display = "";
            }
            if (fromDateLabel) fromDateLabel.style.display = "";

            if (modalToDateInput) {
                modalToDateInput.value = to_date;
                modalToDateInput.style.display = "";
            }
            if (toDateLabel) toDateLabel.style.display = "";

            if (modalDateInput) modalDateInput.style.display = "none";
            if (dateLabel) dateLabel.style.display = "none";
        }

        let editModal = document.querySelector("#editEvent");
        editModal.style.display = "block";
    });
});





// Function to close modal
editClose.onclick = function () {
    editModal.style.display = "none";
};


//Deleting Events Alert
var deleteModal = document.getElementById("deleteEvent");
var deleteBtns = document.querySelectorAll("#deleteBtn");
var deleteClose = document.getElementById("deleteClose");


deleteBtns.forEach(deleteBtn => {
    deleteBtn.addEventListener("click", (event) => {
        const card = event.target.closest(".column-item");
        const name = card.querySelector(".card-title").textContent;

        const eventId = card.closest("td").dataset.id;
        const form = deleteModal.querySelector("form");
        form.querySelector('input[name="eventId"]').value = eventId;

        deleteModal.querySelector("#event-name-edt-del").textContent = `Delete "${name}" Event `;

        deleteModal.style.display = "block";
    });
});


//Function to close modal
deleteClose.onclick = function () {
    deleteModal.style.display = "none";
}





