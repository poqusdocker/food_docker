<?php
session_start();
include "config.php";

// Redirect ke user version jika sudah login
if (isset($_SESSION['user_id'])) {
    header("Location: detail_user.php");
    exit;
}

// Get recipe ID from URL
$recipe_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($recipe_id == 0) {
    die("Resep tidak ditemukan!");
}

// Get recipe data
$recipe_query = "SELECT r.*, c.category_name, u.username as author_name 
                 FROM recipe r 
                 LEFT JOIN category c ON r.id_category = c.id_category 
                 LEFT JOIN user u ON r.id_user = u.id_user 
                 WHERE r.id_recipe = $recipe_id";
$recipe_result = mysqli_query($conn, $recipe_query);

if (!$recipe_result || mysqli_num_rows($recipe_result) == 0) {
    die("Resep tidak ditemukan!");
}

$recipe = mysqli_fetch_assoc($recipe_result);

// Get recipe details (ingredients, steps, notes)
$details_query = "SELECT * FROM recipe_detail WHERE recipe_id = $recipe_id";
$details_result = mysqli_query($conn, $details_query);
$recipe_details = mysqli_fetch_assoc($details_result);

// Parse JSON data if exists
$ingredients = [];
$steps = [];
$notes = '';

if ($recipe_details) {
    if (!empty($recipe_details['ingredients'])) {
        $ingredients = json_decode($recipe_details['ingredients'], true);
    }
    if (!empty($recipe_details['steps'])) {
        $steps = json_decode($recipe_details['steps'], true);
    }
    if (!empty($recipe_details['notes'])) {
        $notes = $recipe_details['notes'];
    }
}

// Get comments for this recipe
$comments_query = "SELECT c.*, u.username FROM comment c 
                   LEFT JOIN user u ON c.id_user = u.id_user 
                   WHERE c.id_recipe = $recipe_id 
                   ORDER BY c.created_at DESC";
$comments_result = mysqli_query($conn, $comments_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Resep - Guest</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/detail.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="recipe-header">
            <button class="favorite-btn" onclick="toggleFavorite(this)">
                <i class="far fa-heart"></i>
            </button>
            <h1 class="recipe-title"><?php echo htmlspecialchars($recipe['title']); ?></h1>
            <p class="recipe-description">
                <?php echo htmlspecialchars($recipe['description']); ?>
            </p>
            
        </div>

        <!-- Image Section -->
        <div class="recipe-image-container">
            <img src="<?php echo !empty($recipe['image']) ? htmlspecialchars($recipe['image']) : 'https://images.unsplash.com/photo-1598103442097-8b74394b95c6?w=1200&h=500&fit=crop'; ?>" 
                 alt="<?php echo htmlspecialchars($recipe['title']); ?>" class="recipe-image">
        </div>

        <!-- Ingredients Section -->
        <div class="content-section">
            <h2 class="section-title">Bahan-Bahan</h2>
            <?php if(!empty($ingredients)): ?>
                <ul class="ingredients-list">
                    <?php foreach($ingredients as $ingredient): ?>
                        <li><?php echo htmlspecialchars($ingredient); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted">Bahan-bahan belum tersedia.</p>
            <?php endif; ?>
        </div>

        <!-- Steps Section -->
        <div class="content-section">
            <h2 class="section-title">Cara Membuat</h2>
            <?php if(!empty($steps)): ?>
                <ol class="steps-list">
                    <?php foreach($steps as $step): ?>
                        <li><?php echo htmlspecialchars($step); ?></li>
                    <?php endforeach; ?>
                </ol>
            <?php else: ?>
                <p class="text-muted">Langkah-langkah belum tersedia.</p>
            <?php endif; ?>
        </div>

        <!-- Notes Section -->
        <?php if(!empty($notes)): ?>
        <div class="content-section">
            <h2 class="section-title">Catatan Chef</h2>
            <div class="notes-box">
                <?php echo nl2br(htmlspecialchars($notes)); ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Comments Section - GUEST VERSION -->
        <div class="content-section" id="comments-section">
            <!-- Login Prompt untuk Guest -->
            <div class="comment-form">
                <h5 class="mb-3" style="color: var(--dark-brown);">
                    <i class="fas fa-comment"></i> Ingin Berkomentar?
                </h5>
                <div class="guest-access-alert">
                    <h6><i class="fas fa-lock"></i> Akses Terbatas untuk Guest</h6>
                    <p>Login untuk menulis komentar dan berinteraksi dengan komunitas.</p>
                    <a href="authentication/login.php" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Login Sekarang
                    </a>
                </div>
            </div>

            <h2 class="section-title">
                <i class="fas fa-comments"></i> Komentar 
                <span class="badge bg-secondary"><?php echo mysqli_num_rows($comments_result); ?></span>
            </h2>
            
            <!-- Comments List - Guest hanya bisa baca -->
            <?php if(mysqli_num_rows($comments_result) > 0): ?>
                <?php while($comment = mysqli_fetch_assoc($comments_result)): ?>
                    <div class="comment-item guest-comment" style="background: white; border-radius: 12px; padding: 20px; margin-bottom:10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); position: relative;">
                        <div class="comment-header">
                            <div class="comment-author">
                                <i class="fas fa-user"></i> 
                                <?php echo htmlspecialchars($comment['username']); ?>
                            </div>
                            <div class="comment-date">
                                <i class="far fa-clock"></i> 
                                <?php echo date('d F Y H:i', strtotime($comment['created_at'])); ?>
                            </div>
                        </div>
                        <div class="comment-text">
                            <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                        </div>
                        <!-- Guest TIDAK bisa delete comment -->
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-comments text-center py-4">
                    <i class="fas fa-comment-slash fa-2x mb-3" style="color: #6c757d;"></i>
                    <h5>Belum ada komentar</h5>
                    <p class="text-muted">Login untuk menjadi yang pertama berkomentar!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleFavorite(btn) {
            const icon = btn.querySelector('i');
            if (icon.classList.contains('far')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                btn.classList.add('active');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                btn.classList.remove('active');
            }
        }
    </script>
</body>
</html>
<?php
// Tutup koneksi database
mysqli_close($conn);
?>