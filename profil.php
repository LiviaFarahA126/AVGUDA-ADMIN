<?php
session_start();

// Mengecek apakah user sudah login
$isLoggedIn = isset($_SESSION['loggedInUser']); 

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "admin_db");

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil data pencarian dari query string
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Query untuk mengambil data buku berdasarkan pencarian dan kategori
$sql = "SELECT tittle, image, author, category FROM books WHERE tittle LIKE ? AND category LIKE ?";
$stmt = $conn->prepare($sql);
$searchParam = "%$searchQuery%";
$categoryParam = "%$category%";
$stmt->bind_param("ss", $searchParam, $categoryParam);
$stmt->execute();
$result = $stmt->get_result();

$books = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Avguda: Book Lovers Community</title>
    <link rel="stylesheet" href="avguda_style.css">
    <!-- Link to Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Shrikhand&display=swap" rel="stylesheet">
</head>

<body>
    <header>
        <div class="header-content">
            <div class="logo">
                <a href="#"><img src="Avguda logo.png" alt="Avguda Logo"></a>
            </div>
            <div class="site-info">
                <h1>Avguda</h1>
                <p>Your Book Lovers Community</p>
            </div>
        </div>
        <nav>
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="5_group_avguda.html">Community</a></li>
                <li><a href="#browse">Browse Books</a></li>
                <?php if ($isLoggedIn): ?>
                    <li><a href="profil.php">Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="2_avguda_log_in.html" id="login-nav">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <section id="home" class="hero-container">
        <div class="hero-text">
            <h2>Welcome to Avguda!</h2>
            <p>Empowering book lovers to connect, share, and find books.</p>
            <a href="#browse" class="cta-button">Browse Books</a>
        </div>
        <div class="hero-image">
            <img src="avguda.1.jpg" alt="Reading Books Image">
            <img src="avguda.5.jpg" alt="Reading Books Image">
        </div>
    </section>

    <div id="popup" class="popup-container">
        <span class="popup-close">&times;</span>
        <img id="popup-image" src="" alt="Book Image">
    </div>
    
    <div class="book-item">
    <img src="data:image/jpeg;base64,<?= base64_encode($book['image']) ?>" alt="Book Cover" />
    <h4 style="font-size: 1.2em; color: #333;"><?= htmlspecialchars($book['nama_book']) ?></h4>
    <p style="font-size: 1em; color: #555;">by <?= htmlspecialchars($book['author_book']) ?></p>
</div>

    <section id="recommendation" class="recommendation-container">
        <div style="text-align: center; margin-bottom: 20px;">
            <h2>This Month's</h2>                 
            <p>RECOMMENDED BOOKS</p>
        </div>
        <div class="recommendation-container" id="recommendation-container">
            <!-- Recommended books will be dynamically added here -->
        </div>
    </section>

    <section id="browse" class="browse-section">
        <div>
            <h2>Browse Books</h2>
            <p>Search and explore books by title, author, or genre.</p>
            <form id="search-form" method="get">
            <input type="text" id="search-input" name="search" placeholder="Search by title, author, or keyword" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            <select id="category-select" name="category">
                <option value="">Select Category</option>
                <option value="fiction" <?= (isset($_GET['category']) && $_GET['category'] == 'fiction') ? 'selected' : '' ?>>Fiction</option>
                <option value="non-fiction" <?= (isset($_GET['category']) && $_GET['category'] == 'non-fiction') ? 'selected' : '' ?>>Non-fiction</option>
                <option value="romance" <?= (isset($_GET['category']) && $_GET['category'] == 'romance') ? 'selected' : '' ?>>Romance</option>
                <option value="horror" <?= (isset($_GET['category']) && $_GET['category'] == 'horror') ? 'selected' : '' ?>>Horror</option>
                <option value="action" <?= (isset($_GET['category']) && $_GET['category'] == 'action') ? 'selected' : '' ?>>Action</option>
            </select>
            <input type="submit" value="Search">
        </form>
            <div class="book-list" id="book-list">
                <?php
                if (isset($_GET['search']) || isset($_GET['category'])) {
                    $searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
                    $category = isset($_GET['category']) ? $_GET['category'] : '';
                    $sql = "SELECT nama_book, image_book, author_book, category FROM Books WHERE nama_book LIKE '%$searchQuery%' AND category LIKE '%$category%'";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="book-item">';
                            echo '<img src="data:image/jpeg;base64,'.base64_encode($row['imag_book']).'" alt="Book Cover"/>';
                            echo '<h4>' . htmlspecialchars($row['nama_book']) . '</h4>';
                            echo '<p>by ' . htmlspecialchars($row['author_book']) . '</p>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No books found matching your search.</p>';
                    }
                } else {
                    foreach ($books as $book) {
                        echo '<div class="book-item">';
                        echo '<img src="data:image/jpeg;base64,'.base64_encode($book['image']).'" alt="Book Cover"/>';
                        echo '<h4>' . htmlspecialchars($book['nama_book']) . '</h4>';
                        echo '<p>by ' . htmlspecialchars($book['author_book']) . '</p>';
                        echo '</div>';
                    }
                }
                ?>
            </div>

            <div class="slider-navigation">
                <button onclick="prevSlide()">&lt; Previous</button>
                <button onclick="nextSlide()">Next &gt;</button>
            </div>
        </div>
        <div class="book-list" id="book-list">
    <?php
    if (isset($_GET['search']) || isset($_GET['category'])) {
        // Menampilkan buku berdasarkan query pencarian
        if (count($books) > 0) {
            foreach ($books as $book) {
                echo '<div class="book-item">';
                echo '<img src="data:image/jpeg;base64,'.base64_encode($book['image_book']).'" alt="Book Cover"/>';
                echo '<h4>' . htmlspecialchars($book['nama_book']) . '</h4>';
                echo '<p>by ' . htmlspecialchars($book['author_book']) . '</p>';
                echo '</div>';
            }
        } else {
            echo '<p>No books found matching your search.</p>';
        }
    } else {
        // Menampilkan semua buku
        foreach ($books as $book) {
            echo '<div class="book-item">';
            echo '<img src="data:image/jpeg;base64,'.base64_encode($book['image_book']).'" alt="Book Cover"/>';
            echo '<h4>' . htmlspecialchars($book['nama_book']) . '</h4>';
            echo '<p>by ' . htmlspecialchars($book['author_book']) . '</p>';
            echo '</div>';
        }
    }
    ?>
