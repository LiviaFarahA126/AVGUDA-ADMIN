let groups = [
    {
        id: "GR001",
        name: "Tech Enthusiasts",
        image: "images/group1.jpg",
        description: "A group for technology lovers.",
    },
    {
        id: "GR002",
        name: "Book Club",
        image: "images/group2.jpg",
        description: "Discussing the best books out there.",
    },
];

// Load Groups into Table
function loadGroups() {
    const tableBody = document.querySelector("tbody");
    tableBody.innerHTML = "";

    groups.forEach((group, index) => {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${group.id}</td>
            <td>${group.name}</td>
            <td><img src="${group.image}" alt="${group.name}" style="width:50px;height:auto;"></td>
            <td>${group.description}</td>
            <td>
                <button class="edit-btn" onclick="openEditGroupModal(${index})">Edit</button>
                <button class="delete-btn" onclick="deleteGroup(${index})">Delete</button>
            </td>
        `;
        tableBody.appendChild(row);
    });
}

// Show Add Group Modal
function showAddGroupModal() {
    document.getElementById("addGroupModal").style.display = "flex";
}

// Close Add Group Modal
function closeAddGroupModal() {
    document.getElementById("addGroupModal").style.display = "none";
    resetAddGroupForm();
}

// Reset Add Group Form
function resetAddGroupForm() {
    document.getElementById("addGroupForm").reset();
    const previewImage = document.getElementById("previewImage");
    previewImage.style.display = "none";
    previewImage.src = "";
}

// Handle Add Group Submission
document.getElementById("addGroupForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const id = document.getElementById("groupID").value;
    const name = document.getElementById("groupName").value;
    const description = document.getElementById("groupDescription").value;
    const fileInput = document.getElementById("groupImage");

    if (fileInput.files.length > 0) {
        const reader = new FileReader();
        reader.onload = function (event) {
            const image = event.target.result;
            groups.push({ id, name, image, description });
            loadGroups();
            closeAddGroupModal();
        };
        reader.readAsDataURL(fileInput.files[0]);
    }
});

// Show Preview for Add Group Modal
document.getElementById("groupImage").addEventListener("change", function (event) {
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
        resetAddGroupForm();
    }
});

// Open Edit Group Modal
function openEditGroupModal(index) {
    const group = groups[index];
    document.getElementById("editGroupID").value = group.id;
    document.getElementById("editGroupName").value = group.name;
    document.getElementById("editGroupDescription").value = group.description;

    const previewImage = document.getElementById("editPreviewImage");
    previewImage.src = group.image;
    previewImage.style.display = "block";

    document.getElementById("editGroupModal").style.display = "flex";

    // Handle Save Changes
    document.getElementById("editGroupForm").onsubmit = function (e) {
        e.preventDefault();

        const updatedName = document.getElementById("editGroupName").value;
        const updatedDescription = document.getElementById("editGroupDescription").value;
        const updatedImageFile = document.getElementById("editGroupImage").files[0];

        if (updatedImageFile) {
            const reader = new FileReader();
            reader.onload = function (event) {
                group.image = event.target.result;
                updateGroupData(index, updatedName, updatedDescription, group.image);
            };
            reader.readAsDataURL(updatedImageFile);
        } else {
            updateGroupData(index, updatedName, updatedDescription);
        }
    };
}

// Update Group Data
function updateGroupData(index, name, description, image = null) {
    groups[index].name = name;
    groups[index].description = description;
    if (image) groups[index].image = image;

    loadGroups();
    closeEditGroupModal();
}

// Close Edit Group Modal
function closeEditGroupModal() {
    document.getElementById("editGroupModal").style.display = "none";
}

// Delete Group
function deleteGroup(index) {
    groups.splice(index, 1);
    loadGroups();
}

// Load Groups on Page Load
document.addEventListener("DOMContentLoaded", loadGroups);
