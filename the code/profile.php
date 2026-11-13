<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include('db.php');

$user_id = $_SESSION['user_id'];
$success_message = "";

// Update user data if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_info'])) {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];

    $update_sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssi", $new_username, $new_email, $user_id);
    if ($stmt->execute()) {
        $success_message = "✅ Profile updated successfully!";
        // Update session variables
        $_SESSION['username'] = $new_username;
        $_SESSION['email'] = $new_email;
    }
    $stmt->close();
}

// Get user data
$user_sql = "SELECT username, email FROM users WHERE id = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();
$stmt->close();

// Count orders - with error handling for missing table
$order_count = 0;
try {
    // Check if orders table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'orders'");
    if ($table_check->num_rows > 0) {
        $sql = "SELECT COUNT(*) AS order_count FROM orders WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $order_count = $row['order_count'];
        }
        $stmt->close();
    }
} catch (Exception $e) {
    // Silently handle the error - table doesn't exist yet
    $order_count = 0;
}

$conn->close();
?>

<!-- Keep the PHP code section unchanged -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Toy World</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --primary-dark: #2980b9;
            --secondary-color: #2c3e50;
            --bg-color: #f8f9fa;
            --card-shadow: 0 0.5rem 1.5rem rgba(52, 152, 219, 0.15);
            --transition: all 0.3s ease;
        }

        body {
            background-color: var(--bg-color);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            padding-top: 76px;
            color: var(--secondary-color);
        }

        /* Enhanced Navbar */
        .navbar {
            background: var(--primary-color);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(52, 152, 219, 0.2);
        }

        .navbar-brand img {
            height: 45px;
            transition: var(--transition);
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.95) !important;
            font-weight: 500;
            padding: 0.7rem 1.2rem;
            border-radius: 8px;
            transition: var(--transition);
            margin: 0 5px;
        }

        .nav-link:hover, .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
            color: white !important;
        }

        /* Improved Profile Header */
        .profile-header {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 4rem 2rem;
            margin-bottom: 2.5rem;
            position: relative;
            overflow: hidden;
            border-radius: 0 0 30px 30px;
            box-shadow: 0 5px 20px rgba(52, 152, 219, 0.3);
        }

        .profile-avatar {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: white;
            padding: 5px;
            margin: 0 auto 2rem;
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
            border: 4px solid rgba(255, 255, 255, 0.3);
            transition: var(--transition);
        }

        .profile-avatar:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.4);
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Enhanced Cards */
        .profile-card {
            background: white;
            border-radius: 20px;
            border: none;
            box-shadow: var(--card-shadow);
            margin-bottom: 2.5rem;
            transition: var(--transition);
            padding: 2.5rem;
        }

        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 3rem rgba(52, 152, 219, 0.2);
        }

        .card-title {
            color: #3498db;
            font-weight: 600;
            font-size: 1.4rem;
            border-bottom: 2px solid rgba(52, 152, 219, 0.1);
            padding-bottom: 1rem;
            margin-bottom: 2rem;
            position: relative;
        }

        .card-title::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 60px;
            height: 2px;
            background: #3498db;
            border-radius: 2px;
        }

        /* Improved Form Elements */
        .input-group {
            margin-bottom: 1.5rem;
        }

        .input-group-text {
            background-color: rgba(52, 152, 219, 0.1);
            border: 1px solid rgba(52, 152, 219, 0.2);
            color: #3498db;
            border-radius: 10px 0 0 10px;
            padding: 0.8rem 1.2rem;
        }

        .form-control {
            border: 1px solid rgba(52, 152, 219, 0.2);
            padding: 0.8rem 1.2rem;
            border-radius: 0 10px 10px 0;
            font-size: 1rem;
        }

        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }

        /* Enhanced Buttons */
        .btn-update {
            background: #3498db;
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 10px;
            font-weight: 500;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }

        .btn-update:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
        }

        .btn-outline-primary {
            color: #3498db;
            border: 2px solid #3498db;
            padding: 1rem 2rem;
            border-radius: 10px;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-outline-primary:hover {
            background: #3498db;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }

        /* Improved Activity Items */
        .activity-item {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            border-radius: 15px;
            background: rgba(52, 152, 219, 0.05);
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }

        .activity-item:hover {
            background: rgba(52, 152, 219, 0.1);
            transform: translateX(5px);
        }

        .activity-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: rgba(52, 152, 219, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1.5rem;
            color: #3498db;
            font-size: 1.2rem;
            transition: var(--transition);
        }

        .activity-item:hover .activity-icon {
            background: #3498db;
            color: white;
            transform: rotate(5deg);
        }

        .activity-content {
            flex: 1;
        }

        .activity-time {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 0.3rem;
        }

        /* Alert Styling */
        .alert-success {
            background-color: rgba(52, 152, 219, 0.1);
            border: none;
            color: #3498db;
            padding: 1.2rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.1);
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            body {
                padding-top: 66px;
            }

            .profile-header {
                padding: 3rem 1rem;
                border-radius: 0 0 20px 20px;
            }

            .profile-avatar {
                width: 120px;
                height: 120px;
            }

            .profile-card {
                padding: 1.5rem;
                margin-bottom: 1.5rem;
            }

            .activity-item {
                padding: 1.2rem;
            }

            .activity-icon {
                width: 40px;
                height: 40px;
                margin-right: 1rem;
            }
        }
    </style>
</head>
<!-- Keep the rest of the HTML structure unchanged -->
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="home.php">
                <img src="Toy World Logo.webp" alt="Toy World" class="img-fluid">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="home.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="nav-item"><a class="nav-link active" href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="menu.php"><i class="fas fa-gamepad"></i> Menu</a></li>
                <li class="nav-item"><a class="nav-link" href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
                <li class="nav-item"><a class="nav-link" href="faq.php"><i class="fas fa-question-circle"></i> FAQ</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
            </div>
        </div>
    </nav>

    <!-- Profile Header -->
    <div class="profile-header text-center">
        <div class="profile-avatar">
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['username']); ?>&background=random" alt="Profile">
        </div>
        <h1 class="display-4"><?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <p class="lead">Welcome back to your gaming universe!</p>
    </div>

    <!-- Main Content -->
    <div class="container py-5">
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Account Info -->
        <div class="profile-card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-user-circle me-2"></i>Account Information</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p><span class="info-label"><i class="fas fa-user"></i></span> <?php echo htmlspecialchars($user['username']); ?></p>
                        <p><span class="info-label"><i class="fas fa-envelope"></i></span> <?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><span class="info-label"><i class="fas fa-calendar"></i></span> Member since <?php echo date('F Y'); ?></p>
                        <p><span class="info-label"><i class="fas fa-shopping-bag"></i></span> <?php echo $order_count; ?> orders placed</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Profile -->
        <div class="profile-card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-edit me-2"></i>Edit Profile</h5>
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="update_info" class="btn btn-update">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </form>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="profile-card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-history me-2"></i>Recent Activity</h5>
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-sign-in-alt"></i>
                    </div>
                    <div class="activity-content">
                        <div class="fw-bold">Account Login</div>
                        <div class="activity-time"><?php echo date('F j, Y, g:i a'); ?></div>
                    </div>
                </div>

                <?php if ($order_count > 0): ?>
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="activity-content">
                        <div class="fw-bold">Recent Order Placed</div>
                        <div class="activity-time">Recently</div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="text-center mt-4">
                    <a href="menu.php" class="btn btn-outline-primary">
                        <i class="fas fa-shopping-basket me-2"></i>Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>