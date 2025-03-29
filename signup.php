<?php 
include "config.php";

$error_message = "";
$success_message = "";



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    
    // Validate username (alphanumeric, 3-20 chars)
    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $error_message = "Username must be 3-20 characters and can only contain letters, numbers, and underscores.";
    }
    // Validate email format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    }
    // Validate password (at least 8 chars, with numbers and letters)
    elseif (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error_message = "Password must be at least 8 characters and include both letters and numbers.";
    }
    // Validate matching passwords
    elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    }
    else {
        // Check if email already exists
        $check_stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error_message = "Email already exists. Please use a different email or  <a href='login.php' style='color:#1e3c72;font-weight:bold;'>login</a>.";
        } else {
            // Check if username already exists
            $check_username = $conn->prepare("SELECT * FROM users WHERE username = ?");
            $check_username->bind_param("s", $username);
            $check_username->execute();
            $result_username = $check_username->get_result();
            
            if ($result_username->num_rows > 0) {
                $error_message = "Username already taken. Please choose a different username.";
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Use prepared statement to prevent SQL injection$stmt = $conn->prepare("INSERT INTO users (username, email, password, join_date) VALUES (?, ?, ?, NOW())");
                $stmt->bind_param("sss", $username, $email, $hashed_password);
                
                if ($stmt->execute()) {
                    $success_message = "Signup successful! You can now login to your account.";
                } else {
                    $error_message = "Error: " . $stmt->error;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoEnthusiast - Create Account</title>
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
            min-height: 100vh;
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
        
        .signup-container {
            background-color: white;
            border-radius: 12px;
            width: 100%;
            max-width: 500px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .signup-container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #1e3c72, #2a5298);
        }
        
        .signup-container h2 {
            font-size: 1.8rem;
            margin-bottom: 2rem;
            color: #1e3c72;
            position: relative;
            padding-bottom: 1rem;
        }
        
        .signup-container h2::after {
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
            margin-bottom: 1.5rem;
            text-align: left;
        }
        
        .input-group i {
            position: absolute;
            left: 15px;
            top: 68%;
            transform: translateY(-50%);
            color: #1e3c72;
            font-size: 1.2rem;
        }
        
        .input-label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.9rem;
            color: #555;
            font-weight: 500;
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
        
        .password-strength {
            height: 5px;
            background-color: #eee;
            border-radius: 5px;
            margin-top: 10px;
            position: relative;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0;
            background: linear-gradient(90deg, #ff4d4d, #ffcc00, #4CAF50);
            transition: width 0.3s ease;
        }
        
        .password-strength-text {
            font-size: 0.8rem;
            color: #666;
            margin-top: 5px;
            text-align: right;
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
        
        .login-link {
            margin-top: 1.5rem;
            color: #666;
            font-size: 0.95rem;
        }
        
        .login-link a {
            color: #1e3c72;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }
        
        .login-link a:hover {
            color: #e63946;
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
            text-align: left;
            border-left: 4px solid #e63946;
        }
        
        .error-message i {
            margin-right: 10px;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        
        .success-message {
            background-color: rgba(76, 175, 80, 0.1);
            color: #4CAF50;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            border-left: 4px solid #4CAF50;
        }
        
        .success-message i {
            margin-right: 10px;
            font-size: 1.1rem;
        }
        
        .terms {
            margin-top: 1.5rem;
            font-size: 0.85rem;
            color: #666;
            text-align: center;
        }
        
        .terms a {
            color: #1e3c72;
            text-decoration: none;
            font-weight: 500;
        }
        
        .terms a:hover {
            text-decoration: underline;
        }
        
        .social-signup {
            margin-top: 2rem;
            border-top: 1px solid #eee;
            padding-top: 1.5rem;
        }
        
        .social-signup p {
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
        
        /* Responsive Styles */
        @media (max-width: 600px) {
            .signup-container {
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
    <a href="index.php" class="logo"><i class="fas fa-car"></i> AutoEnthusiast</a>
    
    <!-- Signup Container -->
    <div class="signup-container">
        <h2>Create Your Account</h2>
        
        <?php if (!empty($error_message)): ?>
        <div class="error-message">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($success_message)): ?>
        <div class="success-message">
            <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
        </div>
        <?php endif; ?>
        
        <?php if (empty($success_message)): ?>
        <form action="signup.php" method="POST" id="signupForm">
            <div class="input-group">
                <label class="input-label">Username</label>
                <i class="fas fa-user"></i>
                <input type="text" name="username" class="form-input" placeholder="Choose a username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
            
            <div class="input-group">
                <label class="input-label">Email Address</label>
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" class="form-input" placeholder="Your email address" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="input-group">
                <label class="input-label">Password</label>
               
                <input type="password" name="password" id="password" class="form-input" placeholder="Create a password" required>
                <div class="password-strength">
                    <div class="password-strength-bar" id="passwordStrengthBar"></div>
                </div>
                <div class="password-strength-text" id="passwordStrengthText">Password strength</div>
            </div>
            
            <div class="input-group">
                <label class="input-label">Confirm Password</label>
                <i class="fas fa-lock"></i>
                <input type="password" name="confirm_password" class="form-input" placeholder="Confirm your password" required>
            </div>
            
            <button type="submit" class="submit-btn">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
            
            <p class="terms">
                By signing up, you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
            </p>
        </form>
        
        <div class="social-signup">
            <p>Or sign up with</p>
            <div class="social-icons">
                <a href="#" class="social-btn facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-btn google"><i class="fab fa-google"></i></a>
                <a href="#" class="social-btn twitter"><i class="fab fa-twitter"></i></a>
            </div>
        </div>
        <?php endif; ?>
        
        <p class="login-link">Login here<a href="login.php">Login</a></p>
    </div>
    
    <script>
        // Password strength meter
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const strengthBar = document.getElementById('passwordStrengthBar');
            const strengthText = document.getElementById('passwordStrengthText');
            
            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    let strength = 0;
                    
                    // Length check
                    if (password.length >= 8) {
                        strength += 1;
                    }
                    
                    // Contains lowercase letters
                    if (/[a-z]/.test(password)) {
                        strength += 1;
                    }
                    
                    // Contains uppercase letters
                    if (/[A-Z]/.test(password)) {
                        strength += 1;
                    }
                    
                    // Contains numbers
                    if (/[0-9]/.test(password)) {
                        strength += 1;
                    }
                    
                    // Contains special characters
                    if (/[^A-Za-z0-9]/.test(password)) {
                        strength += 1;
                    }
                    
                    // Update strength bar
                    let percent = (strength / 5) * 100;
                    strengthBar.style.width = percent + '%';
                    
                    // Update strength text
                    if (password.length === 0) {
                        strengthText.textContent = 'Password strength';
                        strengthBar.style.background = '#eee';
                    } else if (strength < 2) {
                        strengthText.textContent = 'Weak';
                        strengthBar.style.background = '#ff4d4d';
                    } else if (strength < 4) {
                        strengthText.textContent = 'Medium';
                        strengthBar.style.background = '#ffcc00';
                    } else {
                        strengthText.textContent = 'Strong';
                        strengthBar.style.background = '#4CAF50';
                    }
                });
            }
            
            // Show/hide password functionality
            const togglePassword = document.createElement('i');
            togglePassword.className = 'fas fa-eye-slash';
            togglePassword.style.position = 'absolute';
            togglePassword.style.right = '15px';
            togglePassword.style.top = '50%';
            togglePassword.style.transform = 'translateY(-50%)';
            togglePassword.style.cursor = 'pointer';
            togglePassword.style.color = '#1e3c72';
            
            if (passwordInput) {
                const passwordGroup = passwordInput.parentNode;
                passwordGroup.style.position = 'relative';
                passwordGroup.appendChild(togglePassword);
                
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.className = type === 'password' ? 'fas fa-eye-slash' : 'fas fa-eye';
                });
            }
            
            // Form validation
            const form = document.getElementById('signupForm');
            if (form) {
                form.addEventListener('submit', function(event) {
                    const username = form.querySelector('input[name="username"]').value;
                    const email = form.querySelector('input[name="email"]').value;
                    const password = form.querySelector('input[name="password"]').value;
                    const confirmPassword = form.querySelector('input[name="confirm_password"]').value;
                    
                    let isValid = true;
                    let errorMessage = '';
                    
                    // Username validation
                    if (!/^[a-zA-Z0-9_]{3,20}$/.test(username)) {
                        errorMessage = 'Username must be 3-20 characters and can only contain letters, numbers, and underscores.';
                        isValid = false;
                    }
                    // Email validation
                    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                        errorMessage = 'Please enter a valid email address.';
                        isValid = false;
                    }
                    // Password validation
                    else if (password.length < 8) {
                        errorMessage = 'Password must be at least 8 characters long.';
                        isValid = false;
                    }
                    else if (!/[A-Za-z]/.test(password) || !/[0-9]/.test(password)) {
                        errorMessage = 'Password must contain both letters and numbers.';
                        isValid = false;
                    }
                    // Password matching
                    else if (password !== confirmPassword) {
                        errorMessage = 'Passwords do not match.';
                        isValid = false;
                    }
                    
                    // If validation fails, prevent form submission and show error
                    if (!isValid) {
                        event.preventDefault();
                        
                        // Create error message element if it doesn't exist
                        let errorDiv = document.querySelector('.error-message');
                        if (!errorDiv) {
                            errorDiv = document.createElement('div');
                            errorDiv.className = 'error-message';
                            errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> <span></span>';
                            form.parentNode.insertBefore(errorDiv, form);
                        }
                        
                        // Update error message
                        const errorSpan = errorDiv.querySelector('span');
                        if (errorSpan) {
                            errorSpan.textContent = errorMessage;
                        }
                        
                        // Scroll to error message
                        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            }
        });
    </script>
</body>
</html>