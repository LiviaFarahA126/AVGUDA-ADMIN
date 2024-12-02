<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$database = "admin_db";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Menambah buku baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_book'])) {
    $bookName = $_POST['bookName'];
    $bookAuthor = $_POST['bookAuthor'];
    $category = $_POST['category'];  // Kategori dipilih langsung
    $imageName = null;

    // Upload gambar
    if (!empty($_FILES['bookImage']['name'])) {
        $targetDir = "uploads/";
        $imageName = time() . "_" . basename($_FILES['bookImage']['name']);
        $targetFilePath = $targetDir . $imageName;

        // Pindahkan file yang diupload
        if (!move_uploaded_file($_FILES['bookImage']['tmp_name'], $targetFilePath)) {
            echo "<script>alert('Upload gambar gagal. Periksa izin folder uploads.');</script>";
        }
    }

    // Simpan data buku ke database
    $sql = "INSERT INTO `Books` (nama_book, image_book, author_book, category) 
            VALUES ('$bookName', '$imageName', '$bookAuthor', '$category')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Buku berhasil ditambahkan!'); window.location.href='books.php';</script>";
    }
}

// Edit buku
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_book'])) {
    $bookId = $_POST['bookId'];
    $bookName = $_POST['bookName'];
    $bookAuthor = $_POST['bookAuthor'];
    $category = $_POST['category'];
    $imageName = $_POST['currentImage']; // Default image if no new image uploaded

    // Cek jika ada gambar baru yang diupload
    if (!empty($_FILES['bookImage']['name'])) {
        $targetDir = "uploads/";
        $imageName = time() . "_" . basename($_FILES['bookImage']['name']);
        $targetFilePath = $targetDir . $imageName;

        if (!move_uploaded_file($_FILES['bookImage']['tmp_name'], $targetFilePath)) {
            echo "<script>alert('Upload gambar gagal. Periksa izin folder uploads.');</script>";
        }
    }

    // Update data buku di database
    $sql = "UPDATE `Books` SET nama_book='$bookName', image_book='$imageName', author_book='$bookAuthor', category='$category' WHERE id_book=$bookId";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Buku berhasil diperbarui!'); window.location.href='books.php';</script>";
    }
}

// Menghapus buku
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $sql = "DELETE FROM `Books` WHERE id_book = $deleteId";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Buku berhasil dihapus!'); window.location.href='books.php';</script>";
    }
}

// Mengambil data buku
$sql = "SELECT * FROM `Books`";
$result = $conn->query($sql);

// Array kategori statis
$categories = ["Fiction", "Non-Fiction", "Science", "History", "Technology"];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <title>Books - Avguda</title>
</head>

<style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');

    :root{
    --color-primary: #6C9BCF;
    --color-danger: #FF0060;
    --color-success: #1B9C85;
    --color-warning: #F7D060;
    --color-white: #fff;
    --color-info-dark: #7d8da1;
    --color-dark: #363949;
    --color-light: rgba(132, 139, 200, 0.18);
    --color-dark-variant: #677483;
    --color-background: #f6f6f9;

    --card-border-radius: 2rem;
    --border-radius-1: 0.4rem;
    --border-radius-2: 1.2rem;

    --card-padding: 1.8rem;
    --padding-1: 1.2rem;

    --box-shadow: 0 2rem 3rem var(--color-light);
    }

    .dark-mode-variables{
    --color-background: #181a1e;
    --color-white: #202528;
    --color-dark: #edeffd;
    --color-dark-variant: #a3bdcc;
    --color-light: rgba(0, 0, 0, 0.4);
    --box-shadow: 0 2rem 3rem var(--color-light);
}

*{
    margin: 0;
    padding: 0;
    outline: 0;
    appearance: 0;
    border: 0;
    text-decoration: none;
    box-sizing: border-box;
}

