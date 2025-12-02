<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page Makanan saya</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="icon" href="/images/logoooo-removebg-preview.png" type="image/x-icon" />
</head>

<body>
    <header>
        <div class="navbar">
            <div class="logo">&#9679; FoodRecipe</div>
            <nav>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#menu">Menu</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </nav>
            <div class="contact">
                <a href="authentication/login.php">Login</a>
            </div>
        </div>
    </header>

    <section id="home" class="hero">
        <div class="overlay"></div>
        <div class="hero-content">
            <div class="welcome">Welcome Foodieee brayy ❤️</div>
            <h1>Welcome to Food Heaven Where <br> Flavor Meets Excellence!</h1>
            <a href="#menu" class="menu-btn">View Menu main &#10084;</a>
        </div>
    </section>


    <section id="menu" class="menu-section">
        <div class="flex-section">
            <div class="menu-text">
                <h2>Beragam hidangan untuk <br> memuaskan berbagai selera</h2>
                <p>
                    Kami bangga menyajikan hidangan-hidangan tradisional yang telah diwariskan dari generasi ke
                    generasi.
                    Setiap hidangan dipersiapkan dengan cinta dan keahlian, menggunakan bahan-bahan segar dan lokal yang
                    terbaik.
                    Menu kami meliputi berbagai masakan tradisional Indonesia, seperti Nasi Goreng, Sate, dan Gado-gado,
                    serta makanan khas daerah
                    jarang Anda temui seperti Ayam Tangkap dan Gurame Sari Honje.
                </p>
            </div>
            <div class="image-menu">

            </div>
        </div>

        <div class="menu-grid">
            <div class="menu-item">
                <img src="https://img.freepik.com/free-photo/chicken-curry-bowl-with-lemongrass-kaffir-lime-leaves-tomatoes-lemon-garlic_1150-25739.jpg?t=st=1742659449~exp=1742663049~hmac=44b063536b43464743bbb5d6b7a8924bf3bdd3692d09945e8d207afad69fdbf9&w=1380"
                    alt="Makanan 2">
            </div>
            <div class="menu-item">
                <img src="https://img.freepik.com/free-photo/traditional-nasi-lemak-meal_23-2149056868.jpg?t=st=1742659488~exp=1742663088~hmac=f630837ffc3b05565bdf5f1648eaf894751481086d42bcd83a008f0886db42ce&w=1380"
                    alt="Makanan 3">
            </div>
            <div class="menu-item">
                <img src="https://img.freepik.com/free-photo/traditional-nasi-lemak-meal-assortment_23-2149056811.jpg?t=st=1742659507~exp=1742663107~hmac=22bda483f5ebb615c9f1041d901de0e57aec38f9262dd05a3a4983c00b1b42d0&w=1380"
                    alt="Makanan 4">
            </div>
        </div>

        <div class="menu-button">
            <a href="menu.php">Lihat Menu Makanan</a>
        </div>
    </section>

    <!-- About Us -->
    <section class="about" id="about">
        <div class="container">
            <div class="image-section">
            </div>
            <div class="text-section">
                <h1>FoodRecipe</h1>
                <p>Welcome to <strong>FoodRecipe</strong>, the best place to discover delicious recipes from all over
                    indonesia! We believe that cooking is an art that brings joy.</p>
                <p>We provide a variety of recipes, from traditional dishes to modern creations, that are easy for
                    anyone to follow. All our recipes are tested so you can enjoy the best dishes at home.</p>

            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="footer">
        <div class="footer-container">
            <h2 class="footer-title">FoodRecipe</h2>
            <p class="footer-description">Discover the best recipes for every dish! 🍽️ Don't forget to follow us on:
                📍 Instagram | Facebook | Twitter</p>
            <nav class="footer-nav">
                <a href="#">Home</a>
                <a href="#">Menu</a>
                <a href="#">About Us</a>
                <a href="#">Contact</a>
            </nav>
        </div>
        <div class="footer-bottom">
            <div class="footer-logo">&#x1F9C1;</div>
            <p>Copyright &copy; 2023</p>
            <div class="footer-social">
                <a href="#"><img src="https://cdn-icons-png.flaticon.com/512/4922/4922972.png" alt=""></a>
                <a href="#"><img src="https://pixsector.com/cache/f8dbbdb2/avb37443a9d16e06eef66.png" alt=""></a>
                <a href="#"><img src="https://cdn-icons-png.flaticon.com/512/542/542689.png" alt=""></a>
                <a href="#"><img src="https://img.icons8.com/ios_filled/512/facebook-new.png" alt=""></a>
            </div>
        </div>
    </footer>
</body>

</html>