<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$database = "admin_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Ambil data grup dari database
$sql = "SELECT id_group, nama_group, image_group, description FROM `Group`";
$stmt = $pdo->prepare($sql);
$stmt->execute();

// Fetch semua grup
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil data event dari database
$sql_event = "SELECT id_event, nama_event, image_event, description FROM `Event`";
$stmt_event = $pdo->prepare($sql_event);
$stmt_event->execute();

// Fetch semua event
$events = $stmt_event->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Avguda: Book Lovers Community</title>
    <link href="https://fonts.googleapis.com/css2?family=Shrikhand&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f6f4f5;
            color: #333;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #932149;
            color: white;
            padding: 20px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-content {
            display: flex;
            align-items: center;
        }

        .logo img {
            max-width: 100px;
            margin-right: 20px;
        }

        .site-info h1 {
            margin: 0;
            font-size: 2rem;
            font-family: 'Shrikhand', cursive;
        }
        nav ul {
    list-style: none;
    display: flex;
    justify-content: center;  /* Tengah-kan navigasi */
    margin: 0;
    padding: 0;
    gap: 20px;  /* Tambah jarak antar item */
}
        nav ul li a {
    color: white;
    text-decoration: none;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
    padding: 10px 20px;
    background-color: #FFD700;
    border-radius: 25px;
    position: relative;
    transition: background-color 0.3s ease, color 0.3s ease;
}

nav ul li a:hover {
    background-color: #bf577c;
    color: white;
}

nav ul li a::before {
    content: "";
    position: absolute;
    width: 0;
    height: 3px;
    bottom: 0;
    left: 0;
    background-color: white;
    transition: 0.3s ease;
}

nav ul li a:hover::before {
    width: 100%;
}
/* Popup styling */
.popup-container {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 50%;
    background-color: white;
    box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.5);
    z-index: 10;
    padding: 20px;
    border-radius: 10px;
    animation: popup-appear 0.5s ease-in-out;
}

@keyframes popup-appear {
    0% {
        transform: translate(-50%, -50%) scale(0.5);
        opacity: 0;
    }
    100% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }
}

.popup-container img {
    max-width: 100%;
    height: auto;
    border-radius: 10px;
}

.popup-close {
    position: absolute;
    top: 10px;
    right: 10px;
    cursor: pointer;
    font-size: 1.5rem;
}


        section {
            padding: 60px 20px;
        }

        section h2 {
            color: #932149;
            font-size: 2.5rem;
            margin-bottom: 20px;
            text-align: center;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        .groups-list, .events-list {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }

        .group-item, .event-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .group-item img, .event-item img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-right: 20px;
            object-fit: cover;
        }

        .group-info, .event-info {
            flex-grow: 1;
        }

        .group-info h3, .event-info h3 {
            margin: 0;
            font-size: 1.5rem;
            color: #333;
        }

        .group-info p, .event-info p {
            margin: 5px 0;
            font-size: 0.9rem;
            color: #777;
        }

        .join-button, .event-button {
            background-color: #FFD700;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1rem;
        }

        .join-button:hover, .event-button:hover {
            background-color: #FFC300;
        }

        .joined-button {
            background-color: #4CAF50; /* Warna hijau untuk tombol "Joined" */
            color: white;
            cursor: pointer;
        }

        .product-slider-wrapper {
            position: relative;
            overflow: hidden;
        }

        .product-slider {
            display: flex;
            overflow-x: scroll;
            scroll-behavior: smooth;
            gap: 10px;
        }

        .product-slider::-webkit-scrollbar {
            display: none;
        }

        .product-slide {
            min-width: 250px;
            margin: 10px;
            padding: 15px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .product-slide img {
            width: 100%;
            border-radius: 5px;
        }

        .arrow {
            font-size: 2rem;
            cursor: pointer;
            margin: 10px;
            background-color: #FFD700;
            color: white;
            border: none;
            border-radius: 50%;
            height: 50px;
            width: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
        }

        .arrow-left {
            left: 0;
        }

        .arrow-right {
            right: 0;
        }

        footer {
            background-color: #232f3e;
            color: white;
            padding: 40px 20px;
        }

        footer .container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }

        footer .footer-column h3 {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: #ffffff;
        }

        footer .footer-column p,
        footer .footer-column ul li {
            font-size: 0.9rem;
            margin: 5px 0;
            color: #dcdcdc;
        }

        footer ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        footer a {
            color: #dcdcdc;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }

        footer .footer-column {
            flex: 1;
            min-width: 250px;
        }

        footer .footer-bottom {
            text-align: center;
            margin-top: 40px;
            color: #dcdcdc;
        }
            /* Styling untuk bagian event */
        .events-list {
            display: flex;
            overflow-x: auto;
            gap: 20px;
            padding-bottom: 20px;
        }

        .event-item {
            min-width: 300px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
            text-align: center;
            padding: 20px;
        }

        .event-item:hover {
            transform: scale(1.05);
        }

        .event-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }

        .event-info h3 {
            font-size: 1.2rem;
            color: #333;
            margin-top: 10px;
        }

        .event-info p {
            color: #777;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .event-button {
            background-color: #FFD700;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1rem;
            margin-top: 15px;
        }

        .event-button:hover {
            background-color: #FFC300;
        }

        /* Untuk membuat slider lebih baik */
        .product-slider-wrapper {
            position: relative;
            max-width: 100%;
            overflow: hidden;
        }

        .product-slider {
            display: flex;
            gap: 20px;
            transition: transform 0.5s ease;
        }

        .product-slide {
            min-width: 300px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 20px;
        }

        .product-slide img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }

        .arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: #FFD700;
            color: white;
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.5rem;
            z-index: 1;
        }

        .arrow-left {
            left: 10px;
        }

        .arrow-right {
            right: 10px;
        }

    </style>
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
            <li><a href="0 edit home.html">Home</a></li>
            <li><a href="5 group avguda.html">Community</a></li>
            <li><a href="0 edit home.html#browse" id="browse-section">Browse Books</a></li>
            <li><a href="2 avguda log in.html" id="login-nav">Login</a></li>
        </ul>
    </nav>