html{
    font-size: 14px;
}
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            background-color: #f6f6f9;
        }

        aside {
            width: 250px;
            background: #fff;
            height: 100%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 1rem;
            box-sizing: border-box;
        }

        aside .logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        aside .logo img {
            max-width: 100px;
            margin-bottom: 1rem;
        }

        aside ul {
            list-style: none;
            padding: 0;
        }

        aside ul li {
            margin-bottom: 1rem;
        }

        aside ul li a {
            text-decoration: none;
            color: #363949;
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        aside ul li a.active {
            background: #6C9BCF;
            color: #fff;
        }

        aside ul li a:hover {
            background: #6C9BCF;
            color: #fff;
        }

        main {
            flex: 1;
            background: #fff;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 1rem;
            margin: 1rem;
            box-sizing: border-box;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        table th,
        table td {
            padding: 1rem;
            border: 1px solid #ddd;
            text-align: center;
        }

        table img {
            max-width: 50px;
            border-radius: 5px;
        }

        .add-group {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background-color: #1B9C85;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 1rem;
        }

        .add-group:hover {
            background: #127a66;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 400px;
            max-width: 90%;
        }

        .modal-content h2 {
            margin-bottom: 1rem;
            color: #6C9BCF;
        }

        .modal-content input,
        .modal-content textarea,
        .modal-content button {
            width: 100%;
            margin-bottom: 1rem;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .modal-content button {
            background: #6C9BCF;
            color: #fff;
            cursor: pointer;
        }

        .modal-content button:hover {
            background: #557aad;
        }

        .close-btn {
            background: #FF0060;
            color: #fff;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>

<body>
<aside>
        <div class="logo">
            <img src="images/logo.png" alt="Logo">
            <h2>Avguda<span style="color: #6C9BCF;">Books</span></h2>
        </div>
        <ul>
            <li><a href="dashboard.html">Dashboard</a></li>
            <li><a href="books.php" class="active">Books</a></li>
            <li><a href="groups.php">Groups</a></li>
            <li><a href="events.php">Events</a></li>
            <li><a href="users.html">Users</a></li>
            <li><a href="index.html">Analytics</a></li>
            <li><a href="reports.html">Reports</a></li>
            <li><a href="settings.html">Settings</a></li>
            <li><a href="#">Logout</a></li>
        </ul>
    </aside>

    <main>
        <h1>Books</h1>
        <h2>Manage Books</h2>
        <a href="#" class="add-group" onclick="showModal('addModal')">+ Add Book</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Image</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id_book']; ?></td>
                        <td><?= $row['nama_book']; ?></td>
                        <td><img src="uploads/<?= $row['image_book']; ?>" alt="Image" width="50"></td>
                        <td><?= $row['author_book']; ?></td>
                        <td><?= $row['category']; ?></td>
                        <td>
                            <!-- Tombol Edit -->
                            <button class="edit-btn" onclick="editBook(<?= htmlspecialchars(json_encode($row)) ?>)">Edit</button>
                            <!-- Tombol Delete -->
                            <a href="?delete_id=<?= $row['id_book']; ?>" class="delete-btn">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>

    <!-- Modal untuk Add Book -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <h2>Add New Book</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="text" name="bookName" placeholder="Book Name" required>
                <input type="text" name="bookAuthor" placeholder="Author Name" required>
                <select name="category" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category; ?>"><?= $category; ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="file" name="bookImage">
                <button type="submit" name="add_book">Add Book</button>
                <button type="button" class="close-btn" onclick="closeModal('addModal')">Close</button>
            </form>
        </div>
    </div>

    <!-- Modal untuk Edit Book -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2>Edit Book</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="bookId" id="editBookId">
                <input type="text" name="bookName" id="editBookName" placeholder="Book Name" required>
                <input type="text" name="bookAuthor" id="editBookAuthor" placeholder="Author Name" required>
                <input type="hidden" name="currentImage" id="currentImage">
                <select name="category" id="editCategory" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category; ?>"><?= $category; ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="file" name="bookImage">
                <button type="submit" name="edit_book">Save Changes</button>
                <button type="button" class="close-btn" onclick="closeModal('editModal')">Close</button>
            </form>
        </div>
    </div>

    <script>
        function showModal(id) {
            document.getElementById(id).style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        function editBook(book) {
            // Isi modal edit dengan data buku
            document.getElementById('editBookId').value = book.id_book;
            document.getElementById('editBookName').value = book.nama_book;
            document.getElementById('editBookAuthor').value = book.author_book;
            document.getElementById('editCategory').value = book.category;
            document.getElementById('currentImage').value = book.image_book;

            showModal('editModal');
        }
    </script>
</body>

</html>
