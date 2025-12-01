<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/register.css">
    <title>Register - Food Recipe</title>
</head>

<style>
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
    overflow: hidden;
}

body {
    background: url('../images/login-background.jpeg') no-repeat center;
    height: 100vh;
    background-size: cover;
    display: flex;
    justify-content: center;
    align-items: center;
}

.login-container {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    padding: 50px 40px;
    width: 100%;
    max-width: 450px;
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.login-header {
    margin-bottom: 40px;
}

.login-header h1 {
    font-size: 32px;
    color: #1a1a1a;
    margin-bottom: 10px;
    font-weight: 700;
}

.login-header p {
    color: #666;
    font-size: 15px;
}

.login-header a {
    color: #f5deb3;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.login-header a:hover {
    color: #d4a373;
}

.form-group {
    margin-bottom: 25px;
}

.form-group input {
    width: 100%;
    padding: 16px 20px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    font-size: 15px;
    transition: all 0.3s ease;
    background: #f8f9fa;
}

.form-group input:focus {
    outline: none;
    border-color: #f5deb3;
    background: white;
    box-shadow: 0 0 0 4px rgba(245, 222, 179, 0.1);
}

.form-group input::placeholder {
    color: black;
}

.submit-btn {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, #fff3cd 0%, #f5deb3 100%);
    color: #3b3b3b;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-top: 10px;
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(245, 222, 179, 0.5);
}

.submit-btn:active {
    transform: translateY(0);
}

.decorative-line {
    width: 60px;
    height: 4px;
    background: linear-gradient(135deg, #fff3cd 0%, #f5deb3 100%);
    border-radius: 2px;
    margin-bottom: 30px;
}

@media (max-width: 480px) {
    .login-container {
        padding: 40px 30px;
    }

    .login-header h1 {
        font-size: 28px;
    }
}
</style>

<body>
    <div class="login-container">
        <div class="login-header">
            <div class="decorative-line"></div>
            <h1>Register</h1>
            <p>Already have an account? <a href="login.php">Log in now</a></p>
        </div>
        <form method="POST" action="process_register.php">
            <div class="form-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="submit-btn">Sign Up</button>
        </form>
    </div>
</body>

</html>