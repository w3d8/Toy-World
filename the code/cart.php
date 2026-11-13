<?php
session_start();
include('db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle remove from cart
if (isset($_POST['remove_item'])) {
    $product_id = $_POST['product_id'];
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
}

// Handle quantity updates
if (isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = max(1, intval($_POST['quantity']));
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
    }
}

// Get cart items details from database
$cart_items = [];
$total = 0;
if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $ids_string = implode(',', array_map('intval', $product_ids));
    $sql = "SELECT * FROM products WHERE id IN ($ids_string)";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $quantity = $_SESSION['cart'][$row['id']]['quantity'];
            $row['quantity'] = $quantity;
            $row['subtotal'] = $quantity * $row['price'];
            $cart_items[] = $row;
            $total += $row['subtotal'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart | Toy World</title>
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

        .cart-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 3rem 0;
            margin-bottom: 3rem;
            text-align: center;
        }

        .cart-item {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
        }

        .quantity-input {
            width: 80px;
            text-align: center;
            border: 2px solid rgba(52, 152, 219, 0.2);
            border-radius: 10px;
            padding: 0.5rem;
        }

        .btn-remove {
            color: #e74c3c;
            background: none;
            border: none;
            padding: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-remove:hover {
            color: #c0392b;
            transform: scale(1.1);
        }

        .cart-summary {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .btn-checkout {
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 15px;
            padding: 1rem 2rem;
            width: 100%;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-checkout:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }

        .empty-cart {
            text-align: center;
            padding: 3rem;
        }

        .empty-cart i {
            font-size: 4rem;
            color: #bdc3c7;
            margin-bottom: 1.5rem;
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
                <li class="nav-item"><a class="nav-link" href="menu.php"><i class="fas fa-gamepad"></i> Menu</a></li>
                <li class="nav-item"><a class="nav-link active" href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
                <li class="nav-item"><a class="nav-link" href="faq.php"><i class="fas fa-question-circle"></i> FAQ</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="cart-header">
    <div class="container">
        <h1><i class="fas fa-shopping-cart me-2"></i>Shopping Cart</h1>
        <p class="lead mb-0">Review your items before checkout</p>
    </div>
</div>

<div class="container pb-5">
    <?php if (empty($cart_items)): ?>
    <div class="empty-cart">
        <i class="fas fa-shopping-cart"></i>
        <h2>Your cart is empty</h2>
        <p class="text-muted">Add some toys to your cart and come back!</p>
        <a href="menu.php" class="btn btn-primary mt-3">Continue Shopping</a>
    </div>
    <?php else: ?>
    <div class="row">
        <div class="col-lg-8">
            <?php foreach ($cart_items as $item): ?>
            <div class="cart-item">
                <div class="row align-items-center">
                    <div class="col-md-2">
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="img-fluid">
                    </div>
                    <div class="col-md-4">
                        <h5 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h5>
                        <p class="text-muted mb-0">$<?php echo number_format($item['price'], 2); ?> each</p>
                    </div>
                    <div class="col-md-3">
                        <form method="post" class="d-inline">
                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input" onchange="this.form.submit()">
                            <input type="hidden" name="update_quantity" value="1">
                        </form>
                    </div>
                    <div class="col-md-2">
                        <span class="fw-bold">$<?php echo number_format($item['subtotal'], 2); ?></span>
                    </div>
                    <div class="col-md-1">
                        <form method="post" class="d-inline">
                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                            <button type="submit" name="remove_item" class="btn-remove" title="Remove item">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="col-lg-4">
            <div class="cart-summary">
                <h3 class="mb-4">Order Summary</h3>
                <div class="d-flex justify-content-between mb-3">
                    <span>Subtotal</span>
                    <span>$<?php echo number_format($total, 2); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Shipping</span>
                    <span>Free</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-4">
                    <span class="fw-bold">Total</span>
                    <span class="fw-bold">$<?php echo number_format($total, 2); ?></span>
                </div>
                <button class="btn-checkout">
                    <i class="fas fa-lock me-2"></i>Proceed to Checkout
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>