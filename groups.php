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

// Menambah grup baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_group'])) {
    $groupName = $_POST['groupName'];
    $description = $_POST['description'];
    $imageName = null;

    // Upload gambar
    if (!empty($_FILES['groupImage']['name'])) {
        $targetDir = "uploads/";
        $imageName = time() . "_" . basename($_FILES['groupImage']['name']);
        $targetFilePath = $targetDir . $imageName;

        // Pindahkan file yang diupload
        if (!move_uploaded_file($_FILES['groupImage']['tmp_name'], $targetFilePath)) {
            echo "<script>alert('Upload gambar gagal. Periksa izin folder uploads.');</script>";
        }
    }

    $sql = "INSERT INTO `Group` (nama_group, image_group, description) VALUES ('$groupName', '$imageName', '$description')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Group berhasil ditambahkan!'); window.location.href='groups.php';</script>";
    }
}

// Mengedit grup
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_group'])) {
    $id = $_POST['groupId'];
    $groupName = $_POST['groupName'];
    $description = $_POST['description'];
    $imageName = $_POST['currentImage'];

    // Jika ada gambar baru yang diupload
    if (!empty($_FILES['groupImage']['name'])) {
        $targetDir = "uploads/";
        $imageName = time() . "_" . basename($_FILES['groupImage']['name']);
        $targetFilePath = $targetDir . $imageName;

        if (!move_uploaded_file($_FILES['groupImage']['tmp_name'], $targetFilePath)) {
            echo "<script>alert('Upload gambar gagal. Periksa izin folder uploads.');</script>";
        }
    }

    $sql = "UPDATE `Group` SET nama_group='$groupName', image_group='$imageName', description='$description' WHERE id_group=$id";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Group berhasil diperbarui!'); window.location.href='groups.php';</script>";
    }
}

// Menghapus grup
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $sql = "DELETE FROM `Group` WHERE id_group = $deleteId";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Group berhasil dihapus!'); window.location.href='groups.php';</script>";
    }
}

// Mengambil data grup
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
</head>

<body>
    <aside>
        <div class="logo">
            <img src="images/logo.png" alt="Logo">
            <h2>Avguda<span style="color: #6C9BCF;">Books</span></h2>
        </div>
        <ul>
        <li>
            <li><a href="dashboard.html">Dashboard</a></li>
            <li><a href="books.php">Books</a></li>
            <li><a href="groups.php" class="active">Groups</a></li>
            <li><a href="events.php">Events</a></li>
            <li><a href="users.html">Users</a></li>
            <li><a href="index.html">Analytics</a></li>
            <li><a href="reports.html">Reports</a></li>
            <li><a href="settings.html">Settings</a></li>
            <li><a href="#">Logout</a></li>
        </ul>
    </aside>
    <main>
        <h1>Groups</h1>
        <h2>Manage Groups</h2>
        <a href="#" class="add-group" onclick="showModal('addModal')">+ Add Group</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Group Name</th>
                    <th>Image</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id_group'] ?></td>
                        <td><?= $row['nama_group'] ?></td>
                        <td>
                            <?php if ($row['image_group']): ?>
                                <img src="uploads/<?= $row['image_group'] ?>" alt="Group Image">
                            <?php else: ?>
                                No Image
                            <?php endif; ?>
                        </td>
                        <td><?= $row['description'] ?></td>
                        <td>
                            <button class="edit-btn" onclick="editGroup(<?= htmlspecialchars(json_encode($row)) ?>)">Edit</button>
                            <a href="?delete_id=<?= $row['id_group'] ?>" class="delete-btn">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>

    <div id="addModal" class="modal">
        <div class="modal-content">
            <h2>Add Group</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="groupName" placeholder="Group Name" required>
                <textarea name="description" placeholder="Description" required></textarea>
                <input type="file" name="groupImage" accept="image/*">
                <button type="submit" name="add_group">Add Group</button>
                <button type="button" class="close-btn" onclick="closeModal('addModal')">Close</button>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2>Edit Group</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="groupId" id="editGroupId">
                <input type="text" name="groupName" id="editGroupName" required>
                <textarea name="description" id="editDescription" required></textarea>
                <input type="file" name="groupImage" accept="image/*">
                <input type="hidden" name="currentImage" id="currentImage">
                <button type="submit" name="edit_group">Save Changes</button>
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

        function editGroup(group) {
            document.getElementById('editGroupId').value = group.id_group;
            document.getElementById('editGroupName').value = group.nama_group;
            document.getElementById('editDescription').value = group.description;
            document.getElementById('currentImage').value = group.image_group;
            showModal('editModal');
        }
    </script>
</body>

</html>