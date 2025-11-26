<?php
session_start();
include "../config.php";

// Redirect ke login jika belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: detail_guest.php");
    exit;
}

// DEBUG: Tampilkan semua recipe yang ada
$debug_query = "SELECT id_recipe, title FROM recipe";
$debug_result = mysqli_query($conn, $debug_query);
$available_recipes = [];
while($row = mysqli_fetch_assoc($debug_result)) {
    $available_recipes[] = $row;
}

// Ambil recipe_id dari URL atau parameter
if (isset($_GET['id'])) {
    $recipe_id = intval($_GET['id']);
} else {
    // Ambil ID recipe pertama yang ada di database
    if (!empty($available_recipes)) {
        $recipe_id = $available_recipes[0]['id_recipe'];
    } else {
        die("Tidak ada recipe di database! Silakan tambahkan recipe terlebih dahulu.");
    }
}

// CEK APAKAH RECIPE ID ADA DI DATABASE - UPDATE QUERY UNTUK GET DATA LENGKAP
$check_recipe = "SELECT r.*, c.category_name 
                 FROM recipe r 
                 LEFT JOIN category c ON r.id_category = c.id_category 
                 WHERE r.id_recipe = $recipe_id";
$recipe_exists = mysqli_query($conn, $check_recipe);

if (mysqli_num_rows($recipe_exists) == 0) {
    // Jika recipe tidak ditemukan, gunakan recipe pertama yang ada
    if (!empty($available_recipes)) {
        $first_recipe = $available_recipes[0];
        $recipe_id = $first_recipe['id_recipe'];
        
        // Redirect ke recipe yang tersedia
        header("Location: detail_menu.php?id=$recipe_id");
        exit;
    } else {
        die("Tidak ada recipe di database! Silakan tambahkan recipe terlebih dahulu di admin dashboard.");
    }
} else {
    $recipe_data = mysqli_fetch_assoc($recipe_exists);
    $recipe_title = $recipe_data['title'];
    $recipe_description = $recipe_data['description'];
    $recipe_image = !empty($recipe_data['image']) ? $recipe_data['image'] : 'https://images.unsplash.com/photo-1598103442097-8b74394b95c6?w=1200&h=500&fit=crop';
}

// GET RECIPE DETAIL (INGREDIENTS, STEPS, NOTES)
$detail_query = "SELECT * FROM recipe_detail WHERE recipe_id = $recipe_id";
$detail_result = mysqli_query($conn, $detail_query);

// Parse ingredients and steps from JSON
$ingredients = [];
$steps = [];
$notes = '';

if (mysqli_num_rows($detail_result) > 0) {
    $recipe_detail = mysqli_fetch_assoc($detail_result);
    
    // Parse ingredients from JSON
    if (!empty($recipe_detail['ingredients'])) {
        $ingredients_data = $recipe_detail['ingredients'];
        $ingredients = json_decode($ingredients_data, true);
        
        // Jika decode gagal, coba sebagai plain text
        if (json_last_error() !== JSON_ERROR_NONE) {
            $ingredients = array_map('trim', explode(',', $ingredients_data));
        }
        
        if (!is_array($ingredients)) {
            $ingredients = [];
        }
    }
    
    // Parse steps from JSON
    if (!empty($recipe_detail['steps'])) {
        $steps_data = $recipe_detail['steps'];
        $steps = json_decode($steps_data, true);
        
        // Jika decode gagal, coba sebagai plain text
        if (json_last_error() !== JSON_ERROR_NONE) {
            $steps = array_map('trim', explode('.', $steps_data));
        }
        
        if (!is_array($steps)) {
            $steps = [];
        }
    }
    
    // Get notes
    $notes = $recipe_detail['notes'] ?? '';
}

// Get comments for this recipe
$comments_query = "SELECT c.*, u.username, u.id_user as user_id FROM comment c 
                   LEFT JOIN user u ON c.id_user = u.id_user 
                   WHERE c.id_recipe = $recipe_id 
                   ORDER BY c.created_at DESC";
$comments_result = mysqli_query($conn, $comments_query);

// DEBUG: Cek jumlah komentar
$comments_count = mysqli_num_rows($comments_result);

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    $id_user = $_SESSION['user_id'];
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    
    // Validasi content tidak kosong
    if (!empty(trim($content))) {
        $insert_comment = "INSERT INTO comment (id_user, id_recipe, content, created_at) 
                           VALUES ('$id_user', '$recipe_id', '$content', NOW())";
        
        if (mysqli_query($conn, $insert_comment)) {
            // Redirect untuk menghindari resubmission
            header("Location: detail_menu.php?id=$recipe_id#comments-section");
            exit;
        } else {
            $error = "Gagal menambahkan komentar: " . mysqli_error($conn);
        }
    } else {
        $error = "Komentar tidak boleh kosong!";
    }
}

