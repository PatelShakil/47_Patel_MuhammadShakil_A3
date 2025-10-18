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
    $category_id = $_POST['category_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_ext;
        $target_file = $upload_dir . $new_filename;
        
        // Resize and upload
        $source = imagecreatefromstring(file_get_contents($_FILES['image']['tmp_name']));
        $width = imagesx($source);
        $height = imagesy($source);
        $new_width = 400;
        $new_height = ($height / $width) * $new_width;
        
        $dest = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($dest, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        
        if ($file_ext == 'jpg' || $file_ext == 'jpeg') {
            imagejpeg($dest, $target_file, 90);
        } else if ($file_ext == 'png') {
            imagepng($dest, $target_file);
        }
        
        imagedestroy($source);
        imagedestroy($dest);
        $image = $new_filename;
    }
    
    $stmt = $conn->prepare("INSERT INTO products (category_id, name, price, description, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isdss", $category_id, $name, $price, $description, $image);
    if ($stmt->execute()) {
        $message = 'Product created successfully';
    }
    $stmt->close();
}

// Update
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $category_id = $_POST['category_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $old_image = $_POST['old_image'];
    
    $image = $old_image;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../uploads/';
        $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_ext;
        $target_file = $upload_dir . $new_filename;
        
        $source = imagecreatefromstring(file_get_contents($_FILES['image']['tmp_name']));
        $width = imagesx($source);
        $height = imagesy($source);
        $new_width = 400;
        $new_height = ($height / $width) * $new_width;
        
        $dest = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($dest, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        
        if ($file_ext == 'jpg' || $file_ext == 'jpeg') {
            imagejpeg($dest, $target_file, 90);
        } else if ($file_ext == 'png') {
            imagepng($dest, $target_file);
        }
        
        imagedestroy($source);
        imagedestroy($dest);
        
        if ($old_image && file_exists($upload_dir . $old_image)) {
            unlink($upload_dir . $old_image);
        }
        $image = $new_filename;
    }
    
    $stmt = $conn->prepare("UPDATE products SET category_id=?, name=?, price=?, description=?, image=? WHERE id=?");
    $stmt->bind_param("isdssi", $category_id, $name, $price, $description, $image, $id);
    if ($stmt->execute()) {
        $message = 'Product updated successfully';
    }
    $stmt->close();
}

// Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $result = $conn->query("SELECT image FROM products WHERE id=$id");
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        if ($product['image'] && file_exists('../uploads/' . $product['image'])) {
            unlink('../uploads/' . $product['image']);
        }
    }
    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = 'Product deleted successfully';
    }
    $stmt->close();
}

// Read
$products = $conn->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");
$categories = $conn->query("SELECT * FROM categories");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Products</title>
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
        input[type="text"], input[type="number"], textarea, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px; }
        button:hover { background: #0056b3; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #007bff; color: white; }
        .action-btn { padding: 5px 10px; margin-right: 5px; text-decoration: none; border-radius: 4px; display: inline-block; }
        .edit-btn { background: #28a745; color: white; }
        .delete-btn { background: #dc3545; color: white; }
        .message { background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .product-image { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Manage Products</h2>
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
            <h3><?php echo isset($_GET['edit']) ? 'Edit Product' : 'Add New Product'; ?></h3>
            <?php
            $edit_product = null;
            if (isset($_GET['edit'])) {
                $edit_id = $_GET['edit'];
                $stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
                $stmt->bind_param("i", $edit_id);
                $stmt->execute();
                $edit_product = $stmt->get_result()->fetch_assoc();
                $stmt->close();
            }
            ?>
            <form method="POST" enctype="multipart/form-data">
                <?php if ($edit_product): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_product['id']; ?>">
                    <input type="hidden" name="old_image" value="<?php echo $edit_product['image']; ?>">
                <?php endif; ?>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id" required>
                        <option value="">Select Category</option>
                        <?php 
                        $categories_list = $conn->query("SELECT * FROM categories");
                        while ($cat = $categories_list->fetch_assoc()): 
                        ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($edit_product && $edit_product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" value="<?php echo $edit_product['name'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" name="price" value="<?php echo $edit_product['price'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3"><?php echo $edit_product['description'] ?? ''; ?></textarea>
                </div>
                <div class="form-group">
                    <label>Image</label>
                    <input type="file" name="image" accept="image/*" <?php echo !$edit_product ? 'required' : ''; ?>>
                    <?php if ($edit_product && $edit_product['image']): ?>
                        <img src="../uploads/<?php echo $edit_product['image']; ?>" class="product-image" style="margin-top: 10px;">
                    <?php endif; ?>
                </div>
                <button type="submit" name="<?php echo $edit_product ? 'update' : 'create'; ?>">
                    <?php echo $edit_product ? 'Update' : 'Create'; ?>
                </button>
                <?php if ($edit_product): ?>
                    <a href="products.php"><button type="button">Cancel</button></a>
                <?php endif; ?>
            </form>
        </div>

        <h3>Products List</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = $products->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $product['id']; ?></td>
                    <td>
                        <?php if ($product['image']): ?>
                            <img src="../uploads/<?php echo $product['image']; ?>" class="product-image">
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                    <td>₹<?php echo number_format($product['price'], 2); ?></td>
                    <td>
                        <a href="?edit=<?php echo $product['id']; ?>" class="action-btn edit-btn">Edit</a>
                        <a href="?delete=<?php echo $product['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Delete this product?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php $conn->close(); ?>
