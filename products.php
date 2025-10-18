<?php
require_once 'config.php';
$conn = getDBConnection();

if (isset($_GET['category'])) {
    $category_id = $_GET['category'];
    $category_result = $conn->query("SELECT name FROM categories WHERE id=$category_id");
    $category_name = $category_result->fetch_assoc()['name'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .header { background: #007bff; color: white; padding: 20px 30px; }
        .container { padding: 30px; max-width: 1200px; margin: 0 auto; }
        .products { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin-top: 30px; }
        .product-card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .product-card img { width: 100%; height: 200px; object-fit: cover; }
        .product-info { padding: 15px; }
        .product-info h3 { color: #333; margin-bottom: 10px; }
        .product-info .price { color: #007bff; font-size: 20px; font-weight: bold; }
        .pagination { display: flex; justify-content: center; gap: 10px; margin-top: 30px; }
        .pagination button { padding: 8px 15px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .pagination button:hover { background: #0056b3; }
        .pagination button.active { background: #0056b3; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="header">
        <h2><?php echo htmlspecialchars($category_name); ?> - Products</h2>
    </div>
    <div class="container">
        <a href="index.php" class="back-link">← Back to Categories</a>
        <div id="products-container" class="products"></div>
        <div id="pagination" class="pagination"></div>
    </div>

    <script>
        const categoryId = <?php echo $category_id; ?>;
        let currentPage = 1;
        const perPage = 6;

        function loadProducts(page) {
            fetch(`ajax-products.php?category=${categoryId}&page=${page}&per_page=${perPage}`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('products-container');
                    container.innerHTML = '';
                    
                    data.products.forEach(product => {
                        const card = document.createElement('div');
                        card.className = 'product-card';
                        card.innerHTML = `
                            <img src="uploads/${product.image}" alt="${product.name}">
                            <div class="product-info">
                                <h3>${product.name}</h3>
                                <p>${product.description || ''}</p>
                                <div class="price">₹${parseFloat(product.price).toFixed(2)}</div>
                            </div>
                        `;
                        container.appendChild(card);
                    });

                    // Pagination
                    const paginationDiv = document.getElementById('pagination');
                    paginationDiv.innerHTML = '';
                    const totalPages = Math.ceil(data.total / perPage);
                    
                    for (let i = 1; i <= totalPages; i++) {
                        const btn = document.createElement('button');
                        btn.textContent = i;
                        btn.className = i === page ? 'active' : '';
                        btn.onclick = () => {
                            currentPage = i;
                            loadProducts(i);
                        };
                        paginationDiv.appendChild(btn);
                    }
                });
        }

        loadProducts(1);
    </script>
</body>
</html>
<?php $conn->close(); ?>
