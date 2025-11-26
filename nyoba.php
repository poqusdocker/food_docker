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
        }
        
        .btn-discover:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(255,123,156,0.4);
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
        }
        
        .recipe-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
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
        
        .recipe-servings {
            color: #999;
            font-size: 0.9rem;
        }
        
        .recipe-servings i {
            margin-right: 8px;
            color: #b19cd9;
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
        }
    </style>
</head>
<body>
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
                    <button class="btn-discover" onclick="scrollToMenu()">Favorite</button>
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
                <div class="menu-tab" data-category="appetizer">
                    <i class="fas fa-utensils"></i> Appetizer
                </div>
                <div class="menu-tab" data-category="main">
                    <i class="fas fa-hamburger"></i> Main
                </div>
                <div class="menu-tab" data-category="dessert">
                    <i class="fas fa-ice-cream"></i> Dessert
                </div>
            </div>

            <div class="row g-4">
                <!-- Recipe Card 1 -->
                <div class="col-md-6 col-lg-4" data-category="appetizer">
                    <div class="recipe-card">
                        <div class="recipe-image-container">
                            <img src="https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=600&h=400&fit=crop" alt="Fresh Salad" class="recipe-image">
                            <div class="recipe-views">
                                <i class="fas fa-eye"></i> 200+
                            </div>
                        </div>
                        <div class="recipe-content">
                            <span class="recipe-category">Appetizer</span>
                            <h3 class="recipe-title">Fresh Salad with Tahini Sauce</h3>
                            <p class="recipe-description">Crispy vegetables mixed with creamy tahini dressing, perfect for a healthy start.</p>
                        </div>
                    </div>
                </div>

                <!-- Recipe Card 2 -->
                <div class="col-md-6 col-lg-4" data-category="main">
                    <div class="recipe-card">
                        <div class="recipe-image-container">
                            <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=600&h=400&fit=crop" alt="Pizza" class="recipe-image">
                            <div class="recipe-views">
                                <i class="fas fa-eye"></i> 150+
                            </div>
                        </div>
                        <div class="recipe-content">
                            <span class="recipe-category">Main</span>
                            <h3 class="recipe-title">Chili con Carne with Nachos</h3>
                            <p class="recipe-description">Spicy beef chili topped with cheese and served with crispy nachos.</p>
                            <div class="recipe-servings">
                                <i class="fas fa-utensils"></i> 4-5 servings
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recipe Card 3 -->
                <div class="col-md-6 col-lg-4" data-category="appetizer">
                    <div class="recipe-card">
                        <div class="recipe-image-container">
                            <img src="https://images.unsplash.com/photo-1580867382867-e8d3ab8b9cb6?w=600&h=400&fit=crop" alt="Kimchi" class="recipe-image">
                            <div class="recipe-views">
                                <i class="fas fa-eye"></i> 200+
                            </div>
                        </div>
                        <div class="recipe-content">
                            <span class="recipe-category">Appetizer</span>
                            <h3 class="recipe-title">Korean Spicy Kimchi</h3>
                            <p class="recipe-description">Traditional Korean fermented cabbage with authentic spicy flavor.</p>
                            <div class="recipe-servings">
                                <i class="fas fa-utensils"></i> 3-4 servings
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recipe Card 4 -->
                <div class="col-md-6 col-lg-4" data-category="main">
                    <div class="recipe-card">
                        <div class="recipe-image-container">
                            <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&h=400&fit=crop" alt="Pasta" class="recipe-image">
                            <div class="recipe-views">
                                <i class="fas fa-eye"></i> 180+
                            </div>
                        </div>
                        <div class="recipe-content">
                            <span class="recipe-category">Main</span>
                            <h3 class="recipe-title">Creamy Mushroom Pasta</h3>
                            <p class="recipe-description">Rich and creamy pasta with sautéed mushrooms and fresh herbs.</p>
                            <div class="recipe-servings">
                                <i class="fas fa-utensils"></i> 2-3 servings
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recipe Card 5 -->
                <div class="col-md-6 col-lg-4" data-category="dessert">
                    <div class="recipe-card">
                        <div class="recipe-image-container">
                            <img src="https://images.unsplash.com/photo-1587314168485-3236d6710814?w=600&h=400&fit=crop" alt="Cheesecake" class="recipe-image">
                            <div class="recipe-views">
                                <i class="fas fa-eye"></i> 220+
                            </div>
                        </div>
                        <div class="recipe-content">
                            <span class="recipe-category">Dessert</span>
                            <h3 class="recipe-title">Classic New York Cheesecake</h3>
                            <p class="recipe-description">Smooth and creamy cheesecake with a buttery graham cracker crust.</p>
                            <div class="recipe-servings">
                                <i class="fas fa-utensils"></i> 8-10 servings
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recipe Card 6 -->
                <div class="col-md-6 col-lg-4" data-category="dessert">
                    <div class="recipe-card">
                        <div class="recipe-image-container">
                            <img src="https://images.unsplash.com/photo-1563805042-7684c019e1cb?w=600&h=400&fit=crop" alt="Pancakes" class="recipe-image">
                            <div class="recipe-views">
                                <i class="fas fa-eye"></i> 190+
                            </div>
                        </div>
                        <div class="recipe-content">
                            <span class="recipe-category">Dessert</span>
                            <h3 class="recipe-title">Fluffy Buttermilk Pancakes</h3>
                            <p class="recipe-description">Light and fluffy pancakes topped with fresh berries and maple syrup.</p>
                            <div class="recipe-servings">
                                <i class="fas fa-utensils"></i> 3-4 servings
                            </div>
                        </div>
                    </div>
                </div>
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
        const recipeCards = document.querySelectorAll('.col-md-6.col-lg-4');

        menuTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const category = tab.getAttribute('data-category');
                
                // Update active tab
                menuTabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                
                // Filter cards
                recipeCards.forEach(card => {
                    if (category === 'all' || card.getAttribute('data-category') === category) {
                        card.style.display = 'block';
                        setTimeout(() => {
                            card.style.opacity = '1';
                            card.style.transform = 'scale(1)';
                        }, 10);
                    } else {
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.8)';
                        setTimeout(() => {
                            card.style.display = 'none';
                        }, 300);
                    }
                });
            });
        });

        // Add transition styles
        recipeCards.forEach(card => {
            card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        });
    </script>
</body>
</html>