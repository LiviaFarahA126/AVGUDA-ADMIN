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
    <link rel="stylesheet" href="groups.css">
    <title>Groups - Avguda</title>
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