<?php
require_once 'config.php';
$conn = getDBConnection();
$categories = $conn->query("SELECT * FROM categories");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart - Home</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; }
        .header { background: #007bff; color: white; padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; }
        .nav { background: #343a40; padding: 15px 30px; }
        .nav a { color: white; text-decoration: none; margin-right: 20px; padding: 8px 15px; border-radius: 4px; }
        .nav a:hover { background: #495057; }
        .container { padding: 30px; max-width: 1200px; margin: 0 auto; }
        .categories { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin-top: 30px; }
        .category-card { background: white; border: 1px solid #ddd; border-radius: 8px; padding: 20px; text-align: center; transition: transform 0.2s; cursor: pointer; }
        .category-card:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .category-card h3 { color: #007bff; margin-bottom: 10px; }
        .auth-links { display: flex; gap: 10px; }
        .auth-links a { color: white; text-decoration: none; padding: 8px 15px; background: #28a745; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Shopping Cart</h2>
        <div class="auth-links">
            <?php if (isUserLoggedIn()): ?>
                <span style="margin-right: 15px;">Welcome, <?php echo $_SESSION['username']; ?></span>
                <a href="user-logout.php">Logout</a>
            <?php else: ?>
                <a href="register.php">Register</a>
                <a href="user-login.php">Login</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="container">
        <h2>Categories</h2>
        <div class="categories">
            <?php while ($category = $categories->fetch_assoc()): ?>
                <a href="products.php?category=<?php echo $category['id']; ?>" style="text-decoration: none;">
                    <div class="category-card">
                        <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                        <p><?php echo htmlspecialchars($category['description']); ?></p>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
