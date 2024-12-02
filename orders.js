const Books = [
    { bookID: 'BK001', title: 'The Catcher in the Rye', author: 'J.D. Salinger' },
    { bookID: 'BK002', title: '1984', author: 'George Orwell' },
    { bookID: 'BK003', title: 'To Kill a Mockingbird', author: 'Harper Lee' },
];

function loadRecentlyAddedBooks() {
    const tableBody = document.querySelector('.recent-orders tbody');
    tableBody.innerHTML = '';

    Books.forEach((book) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${book.bookID}</td>
            <td>${book.title}</td>
            <td>${book.author}</td>
            <td><button class="details-btn" onclick="viewDetails('${book.bookID}')">Details</button></td>
        `;
        tableBody.appendChild(row);
    });
}

function viewDetails(bookID) {
    window.location.href = `books.html?bookID=${bookID}`;
}

document.addEventListener('DOMContentLoaded', loadRecentlyAddedBooks);
