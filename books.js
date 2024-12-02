let books = [
    {
        id: "BK001",
        image: "images/book-cover1.jpg",
        title: "The Catcher in the Rye",
        author: "J.D. Salinger",
    },
    {
        id: "BK002",
        image: "images/book-cover2.jpg",
        title: "To Kill a Mockingbird",
        author: "Harper Lee",
    },
];

// Load Books to Table
function loadBooks() {
    const tableBody = document.querySelector("tbody");
    tableBody.innerHTML = "";

    books.forEach((book, index) => {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${book.id}</td>
            <td><img src="${book.image}" alt="${book.title}" style="width:50px;height:auto;"></td>
            <td>${book.title}</td>
            <td>${book.author}</td>
            <td>
                <button class="edit-btn" onclick="openEditBookModal(${index})">Edit</button>
                <button class="delete-btn" onclick="deleteBook(${index})">Delete</button>
            </td>
        `;
        tableBody.appendChild(row);
    });
}

// Show Add Book Modal
function showAddBookModal() {
    document.getElementById("addBookModal").style.display = "flex";
}

// Close Add Book Modal
function closeAddBookModal() {
    document.getElementById("addBookModal").style.display = "none";
    resetAddBookForm();
}

// Reset Add Book Form
function resetAddBookForm() {
    document.getElementById("addBookForm").reset();
    const previewImage = document.getElementById("previewImage");
    previewImage.style.display = "none";
    previewImage.src = "";
}

// Handle Add Book Submission
document.getElementById("addBookForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const id = document.getElementById("bookID").value;
    const fileInput = document.getElementById("bookImage");
    const title = document.getElementById("bookTitle").value;
    const author = document.getElementById("bookAuthor").value;

    if (fileInput.files.length > 0) {
        const reader = new FileReader();
        reader.onload = function (event) {
            const image = event.target.result;
            books.push({ id, image, title, author });
            loadBooks();
            closeAddBookModal();
        };
        reader.readAsDataURL(fileInput.files[0]);
    }
});

// Open Edit Book Modal
function openEditBookModal(index) {
    const book = books[index];
    document.getElementById("editBookID").value = book.id;
    document.getElementById("editBookTitle").value = book.title;
    document.getElementById("editBookAuthor").value = book.author;

    const previewImage = document.getElementById("editPreviewImage");
    previewImage.src = book.image;
    previewImage.style.display = "block";

    document.getElementById("editBookModal").style.display = "flex";

    // Save Changes
    document.getElementById("editBookForm").onsubmit = function (e) {
        e.preventDefault();

        const updatedTitle = document.getElementById("editBookTitle").value;
        const updatedAuthor = document.getElementById("editBookAuthor").value;
        const updatedImageFile = document.getElementById("editBookImage").files[0];

        if (updatedImageFile) {
            const reader = new FileReader();
            reader.onload = function (event) {
                book.image = event.target.result;
                updateBookData(index, updatedTitle, updatedAuthor, book.image);
            };
            reader.readAsDataURL(updatedImageFile);
        } else {
            updateBookData(index, updatedTitle, updatedAuthor);
        }
    };
}

// Update Book Data
function updateBookData(index, title, author, image = null) {
    books[index].title = title;
    books[index].author = author;
    if (image) books[index].image = image;

    loadBooks();
    closeEditBookModal();
}

// Delete Book
function deleteBook(index) {
    books.splice(index, 1);
    loadBooks();
}

// Close Edit Modal
function closeEditBookModal() {
    document.getElementById("editBookModal").style.display = "none";
}

// Load Books on Page Load
document.addEventListener("DOMContentLoaded", loadBooks);
