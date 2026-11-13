<?php
session_start();
include('db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get all products from database
$sql = "SELECT * FROM products ORDER BY category";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu | Toy World</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --primary-dark: #2980b9;
            --bg-color: #f8f9fa;
        }

        body {
            background-color: var(--bg-color);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            padding-top: 76px;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .product-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
            padding: 1.5rem;
            margin-bottom: 2rem;
            background: white;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .product-img {
            height: 250px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 1.5rem;
        }

        .category-title {
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 1rem;
            margin: 3rem 0 2rem;
            font-size: 2rem;
            font-weight: 600;
        }

        .btn-add-cart {
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 15px;
            padding: 1rem 2rem;
            transition: all 0.3s ease;
            width: 100%;
            font-weight: 500;
            margin-top: 1rem;
        }

        .btn-add-cart:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }

        .btn-add-cart.active {
            background: #2ecc71;
        }

        .price {
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: bold;
            display: block;
            margin: 1rem 0;
        }

        .card-body {
            padding: 1.5rem 0 0;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .card-text {
            color: #666;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
    </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="home.php">
            <img src="Toy World Logo.webp" alt="Toy World" style="height: 40px;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="home.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="nav-item"><a class="nav-link" href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                <li class="nav-item"><a class="nav-link active" href="menu.php"><i class="fas fa-gamepad"></i> Menu</a></li>
                <li class="nav-item"><a class="nav-link" href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
                <li class="nav-item"><a class="nav-link" href="faq.php"><i class="fas fa-question-circle"></i> FAQ</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-5">
    <h1 class="text-center mb-5">Our Toys Collection</h1>

    <?php
    $current_category = '';
    foreach($products as $product):
        if($current_category != $product['category']):
            if($current_category != '') echo '</div>'; // Close previous row
            $current_category = $product['category'];
    ?>
            <h2 class="category-title"><?php echo htmlspecialchars($product['category']); ?></h2>
            <div class="row g-4">
    <?php endif; ?>

        <div class="col-md-4 col-sm-6">
            <div class="product-card">
                <img src="<?php echo htmlspecialchars($product['image']); ?>" class="product-img" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="price">$<?php echo number_format($product['price'], 2); ?></span>
                        <button class="btn btn-add-cart">
                            <i class="fas fa-cart-plus me-2"></i>Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>

    <?php endforeach; ?>
    <?php if($current_category != '') echo '</div>'; // Close last row ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>