</header>

<section id="groups">
    <div class="container">
        <h2>Explore Groups</h2>
        <div class="groups-list">
            <?php
            // Looping melalui semua grup dan menampilkan datanya
            foreach ($groups as $group) {
                // Menggunakan path lengkap untuk gambar (asumsi gambar disimpan di folder 'uploads')
                $imagePath = !empty($group['image_group']) ? 'uploads/' . $group['image_group'] : 'default-image.jpg'; 
                echo '<div class="group-item">';
                echo '<img src="' . $imagePath . '" alt="' . htmlspecialchars($group['nama_group']) . '">';
                echo '<div class="group-info">';
                echo '<h3>' . htmlspecialchars($group['nama_group']) . '</h3>';
                echo '<p>' . htmlspecialchars($group['description']) . '</p>';
                echo '<button class="join-button" data-title="' . htmlspecialchars($group['nama_group']) . '" data-description="' . htmlspecialchars($group['description']) . '" data-image="' . htmlspecialchars($group['image_group']) . '" data-link="#">Join Group</button>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</section>
<section id="events">
    <div class="container">
        <h2>Explore Events</h2>
        <div class="product-slider-wrapper">
            <div class="product-slider">
                <?php
                // Looping melalui semua event dan menampilkan datanya
                foreach ($events as $event) {
                    $imagePathEvent = !empty($event['image_event']) ? 'uploads/' . $event['image_event'] : 'default-image.jpg';
                    echo '<div class="product-slide">';
                    echo '<img src="' . $imagePathEvent . '" alt="' . htmlspecialchars($event['nama_event']) . '">';
                    echo '<div class="event-info">';
                    echo '<h3>' . htmlspecialchars($event['nama_event']) . '</h3>';
                    echo '<p>' . htmlspecialchars($event['description']) . '</p>';
                    echo '<button class="event-button">Join Event</button>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
            <!-- Tombol navigasi slider -->
            <button class="arrow arrow-left">&#10094;</button>
            <button class="arrow arrow-right">&#10095;</button>
        </div>
    </div>
</section>


<footer>
    <div class="container">
        <div class="footer-column">
            <h3>BINK. Publishers</h3>
            <p>500 Terry Francine St.</p>
            <p>San Francisco, CA 94158</p>
            <p>123-456-7890</p>
            <p>info@my-domain.com</p>
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

    <div class="footer-bottom">
        <p>&copy; 2024 Avguda. All rights reserved. Powered by Avguda.</p>
    </div>

    <script>
    // Script untuk slider event
        let currentIndex = 0;

        const slider = document.querySelector('.product-slider');
        const totalSlides = document.querySelectorAll('.product-slide').length;

        const prevButton = document.querySelector('.arrow-left');
        const nextButton = document.querySelector('.arrow-right');

        function updateSlider() {
            const slideWidth = document.querySelector('.product-slide').offsetWidth + 20; // Adding gap to width
            slider.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
        }

        prevButton.addEventListener('click', () => {
            if (currentIndex > 0) {
                currentIndex--;
                updateSlider();
            }
        });

        nextButton.addEventListener('click', () => {
            if (currentIndex < totalSlides - 1) {
                currentIndex++;
                updateSlider();
            }
        });

    </script>

</footer>

</body>
</html>