</div>

    </section>

    <footer style="background-color: #232f3e; color: #ffffff; padding: 40px 20px;">
        <div class="container">
            <div class="footer-column">
                <h3>BINK. Publishers</h3>
                <p>500 Terry Francine St.</p>
                <p>San Francisco, CA 94158</p>
                <p>123-456-7890</p>
                <p>info@my-domain.com</p>
            </div>
            <div class="footer-column">
                <h3>Shop</h3>
                <ul>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Shipping & Returns</a></li>
                    <li><a href="#">Store Policy</a></li>
                    <li><a href="#">Payment Methods</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Socials</h3>
                <ul>
                    <li><a href="#">Facebook</a></li>
                    <li><a href="#">Twitter</a></li>
                    <li><a href="#">Instagram</a></li>
                    <li><a href="#">Pinterest</a></li>
                </ul>
            </div>
        </div>
        <div style="text-align: center;">
            <p>&copy; 2024 Avguda. All rights reserved.</p>
        </div>
    </footer>

    <script>
        const books = [
           
        ];

        function displayRecommendedBooks() {
            const recommendationContainer = document.querySelector('.recommendation-container');
            const recommendedBooks = books.slice(0, 5);
            recommendationContainer.innerHTML = '';

            recommendedBooks.forEach(book => {
                const bookItem = 
                    <div class="book-item" onclick="goToReview('${book.title}', '${book.image}')">
                        <img src="${book.image}" alt="${book.title}">
                        <p>${book.title}</p>
                        <p>Rate and Review</p>
                    </div>
                ;
                recommendationContainer.innerHTML += bookItem;
            });
        }

        window.onload = function() {
            const loggedInUser = localStorage.getItem("loggedInUser");

            if (loggedInUser) {
                const loginNav = document.getElementById("login-nav");
                loginNav.innerText = "Logout";
                loginNav.href = "#";
                loginNav.onclick = function() {
                    logoutUser();
                };

                const navBar = document.querySelector("nav ul");
                const profileNavItem = document.createElement("li");
                profileNavItem.innerHTML = <a href="profil.php">Profile</a>;
                navBar.appendChild(profileNavItem);
            }
            displayRecommendedBooks();
            displayBooks(books, 0);
        };

        function logoutUser() {
            localStorage.removeItem("loggedInUser");
            window.location.reload();
        }

        function goToReview(title, image) {
            localStorage.setItem("selectedBookTitle", title);
            localStorage.setItem("selectedBookImage", image);
            window.location.href = "7 review avguda.html";
        }

        let currentIndex = 0;
        const booksPerPage = 5;

        function displayBooks(bookList, startIndex) {
            const bookListContainer = document.getElementById("book-list");
            bookListContainer.innerHTML = "";
            const endIndex = startIndex + booksPerPage;

            bookList.slice(startIndex, endIndex).forEach(book => {
                const bookItem = 
                    <div class="book-item" onclick="goToReview('${book.title}', '${book.image}')">
                        <img src="${book.image}" alt="${book.title}">
                        <h4>${book.title}</h4>
                        <p>by ${book.author}</p>
                    </div>;
                bookListContainer.innerHTML += bookItem;
            });
        }

        function nextSlide() {
            if (currentIndex + booksPerPage < books.length) {
                currentIndex += booksPerPage;
            } else {
                currentIndex = 0;
            }
            displayBooks(books, currentIndex);
        }

        function prevSlide() {
            if (currentIndex - booksPerPage >= 0) {
                currentIndex -= booksPerPage;
            } else {
                currentIndex = books.length - booksPerPage;
            }
            displayBooks(books, currentIndex);
        }

        document.getElementById("search-form").addEventListener("submit", function(event) {
            event.preventDefault();
            const searchQuery = document.getElementById("search-input").value.toLowerCase();
            const selectedCategory = document.getElementById("category-select").value.toLowerCase();

            const filteredBooks = books.filter(book => {
                const matchesCategory = selectedCategory === "" || book.category === selectedCategory;
                const matchesQuery = book.title.toLowerCase().includes(searchQuery) || book.author.toLowerCase().includes(searchQuery);
                return matchesCategory && matchesQuery;
            });

            displayBooks(filteredBooks, 0);
        });

        function filterBooksByCategory(category) {
            const filteredBooks = books.filter(book => book.category === category);
            displayBooks(filteredBooks, 0);
        }

        displayBooks(books, 0);
    </script>
</body>
</html>