// Handle comment deletion
if (isset($_GET['delete_comment'])) {
    $comment_id = intval($_GET['delete_comment']);
    $id_user = $_SESSION['user_id'];
    
    // Check if user owns the comment or is admin
    $check_comment = "SELECT * FROM comment WHERE id_comment = $comment_id AND id_user = $id_user";
    $check_result = mysqli_query($conn, $check_comment);
    
    if (mysqli_num_rows($check_result) > 0 || (isset($_SESSION['role']) && $_SESSION['role'] === 'admin')) {
        $delete_comment = "DELETE FROM comment WHERE id_comment = $comment_id";
        if (mysqli_query($conn, $delete_comment)) {
            header("Location: detail_menu.php?id=$recipe_id#comments-section");
            exit;
        } else {
            $error = "Gagal menghapus komentar: " . mysqli_error($conn);
        }
    } else {
        $error = "Anda tidak memiliki izin untuk menghapus komentar ini!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($recipe_title); ?> - Detail Resep</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style/detail.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600;700&family=Montserrat:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <style>
        /* Tambahan style untuk komentar */
        .comment-item {
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
        }
        
        .comment-item:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .user-comment {
            border-left: 4px solid #007bff;
        }
        
        .other-comment {
            border-left: 4px solid #28a745;
        }
        
        .delete-comment {
            transition: all 0.3s ease;
        }
        
        .delete-comment:hover {
            background: #c82333 !important;
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header Section -->
        <div class="recipe-header">
            <button class="favorite-btn" onclick="toggleFavorite(this)">
                <i class="far fa-heart"></i>
            </button>
            <h1 class="recipe-title"><?php echo htmlspecialchars($recipe_title); ?></h1>
            <p class="recipe-description">
                <?php echo htmlspecialchars($recipe_description); ?>
            </p>
        </div>

        <!-- Image Section -->
        <div class="recipe-image-container">
            <img src="<?php echo $recipe_image; ?>"
                alt="<?php echo htmlspecialchars($recipe_title); ?>" class="recipe-image">
        </div>

        <!-- Ingredients Section -->
        <?php if(!empty($ingredients)): ?>
        <div class="content-section">
            <h2 class="section-title">Bahan-Bahan</h2>
            <ul class="ingredients-list">
                <?php foreach($ingredients as $ingredient): ?>
                    <li><?php echo htmlspecialchars($ingredient); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Steps Section -->
        <?php if(!empty($steps)): ?>
        <div class="content-section">
            <h2 class="section-title">Cara Membuat</h2>
            <ol class="steps-list">
                <?php foreach($steps as $step): ?>
                    <li><?php echo htmlspecialchars($step); ?></li>
                <?php endforeach; ?>
            </ol>
        </div>
        <?php endif; ?>

        <!-- Notes Section -->
        <?php if(!empty($notes)): ?>
        <div class="content-section">
            <h2 class="section-title">Catatan Chef</h2>
            <div class="notes-box">
                <?php echo nl2br(htmlspecialchars($notes)); ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Comments Section - USER VERSION -->
        <div class="content-section" id="comments-section">
            <!-- Tampilkan error jika ada -->
            <?php if(isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Comment Form -->
            <div class="comment-form">
                <h5 class="mb-3" style="color: var(--dark-brown);">
                    <i class="fas fa-edit"></i> Tulis Komentar Anda
                </h5>
                <form method="POST" id="commentForm">
                    <div class="mb-3">
                        <textarea class="form-control" name="content" rows="4"
                            placeholder="Bagikan pendapat Anda tentang resep ini..." required></textarea>
                    </div>
                    <button type="submit" name="submit_comment" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> KIRIM KOMENTAR
                    </button>
                </form>
            </div>

            <h2 class="section-title">
                <i class="fas fa-comments"></i> Komentar
                <span class="badge bg-secondary"><?php echo $comments_count; ?></span>
            </h2>

            <!-- Comments List -->
            <?php if($comments_count > 0): ?>
            <div class="comments-container" style="display: flex; flex-direction: column; gap: 20px;">
                <?php while($comment = mysqli_fetch_assoc($comments_result)): ?>
                <?php 
                // Pastikan menggunakan field yang benar untuk user_id
                $is_own_comment = ($_SESSION['user_id'] == $comment['id_user']); 
                ?>
                <div class="comment-item <?php echo $is_own_comment ? 'user-comment' : 'other-comment'; ?>"
                    id="comment-<?php echo $comment['id_comment']; ?>"
                    style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); position: relative;">

                    <!-- Header dengan tombol delete di kanan -->
                    <div class="comment-header"
                        style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                        <div class="comment-author-info">
                            <div class="comment-author" style="font-weight: 600; color: #333; font-size: 1rem;">
                                <i class="fas fa-user"></i>
                                <?php echo htmlspecialchars($comment['username']); ?>
                                <?php if($is_own_comment): ?>
                                <span class="badge bg-primary" style="font-size: 0.7rem; margin-left: 8px;">Anda</span>
                                <?php endif; ?>
                            </div>
                            <div class="comment-date" style="color: #666; font-size: 0.85rem; margin-top: 4px;">
                                <i class="far fa-clock"></i>
                                <?php echo date('d F Y H:i', strtotime($comment['created_at'])); ?>
                            </div>
                        </div>

                        <!-- Delete Button - di kanan atas dengan background merah -->
                        <?php if($is_own_comment): ?>
                        <div class="comment-actions">
                            <a href="detail_menu.php?id=<?php echo $recipe_id; ?>&delete_comment=<?php echo $comment['id_comment']; ?>#comments-section"
                                class="delete-comment" onclick="return confirm('Yakin ingin menghapus komentar ini?')"
                                style="background: #dc3545; color: white; padding: 8px 12px; border-radius: 6px; text-decoration: none; display: flex; align-items: center; gap: 6px; font-size: 0.85rem; transition: all 0.3s;">
                                <i class="fas fa-trash"></i> Hapus
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Comment Text -->
                    <div class="comment-text"
                        style="color: #444; line-height: 1.6; font-size: 0.95rem; padding-right: 20px;">
                        <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="no-comments text-center py-4">
                <i class="fas fa-comment-slash fa-2x mb-3" style="color: #6c757d;"></i>
                <h5>Belum ada komentar</h5>
                <p class="text-muted">Jadilah yang pertama berkomentar tentang resep ini!</p>
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

        // Smooth scroll ke comment section
        document.addEventListener('DOMContentLoaded', function () {
            if (window.location.hash === '#comments-section') {
                document.getElementById('comments-section').scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>

</html>