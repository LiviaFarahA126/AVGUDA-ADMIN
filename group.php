<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$database = "admin_db";

$conn = new mysqli($servername, $username, $password, $database);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Menyimpan data baru ke database
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_group'])) {
    $groupName = $_POST['groupName'];
    $description = $_POST['description'];
    $imageName = null;

    // Upload gambar
    if (!empty($_FILES['groupImage']['name'])) {
        $targetDir = "uploads/";
        $imageName = basename($_FILES['groupImage']['name']);
        $targetFilePath = $targetDir . $imageName;

        if (move_uploaded_file($_FILES['groupImage']['tmp_name'], $targetFilePath)) {
            // File berhasil diunggah
        } else {
            echo "<script>alert('Gagal mengunggah gambar');</script>";
        }
    }

    // Simpan ke database
    $sql = "INSERT INTO `Group` (nama_group, image_group, description) VALUES ('$groupName', '$imageName', '$description')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Group berhasil ditambahkan!');</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menghapus data dari database
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $sql = "DELETE FROM `Group` WHERE id_group = $deleteId";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Group berhasil dihapus!'); window.location.href='groups.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Mengambil semua data dari tabel Group
$sql = "SELECT * FROM `Group`";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <title>Groups - Avguda</title>
    <style>
        /* Import Font dan Variabel Warna */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');

        :root {
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

            --card-border-radius: 1.5rem;
            --padding-1: 1.2rem;

            --box-shadow: 0 2rem 3rem var(--color-light);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--color-background);
            margin: 0;
            padding: 0;
        }

        .container {
            display: grid;
            grid-template-columns: 240px auto;
            gap: 2rem;
            padding: 2rem;
        }

        /* Sidebar */
        aside {
            background-color: var(--color-white);
            border-radius: var(--card-border-radius);
            padding: var(--padding-1);
            height: 100vh;
            box-shadow: var(--box-shadow);
            position: sticky;
            top: 0;
        }

        aside a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--color-dark);
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            border-radius: var(--card-border-radius);
        }

        aside a span {
            font-size: 1.5rem;
            margin-right: 1rem;
        }

        aside a:hover {
            background-color: var(--color-primary);
            color: var(--color-white);
        }

        aside a.active {
            background-color: var(--color-primary);
            color: var(--color-white);
        }

        /* Main Content */
        main {
            background-color: var(--color-white);
            padding: var(--padding-1);
            border-radius: var(--card-border-radius);
            box-shadow: var(--box-shadow);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        table thead {
            background-color: var(--color-primary);
            color: var(--color-white);
        }

        table th,
        table td {
            padding: 1rem;
            text-align: center;
        }

        table tbody tr:hover {
            background-color: var(--color-light);
        }

        table tbody img {
            width: 50px;
            height: auto;
            border-radius: var(--card-border-radius);
        }

        .add-group {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background-color: var(--color-success);
            color: var(--color-white);
            border-radius: var(--card-border-radius);
            text-align: center;
            font-size: 1rem;
            margin-bottom: 1rem;
            cursor: pointer;
            text-decoration: none;
        }

        .add-group:hover {
            background-color: var(--color-dark);
        }

        button.edit-btn,
        button.delete-btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            border: none;
            color: var(--color-white);
            cursor: pointer;
        }

        button.edit-btn {
            background-color: var(--color-warning);
        }

        button.edit-btn:hover {
            background-color: #d4a017;
        }

        button.delete-btn {
            background-color: var(--color-danger);
        }

        button.delete-btn:hover {
            background-color: #c0392b;
        }

        form input,
        form textarea,
        form button {
            width: 100%;
            padding: 1rem;
            margin: 0.5rem 0;
            border-radius: var(--card-border-radius);
            border: 1px solid #ccc;
            font-size: 1rem;
        }

        form button {
            background-color: var(--color-primary);
            color: var(--color-white);
        }

        form button:hover {
            background-color: #557aad;
        }

        @media screen and (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
            }

            aside {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <aside>
            <h2>Avguda<span class="primary">Books</span></h2>
            <a href="#" class="active"><span class="material-icons-sharp">group</span>Groups</a>
            <a href="#"><span class="material-icons-sharp">dashboard</span>Dashboard</a>
            <a href="#"><span class="material-icons-sharp">event</span>Events</a>
            <a href="#"><span class="material-icons-sharp">logout</span>Logout</a>
        </aside>

        <!-- Main Content -->
        <main>
            <h1>Groups</h1>
            <a href="#" class="add-group" onclick="document.getElementById('addForm').style.display='block'">+ Add Group</a>

            <!-- Form Tambah -->
            <form id="addForm" method="POST" enctype="multipart/form-data" style="display: none;">
                <input type="text" name="groupName" placeholder="Group Name" required>
                <textarea name="description" placeholder="Description" rows="4" required></textarea>
                <input type="file" name="groupImage" accept="image/*">
                <button type="submit" name="add_group">Add Group</button>
            </form>

            <!-- Tabel Data -->
            <table>
                <thead>
                    <tr>
                        <th>Group ID</th>
                        <th>Group Name</th>
                        <th>Image</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['id_group'] . "</td>";
                            echo "<td>" . $row['nama_group'] . "</td>";
                            echo "<td>";
                            if ($row['image_group']) {
                                echo "<img src='uploads/" . $row['image_group'] . "' alt='Group Image'>";
                            } else {
                                echo "No Image";
                            }
                            echo "</td>";
                            echo "<td>" . $row['description'] . "</td>";
                            echo "<td>
                                    <button class='edit-btn'>Edit</button>
                                    <a href='?delete_id=" . $row['id_group'] . "' class='delete-btn'>Delete</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No groups found</td></tr>";
                    } ?>
                </tbody>
            </table>
        </main>
    </div>
</body>

</html>
