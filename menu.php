<?php
session_start();
include "config.php";

// Debug: Cek koneksi
if (!$conn) {
    die("Koneksi database gagal!");
}

// Get category filter dari URL
$category_filter = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : 'all';

// Query untuk mendapatkan resep berdasarkan kategori
if ($category_filter === 'all') {
    $query = "SELECT r.*, c.category_name 
              FROM recipe r 
              LEFT JOIN category c ON r.id_category = c.id_category 
              ORDER BY r.created_at DESC";
} else {
    $query = "SELECT r.*, c.category_name 
              FROM recipe r 
              LEFT JOIN category c ON r.id_category = c.id_category 
              WHERE c.category_name = '$category_filter' 
              ORDER BY r.created_at DESC";
}

$result = mysqli_query($conn, $query);

// Debug: Cek error query
if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

$recipes = [];
while ($row = mysqli_fetch_assoc($result)) {
    $recipes[] = $row;
}

// Get semua kategori untuk filter
$categories_query = "SELECT * FROM category";
$categories_result = mysqli_query($conn, $categories_query);

// Debug: Cek error query kategori
if (!$categories_result) {
    die("Query kategori error: " . mysqli_error($conn));
}

$categories = [];
while ($cat = mysqli_fetch_assoc($categories_result)) {
    $categories[] = $cat;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodRecipe - Delicious Recipes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
            overflow-x: hidden;
        }
        
        /* Notification Styles */
        .alert-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1100;
            min-width: 300px;
            animation: slideInRight 0.3s ease-out;
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        /* Hero Section */
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 80px 0;
            background: linear-gradient(135deg, #fef5f0 0%, #faf6f3 100%);
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(255,182,193,0.2) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-30px, -30px) scale(1.1); }
        }
        
        .brand-title {
            color: #81b29a;
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 2rem;
            font-style: italic;
        }
        
        .hero-title {
            font-size: 4.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            color: #1a1a1a;
        }
        
        .hero-subtitle {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 2.5rem;
            max-width: 500px;
        }
        
        .btn-discover {
            background: linear-gradient(135deg, #ff7b9c 0%, #ff6b8a 100%);
            color: white;
            padding: 18px 50px;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(255,123,156,0.3);
            margin-right: 15px;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 18px 50px;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(102,126,234,0.3);
            margin-right: 15px;
        }
        
        .btn-discover:hover, .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }
        
        .hero-image-container {
            position: relative;
            animation: floatImage 6s ease-in-out infinite;
        }
        
        @keyframes floatImage {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(2deg); }
        }
        
        .hero-image {
            width: 100%;
            max-width: 550px;
            height: auto;
            border-radius: 50%;
            box-shadow: 0 30px 80px rgba(0,0,0,0.15);
            border: 15px solid white;
            position: relative;
            z-index: 2;
        }
        
        .hero-image-bg {
            position: absolute;
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1;
            opacity: 0.5;
        }
        
        /* Menu Section */
        .menu-section {
            padding: 80px 0;
            background: white;
        }
        
        .menu-tabs {
            display: flex;
            justify-content: center;
            gap: 3rem;
            margin-bottom: 4rem;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 1rem;
        }
        
        .menu-tab {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: #999;
            cursor: pointer;
            padding: 10px 20px;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .menu-tab:hover {
            color: #ff6b8a;
        }
        
        .menu-tab.active {
            color: #ff6b8a;
            border-bottom-color: #ff6b8a;
        }
        
        .menu-tab i {
            font-size: 1.3rem;
        }
        
        /* Recipe Cards */
        .recipe-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            height: 100%;
            display: flex;
            flex-direction: column;
            text-decoration: none;
            color: inherit;
            position: relative;
        }
        
        .recipe-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            text-decoration: none;
            color: inherit;
        }
        
        .recipe-image-container {
            position: relative;
            overflow: hidden;
            height: 300px;
        }
        
        .recipe-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .recipe-card:hover .recipe-image {
            transform: scale(1.1);
        }
        
        .recipe-views {
            position: absolute;
            top: 20px;
            right: 20px;
            background: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .recipe-views i {
            margin-right: 5px;
        }
        
        .recipe-content {
            padding: 25px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .recipe-category {
            display: inline-block;
            color: #ff8a65;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }
        
        .recipe-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 15px;
            line-height: 1.4;
        }
        
        .recipe-description {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 20px;
            flex-grow: 1;
        }
        
        /* Login Prompt */
        .login-prompt {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin: 20px 0;
            text-align: center;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 3rem;
            }
            
            .hero-image {
                max-width: 400px;
                margin-top: 3rem;
            }
            
            .menu-tabs {
                flex-wrap: wrap;
                gap: 1rem;
            }
            
            .recipe-image-container {
                height: 250px;
            }
        }
        
        @media (max-width: 576px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .menu-tabs {
                gap: 0.5rem;
            }
            
            .menu-tab {
                font-size: 0.9rem;
                padding: 8px 15px;
            }
            
            .btn-discover, .btn-login {
                padding: 15px 30px;
                font-size: 1rem;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Notification Messages -->
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert-notification">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert-notification">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <div class="brand-title">FoodRecipe</div>
                    <h1 class="hero-title">Just a Moment For a Bite of Perfection.</h1>
                    <p class="hero-subtitle">
                        "Enjoy mouthwatering recipes crafted by top chefs, bringing delicious flavors straight to your kitchen with ease!" 🍳 🔥
                    </p>
                    <button class="btn-discover" onclick="scrollToMenu()">Discover Menu</button>
                    <button class="btn-login" onclick="window.location.href='authentication/login.php'">
                        <i class="fas fa-sign-in-alt"></i> Login to Save Recipes
                    </button>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image-container">
                        <div class="hero-image-bg"></div>
                        <img src="https://images.unsplash.com/photo-1512058564366-18510be2db19?w=800&h=800&fit=crop" alt="Delicious Paella" class="hero-image">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Menu Section -->
    <section class="menu-section" id="menu">
        <div class="container">
            <!-- Login Prompt -->
            <div class="login-prompt">
                <p class="mb-3">Login to save recipes, add comments, and rate your favorite dishes.</p>
                <div>
                    <a href="authentication/login.php" class="btn btn-light btn-lg me-2">
                        <i class="fas fa-sign-in-alt"></i> Login Now
                    </a>
                </div>
            </div>

            <div class="menu-tabs">
                <div class="menu-tab active" data-category="all">All</div>
                <?php foreach($categories as $category): ?>
                    <div class="menu-tab" data-category="<?php echo strtolower($category['category_name']); ?>">
                        <i class="fas fa-utensils"></i> <?php echo ucfirst($category['category_name']); ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="row g-4" id="recipe-container">
                <?php if(count($recipes) > 0): ?>
                    <?php foreach($recipes as $recipe): ?>
                        <!-- Recipe Card -->
                        <div class="col-md-6 col-lg-4 recipe-item" data-category="<?php echo strtolower($recipe['category_name']); ?>">
                            <div class="recipe-card" onclick="window.location.href='detail.php?id=<?php echo $recipe['id_recipe']; ?>'">
                                <div class="recipe-image-container">
                                    <img src="<?php echo !empty($recipe['image']) ? $recipe['image'] : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&h=400&fit=crop'; ?>" 
                                         alt="<?php echo htmlspecialchars($recipe['title']); ?>" class="recipe-image">
                                    <div class="recipe-views">
                                        <i class="fas fa-eye"></i> 200+
                                    </div>
                                </div>
                                <div class="recipe-content">
                                    <span class="recipe-category"><?php echo $recipe['category_name']; ?></span>
                                    <h3 class="recipe-title"><?php echo htmlspecialchars($recipe['title']); ?></h3>
                                    <p class="recipe-description"><?php echo htmlspecialchars(substr($recipe['description'], 0, 100) . (strlen($recipe['description']) > 100 ? '...' : '')); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-utensils fa-3x text-muted mb-3"></i>
                        <h3 class="text-muted">Tidak ada resep ditemukan</h3>
                        <p class="text-muted">Silakan coba kategori lain atau tambahkan resep baru.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scroll to menu
        function scrollToMenu() {
            document.getElementById('menu').scrollIntoView({ behavior: 'smooth' });
        }

        // Menu filter functionality
        const menuTabs = document.querySelectorAll('.menu-tab');
        const recipeItems = document.querySelectorAll('.recipe-item');

        menuTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const category = tab.getAttribute('data-category');
                
                // Update active tab
                menuTabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                
                // Filter recipe items
                recipeItems.forEach(item => {
                    const itemCategory = item.getAttribute('data-category');
                    
                    if (category === 'all' || itemCategory === category) {
                        item.style.display = 'block';
                        // Force reflow for animation
                        void item.offsetWidth;
                        item.style.opacity = '1';
                        item.style.transform = 'scale(1)';
                    } else {
                        item.style.opacity = '0';
                        item.style.transform = 'scale(0.8)';
                        setTimeout(() => {
                            item.style.display = 'none';
                        }, 300);
                    }
                });
            });
        });

        // Add transition styles to recipe items
        recipeItems.forEach(item => {
            item.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        });

        // URL parameter handling for category filter
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const category = urlParams.get('category');
            
            if (category) {
                const targetTab = document.querySelector(`.menu-tab[data-category="${category.toLowerCase()}"]`);
                if (targetTab) {
                    targetTab.click();
                }
            }
        });

        // Auto-hide notifications after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
</body>
</html>
<?php
// Tutup koneksi database
mysqli_close($conn);
?>