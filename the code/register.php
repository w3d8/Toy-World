<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $errors = [];

    // Validation
    if (empty($username)) {
        $errors[] = "Username is required";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters";
    }

    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }

    // Check if email already exists
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $errors[] = "Email address is already registered";
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['username'] = $username;
            $_SESSION['success_message'] = "Registration successful! Welcome to Toy World!";
            header("Location: profile.php");
            exit;
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Toy World</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --primary-dark: #2980b9;
            --error-color: #e74c3c;
            --success-color: #2ecc71;
        }

        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .register-container {
            max-width: 450px;
            width: 100%;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 0.5rem 1rem rgba(52, 152, 219, 0.15);
            background: white;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            text-align: center;
            padding: 2rem;
            border: none;
        }

        .card-header h2 {
            margin: 0;
            font-size: 2rem;
            font-weight: 600;
        }

        .card-body {
            padding: 2.5rem;
        }

        .form-floating {
            margin-bottom: 1.5rem;
        }

        .form-floating .form-control {
            border-radius: 10px;
            border: 2px solid rgba(52, 152, 219, 0.2);
            padding: 1rem 1rem;
            height: auto;
            font-size: 1rem;
        }

        .form-floating .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }

        .form-floating label {
            padding: 1rem;
            color: #6c757d;
        }

        .btn-register {
            background: #3498db;
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 10px;
            font-weight: 500;
            width: 100%;
            font-size: 1.1rem;
            margin-top: 1rem;
            transition: all 0.3s ease;
        }

        .btn-register:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }

        .alert {
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: none;
        }

        .alert-danger {
            background-color: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #6c757d;
        }

        .login-link a {
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .login-link a:hover {
            color: #2980b9;
            text-decoration: underline;
        }

        .input-group-text {
            background-color: transparent;
            border: 2px solid rgba(52, 152, 219, 0.2);
            border-right: none;
            border-radius: 10px 0 0 10px;
            color: #3498db;
        }

        .password-toggle {
            cursor: pointer;
            padding: 0.7rem 1rem;
            font-size: 1rem;
        }

        @media (max-width: 576px) {
            .register-container {
                margin: 1rem auto;
            }

            .card-header {
                padding: 1.5rem;
            }

            .card-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>

<div class="register-container">
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-user-plus me-2"></i>Create Account</h2>
            <p class="mb-0 mt-2">Join Toy World today!</p>
        </div>
        <div class="card-body">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0" style="padding-left: 1.2rem;">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" novalidate>
                <div class="form-floating">
                    <input type="text" name="username" class="form-control" id="username" placeholder="Username" 
                           value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" required>
                    <label for="username"><i class="fas fa-user me-2"></i>Username</label>
                </div>

                <div class="form-floating">
                    <input type="email" name="email" class="form-control" id="email" placeholder="Email" 
                           value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                    <label for="email"><i class="fas fa-envelope me-2"></i>Email address</label>
                </div>

                <div class="form-floating">
                    <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
                    <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                </div>

                <div class="form-floating">
                    <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="Confirm Password" required>
                    <label for="confirm_password"><i class="fas fa-lock me-2"></i>Confirm Password</label>
                </div>

                <button type="submit" class="btn btn-register">
                    <i class="fas fa-user-plus me-2"></i>Register
                </button>
            </form>

            <div class="login-link">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password visibility toggle
    const togglePassword = (inputId) => {
        const input = document.getElementById(inputId);
        input.type = input.type === 'password' ? 'text' : 'password';
    };

    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        let isValid = true;
        const username = document.getElementById('username');
        const email = document.getElementById('email');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');

        // Reset previous error states
        [username, email, password, confirmPassword].forEach(input => {
            input.classList.remove('is-invalid');
        });

        if (username.value.trim().length < 3) {
            username.classList.add('is-invalid');
            isValid = false;
        }

        if (!email.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            email.classList.add('is-invalid');
            isValid = false;
        }

        if (password.value.length < 6) {
            password.classList.add('is-invalid');
            isValid = false;
        }

        if (password.value !== confirmPassword.value) {
            confirmPassword.classList.add('is-invalid');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }
    });
});
</script>

</body>
</html>
