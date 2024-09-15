//Add Event
var addEventModal = document.getElementById("addevent");
var addBtn = document.getElementById("addBtn");
var addEventClose = document.getElementById("addEventClose");

//Function to open modal
addBtn.onclick = function() {
    addEventModal.style.display = "block";
}
//Function to close modal
addEventClose.onclick = function() {
    addEventModal.style.display = "none";
}
//Function to close modal when clicked outside
window.onclick = function(event) {
    if (event.target === addEventModal) {
        addEventModal.style.display = "none";
    }
}

//Deleting Events Alert
var deleteModal = document.getElementById("deleteEvent");
var deleteBtn = document.getElementById("deleteBtn");
var deleteClose = document.getElementById("deleteClose");

//Function to open modal
deleteBtn.onclick = function() {
   deleteModal.style.display = "block";
}
//Function to close modal
deleteClose.onclick = function() {
   deleteModal.style.display = "none";
}
//Function to close modal when clicked outside
window.onclick = function(event) {
   if (event.target === ByteLengthQueuingStrategyModal) {
      deleteModal.style.display = "none";
   }
}

//Editing Events Modal
var editModal = document.getElementById("editEvent");
var editBtn = document.getElementById("editBtn");
var editClose = document.getElementById("editClose");

//Function to open modal
editBtn.onclick = function() {
   editModal.style.display = "block";
}
//Function to close modal
editClose.onclick = function() {
   editModal.style.display = "none";
}
//Function to close modal when clicked outside
window.onclick = function(event) {
   if (event.target === ByteLengthQueuingStrategyModal) {
      editModal.style.display = "none";
   }
}

