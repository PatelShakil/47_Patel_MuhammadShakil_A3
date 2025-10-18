<?php
require_once '../config.php';

if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; }
        .header { background: #007bff; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .nav { background: #343a40; padding: 15px 30px; }
        .nav a { color: white; text-decoration: none; margin-right: 20px; padding: 8px 15px; border-radius: 4px; }
        .nav a:hover { background: #495057; }
        .container { padding: 30px; }
        .logout-btn { background: #dc3545; color: white; padding: 8px 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Admin Dashboard</h2>
        <div>
            <span>Welcome, <?php echo $_SESSION['admin_username']; ?></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
    <div class="nav">
        <a href="categories.php">Manage Categories</a>
        <a href="products.php">Manage Products</a>
    </div>
    <div class="container">
        <h3>Welcome to Admin Panel</h3>
        <p>Use the navigation menu to manage categories and products.</p>
    </div>
</body>
</html>
