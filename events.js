let events = [
    {
        id: "EV001",
        image: "images/event1.jpg",
        name: "Book Fair 2024",
        description: "A grand event showcasing the best books of the year.",
    },
];

// Load Events to the Table
function loadEvents() {
    const tableBody = document.querySelector("tbody");
    tableBody.innerHTML = "";

    events.forEach((event, index) => {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${event.id}</td>
            <td><img src="${event.image}" alt="${event.name}" style="width:50px;height:auto;"></td>
            <td>${event.name}</td>
            <td>${event.description}</td>
            <td>
                <button class="edit-btn" onclick="openEditEventModal(${index})">Edit</button>
                <button class="delete-btn" onclick="deleteEvent(${index})">Delete</button>
            </td>
        `;
        tableBody.appendChild(row);
    });
}

// Show Add Event Modal
function showAddEventModal() {
    document.getElementById("addEventModal").style.display = "flex";
}

// Close Add Event Modal
function closeAddEventModal() {
    document.getElementById("addEventModal").style.display = "none";
    resetAddEventForm();
}

// Reset Add Event Form
function resetAddEventForm() {
    document.getElementById("addEventForm").reset();
    const previewImage = document.getElementById("previewImage");
    previewImage.style.display = "none";
    previewImage.src = "";
}

// Handle Add Event Submission
document.getElementById("addEventForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const id = document.getElementById("eventID").value;
    const fileInput = document.getElementById("eventImage");
    const name = document.getElementById("eventName").value;
    const description = document.getElementById("eventDescription").value;

    if (fileInput.files.length > 0) {
        const reader = new FileReader();
        reader.onload = function (event) {
            const image = event.target.result;
            events.push({ id, image, name, description });
            loadEvents();
            closeAddEventModal();
        };
        reader.readAsDataURL(fileInput.files[0]);
    }
});

// Show Preview for Add Event Modal
document.getElementById("eventImage").addEventListener("change", function (event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const previewImage = document.getElementById("previewImage");
            previewImage.src = e.target.result;
            previewImage.style.display = "block";
        };
        reader.readAsDataURL(file);
    } else {
        resetAddEventForm();
    }
});

// Open Edit Event Modal
function openEditEventModal(index) {
    const event = events[index];
    document.getElementById("editEventID").value = event.id;
    document.getElementById("editEventName").value = event.name;
    document.getElementById("editEventDescription").value = event.description;

    const previewImage = document.getElementById("editPreviewImage");
    previewImage.src = event.image;
    previewImage.style.display = "block";

    document.getElementById("editEventModal").style.display = "flex";

    document.getElementById("editEventForm").onsubmit = function (e) {
        e.preventDefault();

        const updatedName = document.getElementById("editEventName").value;
        const updatedDescription = document.getElementById("editEventDescription").value;
        const updatedImageFile = document.getElementById("editEventImage").files[0];

        if (updatedImageFile) {
            const reader = new FileReader();
            reader.onload = function (event) {
                event.image = event.target.result;
                updateEventData(index, updatedName, updatedDescription, event.image);
            };
            reader.readAsDataURL(updatedImageFile);
        } else {
            updateEventData(index, updatedName, updatedDescription);
        }
    };
}

// Update Event Data
function updateEventData(index, name, description, image = null) {
    events[index].name = name;
    events[index].description = description;
    if (image) events[index].image = image;

    loadEvents();
    closeEditEventModal();
}

// Close Edit Event Modal
function closeEditEventModal() {
    document.getElementById("editEventModal").style.display = "none";
}

// Delete Event
function deleteEvent(index) {
    events.splice(index, 1);
    loadEvents();
}

// Load Events on Page Load
document.addEventListener("DOMContentLoaded", loadEvents);
