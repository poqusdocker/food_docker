<?php
session_start();
include "../config.php";

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

// GET FAVORITE RECIPES FOR CURRENT USER
$favorites = [];
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $favorites_query = "SELECT f.id_favorite, 
                               r.id_recipe, r.title, r.description, r.image,
                               c.category_name
                       FROM favorite f 
                       JOIN recipe r ON f.id_recipe = r.id_recipe 
                       LEFT JOIN category c ON r.id_category = c.id_category
                       WHERE f.id_user = $user_id 
                       ORDER BY f.id_favorite DESC";
    $favorites_result = mysqli_query($conn, $favorites_query);
    
    if ($favorites_result) {
        while ($fav = mysqli_fetch_assoc($favorites_result)) {
            $favorites[] = $fav;
        }
    } else {
        error_log("Favorite query error: " . mysqli_error($conn));
    }
}

// Handle favorite actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_favorite'])) {
        $recipe_id = intval($_POST['recipe_id']);
        
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Please login to add favorites";
            header("Location: menu.php");
            exit;
        }
        
        $user_id = $_SESSION['user_id'];
        
        // Debug: Cek data
        error_log("Adding favorite - User: $user_id, Recipe: $recipe_id");
        
        // Check if already favorited
        $check_fav = "SELECT * FROM favorite WHERE id_user = $user_id AND id_recipe = $recipe_id";
        $check_result = mysqli_query($conn, $check_fav);
        
        if (!$check_result) {
            error_log("Check favorite error: " . mysqli_error($conn));
            $_SESSION['error'] = "Database error: " . mysqli_error($conn);
            header("Location: menu.php");
            exit;
        }
        
        if (mysqli_num_rows($check_result) == 0) {
            // TANPA created_at karena tidak ada di struktur tabel
            $add_fav = "INSERT INTO favorite (id_user, id_recipe) VALUES ($user_id, $recipe_id)";
            if (mysqli_query($conn, $add_fav)) {
                error_log("Favorite added successfully");
                $_SESSION['success'] = "Recipe added to favorites!";
            } else {
                error_log("Error adding favorite: " . mysqli_error($conn));
                $_SESSION['error'] = "Failed to add favorite: " . mysqli_error($conn);
            }
        } else {
            error_log("Recipe already in favorites");
            $_SESSION['info'] = "Recipe already in favorites";
        }
        
        header("Location: menu.php");
        exit;
        
    } elseif (isset($_POST['remove_favorite'])) {
        $favorite_id = intval($_POST['favorite_id']);
        
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Please login to manage favorites";
            header("Location: menu.php");
            exit;
        }
        
        $user_id = $_SESSION['user_id'];
        
        error_log("Removing favorite - ID: $favorite_id, User: $user_id");
        
        $remove_fav = "DELETE FROM favorite WHERE id_favorite = $favorite_id AND id_user = $user_id";
        if (mysqli_query($conn, $remove_fav)) {
            error_log("Favorite removed successfully");
            $_SESSION['success'] = "Recipe removed from favorites!";
        } else {
            error_log("Error removing favorite: " . mysqli_error($conn));
            $_SESSION['error'] = "Failed to remove favorite: " . mysqli_error($conn);
        }
        
        header("Location: menu.php");
        exit;
    }
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
        
        .btn-favorite {
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
        
        .btn-discover:hover, .btn-favorite:hover {
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
        
        .favorite-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            z-index: 10;
            font-size: 1.1rem;
        }
        
        .favorite-btn:hover {
            transform: scale(1.1);
            background: #ff6b8a;
            color: white;
        }
        
        .favorite-btn.active {
            background: #ff6b8a;
            color: white;
        }
        
        .favorite-btn.active:hover {
            background: #dc3545;
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
        
        /* Favorite Overlay */
        .favorite-overlay {
            position: fixed;
            top: 0;
            right: -400px;
            width: 400px;
            height: 100vh;
            background: white;
            box-shadow: -5px 0 25px rgba(0,0,0,0.1);
            transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            overflow-y: auto;
        }
        
        .favorite-overlay.active {
            right: 0;
        }
        
        .overlay-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 999;
        }
        
        .overlay-backdrop.active {
            opacity: 1;
            visibility: visible;
        }
        
        .overlay-header {
            padding: 25px;
            border-bottom: 1px solid #f0f0f0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .overlay-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }
        
        .overlay-close {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            position: absolute;
            top: 20px;
            right: 20px;
        }
        
        .favorite-list {
            padding: 20px;
        }
        
        .favorite-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 1px solid #f0f0f0;
            border-radius: 10px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            background: white;
        }
        
        .favorite-item:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .favorite-item-image {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .favorite-item-content {
            flex: 1;
            min-width: 0;
        }
        
        .favorite-item-title {
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
            font-size: 1rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .favorite-item-category {
            font-size: 0.75rem;
            color: #ff8a65;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        
        
        .remove-favorite {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.3s ease;
            flex-shrink: 0;
            margin-left: 10px;
        }
        
        .remove-favorite:hover {
            background: #c82333;
            transform: scale(1.05);
        }
        
        .no-favorites {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }
        
        .no-favorites i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #ddd;
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
            
            .favorite-overlay {
                width: 100%;
                right: -100%;
            }
            
            .favorite-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .favorite-item-image {
                width: 100%;
                height: 120px;
                margin-right: 0;
                margin-bottom: 10px;
            }
            
            .remove-favorite {
                align-self: flex-end;
                margin-top: 10px;
                margin-left: 0;
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
            
            .btn-discover, .btn-favorite {
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
    
    <?php if(isset($_SESSION['info'])): ?>
        <div class="alert-notification">
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <?php echo $_SESSION['info']; unset($_SESSION['info']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Favorite Overlay -->
    <div class="overlay-backdrop" id="overlayBackdrop"></div>
    <div class="favorite-overlay" id="favoriteOverlay">
        <div class="overlay-header">
            <h3 class="overlay-title">
                <i class="fas fa-heart"></i> My Favorites
            </h3>
            <button class="overlay-close" onclick="closeFavoriteOverlay()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="favorite-list">
            <?php if(count($favorites) > 0): ?>
                <?php foreach($favorites as $favorite): ?>
                    <div class="favorite-item">
                        <?php if(!empty($favorite['image'])): ?>
                            <img src="<?php echo $favorite['image']; ?>" alt="<?php echo htmlspecialchars($favorite['title']); ?>" class="favorite-item-image">
                        <?php else: ?>
                            <div style="width: 80px; height: 80px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-utensils text-muted"></i>
                            </div>
                        <?php endif; ?>
                        <div class="favorite-item-content">
                            <div class="favorite-item-category"><?php echo !empty($favorite['category_name']) ? $favorite['category_name'] : 'Uncategorized'; ?></div>
                            <div class="favorite-item-title"><?php echo htmlspecialchars($favorite['title']); ?></div>
                            <div class="favorite-item-description">
                                <?php echo htmlspecialchars(substr($favorite['description'], 0, 80) . (strlen($favorite['description']) > 80 ? '...' : '')); ?>
                            </div>
                        </div>
                        <form method="POST" class="remove-favorite-form">
                            <input type="hidden" name="favorite_id" value="<?php echo $favorite['id_favorite']; ?>">
                            <button type="submit" name="remove_favorite" class="remove-favorite" onclick="return confirm('Remove from favorites?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-favorites">
                    <i class="far fa-heart"></i>
                    <h5>No favorites yet</h5>
                    <p>Start adding recipes to your favorites!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

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
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <button class="btn-favorite" onclick="openFavoriteOverlay()">
                            <i class="fas fa-heart"></i> My Favorites
                        </button>
                    <?php else: ?>
                        <button class="btn-favorite" onclick="showLoginAlert()">
                            <i class="fas fa-heart"></i> My Favorites
                        </button>
                    <?php endif; ?>
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
                    <?php foreach($recipes as $recipe): 
                        // Check if recipe is in favorites
                        $is_favorited = false;
                        $favorite_id = null;
                        foreach($favorites as $fav) {
                            if ($fav['id_recipe'] == $recipe['id_recipe']) {
                                $is_favorited = true;
                                $favorite_id = $fav['id_favorite'];
                                break;
                            }
                        }
                    ?>
                        <!-- Recipe Card -->
                        <div class="col-md-6 col-lg-4 recipe-item" data-category="<?php echo strtolower($recipe['category_name']); ?>">
                            <div class="recipe-card" onclick="window.location.href='detail_menu.php?id=<?php echo $recipe['id_recipe']; ?>'">
                                <div class="recipe-image-container">
                                    <img src="<?php echo !empty($recipe['image']) ? $recipe['image'] : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&h=400&fit=crop'; ?>" 
                                         alt="<?php echo htmlspecialchars($recipe['title']); ?>" class="recipe-image">
                                    <div class="recipe-views">
                                        <i class="fas fa-eye"></i> 200+
                                    </div>
                                    <?php if(isset($_SESSION['user_id'])): ?>
                                        <form method="POST" class="favorite-form">
                                            <input type="hidden" name="recipe_id" value="<?php echo $recipe['id_recipe']; ?>">
                                            <?php if($is_favorited): ?>
                                                <button type="submit" name="remove_favorite" class="favorite-btn active" onclick="event.stopPropagation()">
                                                    <i class="fas fa-heart"></i>
                                                </button>
                                                <input type="hidden" name="favorite_id" value="<?php echo $favorite_id; ?>">
                                            <?php else: ?>
                                                <button type="submit" name="add_favorite" class="favorite-btn" onclick="event.stopPropagation()">
                                                    <i class="far fa-heart"></i>
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    <?php else: ?>
                                        <button class="favorite-btn" onclick="event.stopPropagation(); showLoginAlert();">
                                            <i class="far fa-heart"></i>
                                        </button>
                                    <?php endif; ?>
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

        // Favorite Overlay Functions
        function openFavoriteOverlay() {
            document.getElementById('favoriteOverlay').classList.add('active');
            document.getElementById('overlayBackdrop').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeFavoriteOverlay() {
            document.getElementById('favoriteOverlay').classList.remove('active');
            document.getElementById('overlayBackdrop').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Close overlay when clicking backdrop
        document.getElementById('overlayBackdrop').addEventListener('click', closeFavoriteOverlay);

        // Show login alert for non-logged in users
        function showLoginAlert() {
            alert('Please login to add favorites!');
        }

        // Menu filter functionality - FIXED VERSION
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

        // Prevent form submission from affecting card click
        document.querySelectorAll('.favorite-form').forEach(form => {
            form.addEventListener('click', function(e) {
                e.stopPropagation();
            });
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

        // Debug info
        console.log('Favorites count:', <?php echo count($favorites); ?>);
        console.log('Recipes count:', <?php echo count($recipes); ?>);
        console.log('User logged in:', <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>);
        console.log('Recipe items found:', document.querySelectorAll('.recipe-item').length);
    </script>
</body>
</html>
<?php
// Tutup koneksi database
mysqli_close($conn);
?>