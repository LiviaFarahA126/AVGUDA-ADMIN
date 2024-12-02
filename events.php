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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_event'])) {
    $eventName = $_POST['eventName'];
    $description = $_POST['description'];
    $imageName = null;

    // Upload gambar
    if (!empty($_FILES['eventImage']['name'])) {
        $targetDir = "uploads/";
        $imageName = time() . "_" . basename($_FILES['eventImage']['name']);
        $targetFilePath = $targetDir . $imageName;

        // Pindahkan file yang diupload
        if (!move_uploaded_file($_FILES['eventImage']['tmp_name'], $targetFilePath)) {
            echo "<script>alert('Upload gambar gagal. Periksa izin folder uploads.');</script>";
        }
    }

    $sql = "INSERT INTO `Event` (nama_event, image_event, description) VALUES ('$eventName', '$imageName', '$description')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Event berhasil ditambahkan!'); window.location.href='events.php';</script>";
    }
}

// Mengedit grup
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_event'])) {
    $id = $_POST['eventId'];
    $eventName = $_POST['eventName'];
    $description = $_POST['description'];
    $imageName = $_POST['currentImage'];

    // Jika ada gambar baru yang diupload
    if (!empty($_FILES['eventImage']['name'])) {
        $targetDir = "uploads/";
        $imageName = time() . "_" . basename($_FILES['eventImage']['name']);
        $targetFilePath = $targetDir . $imageName;

        if (!move_uploaded_file($_FILES['eventImage']['tmp_name'], $targetFilePath)) {
            echo "<script>alert('Upload gambar gagal. Periksa izin folder uploads.');</script>";
        }
    }

    $sql = "UPDATE `Event` SET nama_event='$eventName', image_event='$imageName', description='$description' WHERE id_event=$id";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Event berhasil diperbarui!'); window.location.href='events.php';</script>";
    }
}

// Menghapus grup
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $sql = "DELETE FROM `Event` WHERE id_event = $deleteId";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Event berhasil dihapus!'); window.location.href='event.php';</script>";
    }
}

// Mengambil data event
$sql = "SELECT * FROM `Event`";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <title>Event - Avguda</title>
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

        .add-event {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background-color: #1B9C85;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 1rem;
        }

        .add-event:hover {
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
            <li><a href="groups.php">Groups</a></li>
            <li><a href="events.php" class="active">Events</a></li>
            <li><a href="users.html">Users</a></li>
            <li><a href="index.html">Analytics</a></li>
            <li><a href="reports.html">Reports</a></li>
            <li><a href="settings.html">Settings</a></li>
            <li><a href="#">Logout</a></li>
        </ul>
    </aside>
    <main>
        <h1>Event</h1>
        <h2>Manage Event</h2>
        <a href="#" class="add-event" onclick="showModal('addModal')">+ Add Event</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Event Name</th>
                    <th>Image</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id_event'] ?></td>
                        <td><?= $row['nama_event'] ?></td>
                        <td>
                            <?php if ($row['image_event']): ?>
                                <img src="uploads/<?= $row['image_event'] ?>" alt="Event Image">
                            <?php else: ?>
                                No Image
                            <?php endif; ?>
                        </td>
                        <td><?= $row['description'] ?></td>
                        <td>
                            <button class="edit-btn" onclick="editEvent(<?= htmlspecialchars(json_encode($row)) ?>)">Edit</button>
                            <a href="?delete_id=<?= $row['id_event'] ?>" class="delete-btn">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>

    <div id="addModal" class="modal">
        <div class="modal-content">
            <h2>Add Event</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="eventName" placeholder="Event Name" required>
                <textarea name="description" placeholder="Description" required></textarea>
                <input type="file" name="eventImage" accept="image/*">
                <button type="submit" name="add_event">Add Group</button>
                <button type="button" class="close-btn" onclick="closeModal('addModal')">Close</button>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2>Edit Event</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="eventId" id="editEventId">
                <input type="text" name="eventName" id="editEventName" required>
                <textarea name="description" id="editDescription" required></textarea>
                <input type="file" name="eventImage" accept="image/*">
                <input type="hidden" name="currentImage" id="currentImage">
                <button type="submit" name="edit_event">Save Changes</button>
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

        function editEvent(event) {
            document.getElementById('editEventId').value = event.id_event;
            document.getElementById('editEventName').value = event.nama_event;
            document.getElementById('editDescription').value = event.description;
            document.getElementById('currentImage').value = event.image_event;
            showModal('editModal');
        }
    </script>
</body>

</html>