<?php
session_start();
include('db.php');

// Initialize error message variable
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate inputs
    if(empty($_POST['loginEmail']) || empty($_POST['loginPassword'])) {
        $error_message = "Please fill in all fields";
    } else {
        $email = filter_var($_POST['loginEmail'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['loginPassword'];
        
        // Check if email is valid
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Invalid email format";
        } else {
            $stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE email=?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                if (password_verify($password, $user['password'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['logged_in'] = true;
                    
                    // Regenerate session ID for security
                    session_regenerate_id(true);
                    
                    // Redirect to home page
                    header("Location: profile.php");
                    exit;
                } else {
                    $error_message = "Invalid email or password";
                }
            } else {
                // Use the same error message to prevent user enumeration
                $error_message = "Invalid email or password";
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Toy World</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --primary-dark: #2980b9;
            --error-color: #e74c3c;
        }

        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .login-container {
            max-width: 400px;
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

        .card-header h1 {
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

        .btn-login {
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

        .btn-login:hover {
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

        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #6c757d;
        }

        .register-link a {
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .register-link a:hover {
            color: #2980b9;
            text-decoration: underline;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            transition: all 0.3s ease;
        }

        .password-toggle:hover {
            color: #3498db;
        }

        @media (max-width: 576px) {
            .login-container {
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

<div class="login-container">
    <div class="card">
        <div class="card-header">
            <h1><i class="fas fa-lock me-2"></i>Login</h1>
            <p class="mb-0 mt-2">Welcome back to Toy World!</p>
        </div>
        <div class="card-body">
            <?php if(!empty($error_message)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="form-floating">
                    <input type="email" class="form-control" id="loginEmail" name="loginEmail" 
                           placeholder="Email" required>
                    <label for="loginEmail"><i class="fas fa-envelope me-2"></i>Email address</label>
                </div>

                <div class="form-floating position-relative">
                    <input type="password" class="form-control" id="loginPassword" name="loginPassword" 
                           placeholder="Password" required>
                    <label for="loginPassword"><i class="fas fa-key me-2"></i>Password</label>
                    <span class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </span>
                </div>

                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </button>
            </form>

            <div class="register-link">
                Don't have an account? <a href="register.php">Register here</a>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('loginPassword');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
</script>

</body>
</html>