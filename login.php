<?php 
session_start(); 
include "config.php";  

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format";
    } else {
        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['user'] = $row['username'];
                $_SESSION['email'] = $row['email'];
                header("Location:dashboard.php");
                exit();
            } else {
                $error_message = "Invalid password. Please try again.";
            }
        } else {
            $error_message = "User not found. Please check your email or register.";
        }
    }
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoEnthusiast - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            color: #333;
            line-height: 1.6;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 20px;
        }
        
        .logo {
            font-size: 2.5rem;
            font-weight: bold;
            color: #1e3c72;
            text-decoration: none;
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .logo i {
            margin-right: 0.5rem;
            color: #ffcc00;
            font-size: 2.8rem;
        }
        
        .login-container {
            background-color: white;
            border-radius: 12px;
            width: 100%;
            max-width: 450px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .login-container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #1e3c72, #2a5298);
        }
        
        .login-container h2 {
            font-size: 1.8rem;
            margin-bottom: 2rem;
            color: #1e3c72;
            position: relative;
            padding-bottom: 1rem;
        }
        
        .login-container h2::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, #1e3c72, #2a5298);
        }
        
        .input-group {
            position: relative;
            margin-bottom: 1.8rem;
        }
        
        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #1e3c72;
            font-size: 1.2rem;
        }
        
        .form-input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 1px solid #ddd;
            border-radius: 50px;
            font-size: 1rem;
            transition: all 0.3s ease;
            outline: none;
        }
        
        .form-input:focus {
            border-color: #1e3c72;
            box-shadow: 0 0 10px rgba(30, 60, 114, 0.2);
        }
        
        .submit-btn {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            border: none;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: bold;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(30, 60, 114, 0.4);
            width: 100%;
            margin-top: 1rem;
        }
        
        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(30, 60, 114, 0.5);
        }
        
        .signup-link {
            margin-top: 2rem;
            color: #666;
            font-size: 0.95rem;
        }
        
        .signup-link a {
            color: #1e3c72;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }
        
        .signup-link a:hover {
            color: #e63946;
        }
        
        .create-account-btn {
            display: inline-block;
            background: #f5f7fa;
            color: #1e3c72;
            font-weight: bold;
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            margin-top: 1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 2px solid #1e3c72;
            font-size: 0.95rem;
            width: 100%;
        }
        
        .create-account-btn:hover {
            background: #1e3c72;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(30, 60, 114, 0.3);
        }
        
        .error-message {
            background-color: rgba(230, 57, 70, 0.1);
            color: #e63946;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-left: 4px solid #e63946;
        }
        
        .error-message i {
            margin-right: 10px;
            font-size: 1.1rem;
        }
        
        .social-login {
            margin-top: 2rem;
            border-top: 1px solid #eee;
            padding-top: 1.5rem;
        }
        
        .social-login p {
            margin-bottom: 1rem;
            color: #666;
            font-size: 0.95rem;
        }
        
        .social-icons {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }
        
        .social-btn {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background-color: #f5f7fa;
            color: #1e3c72;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }
        
        .social-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }
        
        .facebook {
            color: #3b5998;
        }
        
        .google {
            color: #db4437;
        }
        
        .twitter {
            color: #1da1f2;
        }
        
        .remember-group {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            color: #666;
            font-size: 0.9rem;
        }
        
        .remember-me input {
            margin-right: 8px;
        }
        
        .forgot-password {
            color: #1e3c72;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        
        .forgot-password:hover {
            color: #e63946;
        }
        
        /* Responsive Styles */
        @media (max-width: 500px) {
            .login-container {
                padding: 2rem 1.5rem;
            }
            
            .logo {
                font-size: 2rem;
            }
            
            .logo i {
                font-size: 2.3rem;
            }
        }
    </style>
</head>
<body>
    
    <!-- Logo -->
    <a href="dashboard.php" class="logo"><i class="fas fa-car"></i> AutoEnthusiast</a>
    
    <!-- Login Container -->
    <div class="login-container">
        <h2>Welcome Back</h2>
        
        <?php if (!empty($error_message)): ?>
        <div class="error-message">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
        </div>
        <?php endif; ?>
        
        <form action="login.php" method="POST">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" class="form-input" placeholder="Email Address" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" class="form-input" placeholder="Password" required>
            </div>
            
            <div class="remember-group">
                <label class="remember-me">
                    <input type="checkbox" name="remember"> Remember me
                </label>
                <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
            </div>


            <form action="dashboard.php" method="POST">
    <button type="submit" class="submit-btn">
        <i class="fas fa-sign-in-alt"></i> Login
    </button>
</form>

        
        <p class="signup-link">Don't have an account? <a href="signup.php">Sign Up</a></p>
    </div>
    
    <script>
        // Show/hide password functionality
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.createElement('i');
            togglePassword.className = '';
            togglePassword.style.position = 'absolute';
            togglePassword.style.right = '15px';
            togglePassword.style.top = '50%';
            togglePassword.style.transform = 'translateY(-50%)';
            togglePassword.style.cursor = 'pointer';
            togglePassword.style.color = '#1e3c72';
            
            const passwordInput = document.querySelector('input[name="password"]');
            const passwordGroup = passwordInput.parentNode;
            passwordGroup.appendChild(togglePassword);
            
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.className = type === 'password' ? 'fas fa-eye-slash' : 'fas fa-eye';
            });
        });
    </script>
</body>
</html>