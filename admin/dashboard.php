<?php
session_start();
include "../config.php";

if (!isset($_SESSION['user_id'])) {
    die("Akses ditolak! Anda belum login.");
}

if ($_SESSION['role'] !== 'admin') {
    die("Akses ditolak! Halaman ini khusus untuk admin.");
}

// Handle success/error messages from delete operations
if (isset($_GET['success'])) {
    echo "<div class='alert alert-success alert-dismissible fade show m-3' role='alert'>
            " . htmlspecialchars($_GET['success']) . "
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
          </div>";
}

if (isset($_GET['error'])) {
    echo "<div class='alert alert-danger alert-dismissible fade show m-3' role='alert'>
            " . htmlspecialchars($_GET['error']) . "
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
          </div>";
}

// Get data for editing
$editRecipe = null;
$editRecipeDetail = null;
if (isset($_GET['edit_recipe'])) {
    $id = mysqli_real_escape_string($conn, $_GET['edit_recipe']);
    $query = "SELECT * FROM recipe WHERE id_recipe = '$id'";
    $result = mysqli_query($conn, $query);
    $editRecipe = mysqli_fetch_assoc($result);
}

if (isset($_GET['edit_recipe_detail'])) {
    $id = mysqli_real_escape_string($conn, $_GET['edit_recipe_detail']);
    $query = "SELECT * FROM recipe_detail WHERE id_detail = '$id'";
    $result = mysqli_query($conn, $query);
    $editRecipeDetail = mysqli_fetch_assoc($result);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Recipe Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../style/dashboard.css">
    <style>
        .table-preview {
            max-width: 200px;
        }
        .preview-content {
            max-height: 60px;
            overflow: hidden;
        }
        .items-badge {
            font-size: 0.75em;
        }
        .sidebar-menu li a.active {
            background: rgba(255, 255, 255, 0.1) !important;
            border-left: 4px solid #007bff !important;
            color: white !important;
        }

    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="sidebar-brand">
                    <i class="bi bi-egg-fried"></i> Food Recipe Admin
                </div>

                <ul class="sidebar-menu">
                    <li><a href="#" onclick="switchTab(event, 'users')"><i class="bi bi-people"></i> Users</a></li>
                    <li><a href="#" onclick="switchTab(event, 'recipes')"><i class="bi bi-book"></i> Recipes</a></li>
                    <li><a href="#" onclick="switchTab(event, 'recipe-detail')"><i class="bi bi-file-text"></i> Recipe Detail</a></li>
                    <li><a href="#" onclick="switchTab(event, 'comments')"><i class="bi bi-chat-dots"></i> Comments</a></li>
                    <li><a href="#" onclick="switchTab(event, 'favorites')"><i class="bi bi-heart"></i> Favorites</a></li>
                </ul>

                <div class="btn btn-danger" style="padding: 20px; margin-top: 280px; text-decoration: none;">
                    <a class="text-white" href="../authentication/logout.php" style="text-decoration: none;">Logout</a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <div class="header-bg">
                    <nav class="breadcrumb-custom">
                        <a href="#">Pages</a> / <span class="active">Dashboard</span>
                    </nav>
                    <h2>Dashboard Management</h2>
                </div>

                <!-- Tabs Content -->
                <div class="tab-content scrollable-content" id="dashboardTabsContent">
                    
                    <!-- Recipes Table -->
                    <div class="tab-pane fade show active" id="recipes" role="tabpanel">
                        <div class="table-card">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-0">Recipes Management</h5>
                                <button class="btn-add btn btn-primary mr-5" data-bs-toggle="modal" data-bs-target="#recipeModal">
                                    <i class="bi bi-plus-circle"></i> Add Recipe
                                </button>
                            </div>

                            <!-- Recipe Modal (Add & Edit) -->
                            <div class="modal fade" id="recipeModal" tabindex="-1" aria-labelledby="recipeModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content p-3">
                                        <div class="header">
                                            <div class="header-content">
                                                <div>
                                                    <h1><?php echo $editRecipe ? 'Edit' : 'Add'; ?> Food Recipe</h1>
                                                </div>
                                            </div>
                                            <button type="button" class="close-btn" data-bs-dismiss="modal">×</button>
                                        </div>
                                        <form class="form-content" method="POST" action="../root/recipe_<?php echo $editRecipe ? 'edit' : 'add'; ?>.php" enctype="multipart/form-data">
                                            <?php if($editRecipe): ?>
                                                <input type="hidden" name="id_recipe" value="<?php echo $editRecipe['id_recipe']; ?>">
                                            <?php endif; ?>
                                            <div class="mb-4">
                                                <label class="form-label">Food Image</label>
                                                <input type="file" name="image" class="form-control" accept="image/*" <?php echo !$editRecipe ? 'required' : ''; ?>>
                                                <?php if($editRecipe && !empty($editRecipe['image'])): ?>
                                                    <small class="text-muted">Current: <?php echo basename($editRecipe['image']); ?></small>
                                                <?php endif; ?>
                                            </div>
                                            <div class="form-group">
                                                <label>Title<span class="required">*</span></label>
                                                <input type="text" name="title" placeholder="Masukkan judul resep..." 
                                                       value="<?php echo isset($editRecipe['title']) ? $editRecipe['title'] : ''; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Description<span class="required">*</span></label>
                                                <textarea name="description" placeholder="Tulis deskripsi resep di sini..." required><?php echo isset($editRecipe['description']) ? $editRecipe['description'] : ''; ?></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Category<span class="required">*</span></label>
                                                <select name="id_category" class="form-select" required>
                                                    <option value="" disabled selected>Select category</option>
                                                    <?php
                                                    $catQuery = "SELECT * FROM category";
                                                    $catResult = mysqli_query($conn, $catQuery);
                                                    while ($cat = mysqli_fetch_assoc($catResult)) {
                                                        $selected = ($editRecipe && $editRecipe['id_category'] == $cat['id_category']) ? 'selected' : '';
                                                        echo "<option value='{$cat['id_category']}' $selected>{$cat['category_name']}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="footer">
                                                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Batal</button>
                                                <button name="btn-submit" class="btn btn-submit"><?php echo $editRecipe ? 'Update' : 'Simpan'; ?></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Category</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT r.*, c.category_name FROM recipe r 
                                            LEFT JOIN category c ON r.id_category = c.id_category
                                            ORDER BY r.created_at DESC";
                                    $result = mysqli_query($conn, $query);

                                    while ($row = mysqli_fetch_assoc($result)) {
                                        // Handle potential NULL values
                                        $image = isset($row['image']) ? $row['image'] : '';
                                        $title = isset($row['title']) ? $row['title'] : 'No Title';
                                        $description = isset($row['description']) ? $row['description'] : 'No description available';
                                        $category = isset($row['category_name']) ? $row['category_name'] : 'Uncategorized';
                                        $created_at = isset($row['created_at']) ? $row['created_at'] : date('Y-m-d H:i:s');
                                    ?>
                                    <tr>
                                        <td>
                                            <?php if(!empty($image)): ?>
                                                <img src="<?php echo $image; ?>" 
                                                     alt="<?php echo htmlspecialchars($title); ?>"
                                                     style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                                            <?php else: ?>
                                                <div style="width: 80px; height: 80px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="bi bi-image" style="font-size: 24px; color: #6c757d;"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><strong><?php echo htmlspecialchars($title); ?></strong></td>
                                        <td class="table-preview">
                                            <div class="preview-content">
                                                <?php echo substr($description, 0, 100) . (strlen($description) > 100 ? '...' : ''); ?>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($category); ?></td>
                                        <td><?php echo date("d/m/Y", strtotime($created_at)); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="?edit_recipe=<?php echo $row['id_recipe']; ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="../root/recipe_delete.php?id=<?php echo $row['id_recipe']; ?>" class="btn btn-sm btn-outline-danger" 
                                                   onclick="return confirm('Yakin ingin hapus resep ini?')" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Recipe Detail Table -->
                    <div class="tab-pane fade" id="recipe-detail" role="tabpanel">
                        <div class="table-card">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-0">Recipe Details Management</h5>
                                <button class="btn-add btn btn-primary mr-5" data-bs-toggle="modal" data-bs-target="#recipeDetailModal">
                                    <i class="bi bi-plus-circle"></i> Add Recipe Detail
                                </button>
                            </div>

                            <!-- Recipe Detail Modal (Add & Edit) -->
                            <div class="modal fade" id="recipeDetailModal" tabindex="-1" aria-labelledby="recipeDetailModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content p-3">
                                        <div class="header">
                                            <div class="header-content">
                                                <div>
                                                    <h1><?php echo $editRecipeDetail ? 'Edit' : 'Create'; ?> Recipe Detail</h1>
                                                </div>
                                            </div>
                                            <button type="button" class="close-btn" data-bs-dismiss="modal">×</button>
                                        </div>
                                        <form class="form-content" method="POST" action="../root/recipe_detail_<?php echo $editRecipeDetail ? 'edit' : 'create'; ?>.php" id="recipeDetailForm">
                                            <?php if($editRecipeDetail): ?>
                                                <input type="hidden" name="id_detail" value="<?php echo $editRecipeDetail['id_detail']; ?>">
                                            <?php endif; ?>
                                            
                                            <div class="form-group">
                                                <label>Recipe<span class="required">*</span></label>
                                                <select name="recipe_id" class="form-select" required id="recipeSelect">
                                                    <option value="" disabled selected>Select recipe</option>
                                                    <?php
                                                    $recipeQuery = "SELECT * FROM recipe";
                                                    $recipeResult = mysqli_query($conn, $recipeQuery);
                                                    while ($recipe = mysqli_fetch_assoc($recipeResult)) {
                                                        $selected = ($editRecipeDetail && $editRecipeDetail['recipe_id'] == $recipe['id_recipe']) ? 'selected' : '';
                                                        echo "<option value='{$recipe['id_recipe']}' $selected>{$recipe['title']}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>Description</label>
                                                <textarea name="description" placeholder="Masukkan deskripsi detail resep..." rows="3"><?php echo isset($editRecipeDetail['description']) ? $editRecipeDetail['description'] : ''; ?></textarea>
                                            </div>

                                            <div class="form-group items-section">
                                                <label>Ingredients<span class="required">*</span></label>
                                                <div class="add-item-container">
                                                    <input type="text" id="newIngredientInput" class="add-item-input" placeholder="Tambah bahan baru...">
                                                    <button type="button" class="add-btn" onclick="addIngredient()">+</button>
                                                </div>
                                                <p class="helper-text">Tekan Enter atau klik tombol + untuk menambah bahan</p>
                                                <div class="items-list" id="ingredientsList"></div>
                                                <input type="hidden" name="ingredients" id="ingredientsData">
                                            </div>

                                            <div class="form-group items-section">
                                                <label>Steps<span class="required">*</span></label>
                                                <div class="add-item-container">
                                                    <input type="text" id="newStepInput" class="add-item-input" placeholder="Tambah langkah baru...">
                                                    <button type="button" class="add-btn" onclick="addStep()">+</button>
                                                </div>
                                                <p class="helper-text">Tekan Enter atau klik tombol + untuk menambah langkah</p>
                                                <div class="items-list" id="stepsList"></div>
                                                <input type="hidden" name="steps" id="stepsData">
                                            </div>

                                            <div class="form-group">
                                                <label>Notes</label>
                                                <textarea name="notes" placeholder="Masukkan catatan..." rows="2"><?php echo isset($editRecipeDetail['notes']) ? $editRecipeDetail['notes'] : ''; ?></textarea>
                                            </div>
                                            
                                            <div class="footer">
                                                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-submit"><?php echo $editRecipeDetail ? 'Update' : 'Simpan'; ?></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th width="100">Image</th>
                                        <th width="150">Recipe</th>
                                        <th width="120">Ingredients</th>
                                        <th width="120">Steps</th>
                                        <th width="150">Description</th>
                                        <th width="100">Notes</th>
                                        <th width="100">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $detailQuery = "SELECT rd.*, r.title, r.image 
                                                FROM recipe_detail rd 
                                                LEFT JOIN recipe r ON rd.recipe_id = r.id_recipe
                                                ORDER BY rd.id_detail DESC";
                                    $detailResult = mysqli_query($conn, $detailQuery);
                                    
                                    if (mysqli_num_rows($detailResult) > 0) {
                                        while ($detail = mysqli_fetch_assoc($detailResult)) {
                                            // Parse ingredients and steps as arrays
                                            $ingredients = isset($detail['ingredients']) ? json_decode($detail['ingredients'], true) : [];
                                            $steps = isset($detail['steps']) ? json_decode($detail['steps'], true) : [];
                                            $notes = isset($detail['notes']) ? $detail['notes'] : '';
                                            $description = isset($detail['description']) ? $detail['description'] : '';
                                            $title = isset($detail['title']) ? $detail['title'] : 'Untitled Recipe';
                                            $image = isset($detail['image']) ? $detail['image'] : '';
                                    ?>
                                    <tr>
                                        <td>
                                            <?php if(!empty($image)): ?>
                                                <img src="<?php echo $image; ?>" 
                                                    alt="<?php echo htmlspecialchars($title); ?>"
                                                    style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                                            <?php else: ?>
                                                <div style="width: 80px; height: 80px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="bi bi-image" style="font-size: 24px; color: #6c757d;"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($title); ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary items-badge">
                                                <?php echo is_array($ingredients) ? count($ingredients) : 0; ?> ingredients
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info items-badge">
                                                <?php echo is_array($steps) ? count($steps) : 0; ?> steps
                                            </span>
                                        </td>
                                        <td>
                                            <div class="preview-content text-truncate-2">
                                                <?php 
                                                if (!empty($description)) {
                                                    echo htmlspecialchars($description);
                                                } else {
                                                    echo '<span class="text-muted">No description</span>';
                                                }
                                                ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="preview-content text-truncate-2">
                                                <?php 
                                                if (!empty($notes)) {
                                                    echo htmlspecialchars($notes);
                                                } else {
                                                    echo '<span class="text-muted">No notes</span>';
                                                }
                                                ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="?edit_recipe_detail=<?php echo $detail['id_detail']; ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="../root/recipe_detail_delete.php?id=<?php echo $detail['id_detail']; ?>" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Are you sure you want to delete this recipe detail?')" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php 
                                        }
                                    } else {
                                    ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="bi bi-inbox display-4 text-muted d-block mb-2"></i>
                                            <span class="text-muted">No recipe details found</span>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Users Table -->
                    <div class="tab-pane fade" id="users" role="tabpanel">
                        <div class="table-card">
                            <h5>Users Management</h5>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>User Info</th>
                                        <th>Role</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $userQuery = "SELECT * FROM user ORDER BY created_at DESC";
                                    $userResult = mysqli_query($conn, $userQuery);
                                    while ($user = mysqli_fetch_assoc($userResult)) {
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="user-info">
                                                <div class="user-avatar">
                                                    <i class="bi bi-person-fill"></i>
                                                </div>
                                                <div class="user-details">
                                                    <h6><?php echo htmlspecialchars($user['username']); ?></h6>
                                                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $user['role'] == 'admin' ? 'danger' : 'success'; ?>">
                                                <?php echo htmlspecialchars($user['role']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date("d/m/Y", strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <a href="../root/user_delete.php?id=<?php echo $user['id_user']; ?>" class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Yakin ingin hapus user ini?')">
                                                <i class="bi bi-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Comments Table -->
                    <div class="tab-pane fade" id="comments" role="tabpanel">
                        <div class="table-card">
                            <h5>Comments Management</h5>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Recipe</th>
                                        <th>Content</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $commentQuery = "SELECT c.*, u.username, r.title 
                                                   FROM comment c 
                                                   LEFT JOIN user u ON c.id_user = u.id_user 
                                                   LEFT JOIN recipe r ON c.id_recipe = r.id_recipe 
                                                   ORDER BY c.created_at DESC";
                                    $commentResult = mysqli_query($conn, $commentQuery);
                                    while ($comment = mysqli_fetch_assoc($commentResult)) {
                                        $content = isset($comment['content']) ? $comment['content'] : 'No content';
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($comment['username']); ?></td>
                                        <td><?php echo htmlspecialchars($comment['title']); ?></td>
                                        <td class="table-preview">
                                            <div class="preview-content">
                                                <?php echo substr($content, 0, 100) . (strlen($content) > 100 ? '...' : ''); ?>
                                            </div>
                                        </td>
                                        <td><?php echo date("d/m/Y", strtotime($comment['created_at'])); ?></td>
                                        <td>
                                            <a href="../root/comment_delete.php?id=<?php echo $comment['id_comment']; ?>" class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Yakin ingin hapus komentar ini?')">
                                                <i class="bi bi-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Favorites Table -->
                    <div class="tab-pane fade" id="favorites" role="tabpanel">
                        <div class="table-card">
                            <h5>Favorites Management</h5>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Recipe</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $favoriteQuery = "SELECT f.*, u.username, r.title 
                                                    FROM favorite f 
                                                    LEFT JOIN user u ON f.id_user = u.id_user 
                                                    LEFT JOIN recipe r ON f.id_recipe = r.id_recipe";
                                    $favoriteResult = mysqli_query($conn, $favoriteQuery);
                                    while ($favorite = mysqli_fetch_assoc($favoriteResult)) {
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="user-info">
                                                <div class="user-avatar">
                                                    <i class="bi bi-person-fill"></i>
                                                </div>
                                                <div class="user-details">
                                                    <h6><?php echo htmlspecialchars($favorite['username']); ?></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($favorite['title']); ?></td>
                                        <td>
                                            <a href="../root/favorite_delete.php?id=<?php echo $favorite['id_favorite']; ?>" class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Yakin ingin hapus favorite ini?')">
                                                <i class="bi bi-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tab Switching Function
        function switchTab(event, tabName) {
            event.preventDefault();
            
            // Remove active dari semua sidebar menu
            document.querySelectorAll('.sidebar-menu li a').forEach(link => {
                link.classList.remove('active');
            });
            
            // Add active ke yang diklik
            event.currentTarget.classList.add('active');
            
            // Hide semua tab content
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('show', 'active');
            });
            
            // Show tab yang dipilih
            const targetTab = document.getElementById(tabName);
            if (targetTab) {
                targetTab.classList.add('show', 'active');
            }
            
            // Simpan tab terakhir
            localStorage.setItem('lastTab', tabName);
        }

        // Load Last Active Tab
        document.addEventListener('DOMContentLoaded', function() {
            const lastTab = localStorage.getItem('lastTab') || 'recipes';
            
            // Update active sidebar
            document.querySelectorAll('.sidebar-menu li a').forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('onclick') && link.getAttribute('onclick').includes(lastTab)) {
                    link.classList.add('active');
                }
            });

            // Show the correct tab
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('show', 'active');
            });
            
            const targetTab = document.getElementById(lastTab);
            if (targetTab) {
                targetTab.classList.add('show', 'active');
            }

            // Auto show modal if editing
            <?php if($editRecipe): ?>
                setTimeout(function() {
                    var recipeModal = new bootstrap.Modal(document.getElementById('recipeModal'));
                    recipeModal.show();
                }, 100);
            <?php endif; ?>

            <?php if($editRecipeDetail): ?>
                setTimeout(function() {
                    var recipeDetailModal = new bootstrap.Modal(document.getElementById('recipeDetailModal'));
                    recipeDetailModal.show();
                    
                    // Load existing ingredients and steps for editing
                    <?php if($editRecipeDetail && isset($editRecipeDetail['ingredients'])): ?>
                        ingredients = <?php echo !empty($editRecipeDetail['ingredients']) ? $editRecipeDetail['ingredients'] : '[]'; ?>;
                    <?php endif; ?>
                    
                    <?php if($editRecipeDetail && isset($editRecipeDetail['steps'])): ?>
                        steps = <?php echo !empty($editRecipeDetail['steps']) ? $editRecipeDetail['steps'] : '[]'; ?>;
                    <?php endif; ?>
                    
                    // Render them
                    renderIngredients();
                    renderSteps();
                    updateIngredientsData();
                    updateStepsData();
                }, 100);
            <?php endif; ?>
        });

        // Ingredients and Steps Management
        let ingredients = [];
        let steps = [];

        function addIngredient() {
            const input = document.getElementById('newIngredientInput');
            const value = input.value.trim();
            
            if (value) {
                ingredients.push(value);
                input.value = '';
                renderIngredients();
                updateIngredientsData();
            }
        }

        function addStep() {
            const input = document.getElementById('newStepInput');
            const value = input.value.trim();
            
            if (value) {
                steps.push(value);
                input.value = '';
                renderSteps();
                updateStepsData();
            }
        }

        function removeIngredient(index) {
            ingredients.splice(index, 1);
            renderIngredients();
            updateIngredientsData();
        }

        function removeStep(index) {
            steps.splice(index, 1);
            renderSteps();
            updateStepsData();
        }

        function renderIngredients() {
            const list = document.getElementById('ingredientsList');
            list.innerHTML = '';
            
            ingredients.forEach((ingredient, index) => {
                const item = document.createElement('div');
                item.className = 'item';
                item.innerHTML = `
                    <span>${ingredient}</span>
                    <button type="button" class="remove-btn" onclick="removeIngredient(${index})">×</button>
                `;
                list.appendChild(item);
            });
        }

        function renderSteps() {
            const list = document.getElementById('stepsList');
            list.innerHTML = '';
            
            steps.forEach((step, index) => {
                const item = document.createElement('div');
                item.className = 'item';
                item.innerHTML = `
                    <span><strong>${index + 1}.</strong> ${step}</span>
                    <button type="button" class="remove-btn" onclick="removeStep(${index})">×</button>
                `;
                list.appendChild(item);
            });
        }

        function updateIngredientsData() {
            document.getElementById('ingredientsData').value = JSON.stringify(ingredients);
        }

        function updateStepsData() {
            document.getElementById('stepsData').value = JSON.stringify(steps);
        }

        // Enter key support
        document.getElementById('newIngredientInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addIngredient();
            }
        });

        document.getElementById('newStepInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addStep();
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