<?php
require_once '../config.php';

if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

$conn = getDBConnection();
$message = '';

// Create
if (isset($_POST['create'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $description);
    if ($stmt->execute()) {
        $message = 'Category created successfully';
    }
    $stmt->close();
}

// Update
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $stmt = $conn->prepare("UPDATE categories SET name=?, description=? WHERE id=?");
    $stmt->bind_param("ssi", $name, $description, $id);
    if ($stmt->execute()) {
        $message = 'Category updated successfully';
    }
    $stmt->close();
}

// Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM categories WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = 'Category deleted successfully';
    }
    $stmt->close();
}

// Read
$categories = $conn->query("SELECT * FROM categories ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Categories</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; }
        .header { background: #007bff; color: white; padding: 15px 30px; }
        .nav { background: #343a40; padding: 15px 30px; }
        .nav a { color: white; text-decoration: none; margin-right: 20px; padding: 8px 15px; border-radius: 4px; }
        .nav a:hover { background: #495057; }
        .container { padding: 30px; }
        .form-section { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px; }
        button:hover { background: #0056b3; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #007bff; color: white; }
        .action-btn { padding: 5px 10px; margin-right: 5px; text-decoration: none; border-radius: 4px; }
        .edit-btn { background: #28a745; color: white; }
        .delete-btn { background: #dc3545; color: white; }
        .message { background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Manage Categories</h2>
    </div>
    <div class="nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="categories.php">Categories</a>
        <a href="products.php">Products</a>
        <a href="logout.php">Logout</a>
    </div>
    <div class="container">
        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <div class="form-section">
            <h3><?php echo isset($_GET['edit']) ? 'Edit Category' : 'Add New Category'; ?></h3>
            <?php
            $edit_category = null;
            if (isset($_GET['edit'])) {
                $edit_id = $_GET['edit'];
                $stmt = $conn->prepare("SELECT * FROM categories WHERE id=?");
                $stmt->bind_param("i", $edit_id);
                $stmt->execute();
                $edit_category = $stmt->get_result()->fetch_assoc();
                $stmt->close();
            }
            ?>
            <form method="POST">
                <?php if ($edit_category): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_category['id']; ?>">
                <?php endif; ?>
                <div class="form-group">
                    <label>Category Name</label>
                    <input type="text" name="name" value="<?php echo $edit_category['name'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3"><?php echo $edit_category['description'] ?? ''; ?></textarea>
                </div>
                <button type="submit" name="<?php echo $edit_category ? 'update' : 'create'; ?>">
                    <?php echo $edit_category ? 'Update' : 'Create'; ?>
                </button>
                <?php if ($edit_category): ?>
                    <a href="categories.php"><button type="button">Cancel</button></a>
                <?php endif; ?>
            </form>
        </div>

        <h3>Categories List</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($category = $categories->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $category['id']; ?></td>
                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                    <td><?php echo htmlspecialchars($category['description']); ?></td>
                    <td>
                        <a href="?edit=<?php echo $category['id']; ?>" class="action-btn edit-btn">Edit</a>
                        <a href="?delete=<?php echo $category['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Delete this category?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php $conn->close(); ?>
