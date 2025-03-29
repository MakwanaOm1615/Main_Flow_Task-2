<?php 
session_start(); 
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Assuming you store these details during signup
$username = $_SESSION['user'];
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@example.com';
$joinDate = isset($_SESSION['join_date']) ? $_SESSION['join_date'] : date("F j, Y");
$lastLogin = isset($_SESSION['last_login']) ? $_SESSION['last_login'] : date("F j, Y, g:i a");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoEnthusiast Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        /* Navbar Styles */
        .navbar {
            background: linear-gradient(90deg, #1e3c72, #2a5298);
            padding: 1rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .logo i {
            margin-right: 0.5rem;
            color: #ffcc00;
        }
        
        .nav-links {
            display: flex;
            gap: 2rem;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            padding-bottom: 5px;
        }
        
        .nav-links a::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #ffcc00;
            transition: width 0.3s ease;
        }
        
        .nav-links a:hover::after {
            width: 100%;
        }
        
        .nav-links a:hover {
            color: #ffcc00;
        }
        
        .user-actions {
            display: flex;
            align-items: center;
        }
        
        .user-icon {
            background: linear-gradient(135deg, #ffcc00, #ff9500);
            color: #1e3c72;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.2rem;
            cursor: pointer;
            transition: transform 0.3s ease;
            box-shadow: 0 2px 10px rgba(255, 204, 0, 0.3);
        }
        
        .user-icon:hover {
            transform: scale(1.1);
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(25, 47, 89, 0.7), rgba(33, 63, 116, 0.7)), url('https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80') no-repeat center center/cover;
            height: 70vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            padding: 0 5%;
            position: relative;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(25, 47, 89, 0.6) 0%, rgba(19, 37, 71, 0.8) 100%);
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 900px;
        }
        
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            animation: fadeInDown 1s ease-out;
        }
        
        .hero p {
            font-size: 1.3rem;
            max-width: 700px;
            margin: 0 auto 2.5rem;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 1s ease-out;
        }
        
        .cta-btn {
            background: linear-gradient(135deg, #e63946, #d62839);
            color: white;
            border: none;
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            font-weight: bold;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(230, 57, 70, 0.4);
            animation: fadeInUp 1.2s ease-out;
            position: relative;
            overflow: hidden;
        }
        
        .cta-btn::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: all 0.5s ease;
        }
        
        .cta-btn:hover::before {
            left: 100%;
        }
        
        .cta-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(230, 57, 70, 0.5);
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Main Content */
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 3rem auto;
        }
        
        /* Profile Modal */
        .profile-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .profile-content {
            background-color: white;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            padding: 2.5rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            position: relative;
        }
        
        .close-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
            transition: all 0.3s ease;
        }
        
        .close-btn:hover {
            color: #e63946;
            transform: scale(1.1);
        }
        
        .profile-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        /* Profile icon instead of avatar */
        .profile-icon {
            font-size: 5rem;
            color: #1e3c72;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .profile-name {
            font-size: 1.6rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: #1e3c72;
        }
        
        .profile-email {
            color: #666;
            font-size: 0.95rem;
        }
        
        .profile-stats {
            border-top: 1px solid #eee;
            padding-top: 1.5rem;
        }
        
        .stat-item {
            margin-bottom: 1.2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
        }
        
        .stat-label i {
            margin-right: 10px;
            color: #1e3c72;
            font-size: 1.1rem;
        }
        
        .stat-value {
            font-weight: 600;
            color: #333;
        }
        
        .logout-btn {
            display: block;
            background: linear-gradient(135deg, #e63946, #d62839);
            color: white;
            text-decoration: none;
            padding: 0.8rem 1.5rem;
            font-weight: 500;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 3px 8px rgba(230, 57, 70, 0.3);
            text-align: center;
            margin-top: 2rem;
        }
        
        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(230, 57, 70, 0.4);
        }
        
        /* Dashboard Content */
        .dashboard-content {
            background-color: white;
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }
        
        .content-header {
            margin-bottom: 2.5rem;
            position: relative;
            padding-bottom: 1rem;
        }
        
        .content-header::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, #1e3c72, #2a5298);
        }
        
        .content-header h2 {
            font-size: 1.8rem;
            margin-bottom: 0.8rem;
            color: #1e3c72;
        }
        
        .content-header p {
            color: #666;
            font-size: 1.05rem;
        }
        
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.8rem;
        }
        
        .card {
            background-color: #f9f9f9;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            position: relative;
        }
        
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .card-img {
            height: 180px;
            overflow: hidden;
            position: relative;
        }
        
        .card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .card:hover .card-img img {
            transform: scale(1.05);
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .card-icon {
            font-size: 2rem;
            color: #1e3c72;
            margin-bottom: 1rem;
        }
        
        .card h3 {
            font-size: 1.3rem;
            margin-bottom: 0.8rem;
            color: #1e3c72;
        }
        
        .card p {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 1.2rem;
            line-height: 1.5;
        }
        
        .card-btn {
            display: inline-block;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            text-decoration: none;
            padding: 0.6rem 1.2rem;
            font-weight: 500;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 3px 8px rgba(30, 60, 114, 0.3);
        }
        
        .card-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 60, 114, 0.4);
        }
        
        /* Footer */
        .footer {
            background: linear-gradient(135deg, #172b46, #1a1a1a);
            color: white;
            padding: 3rem 5% 2rem;
            position: relative;
        }
        
        .footer::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #e63946, #ffcc00);
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
        }
        
        .footer-section h3 {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.8rem;
            color: #fff;
        }
        
        .footer-section h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 2px;
            background-color: #e63946;
        }
        
        .footer-section p {
            color: #bbb;
            margin-bottom: 1.5rem;
            line-height: 1.7;
        }
        
        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .social-icon {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }
        
        .social-icon:hover {
            background-color: #e63946;
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(230, 57, 70, 0.3);
        }
        
        .footer-bottom {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: #aaa;
            font-size: 0.9rem;
        }
        
        /* Car Animation */
        @keyframes drive {
            0% {
                transform: translateX(-100%) scale(0.7);
            }
            100% {
                transform: translateX(100vw) scale(0.7);
            }
        }
        
        .car-animation {
            position: absolute;
            bottom: 10%;
            font-size: 2.5rem;
            color: rgba(255, 255, 255, 0.8);
            animation: drive 15s linear infinite;
            z-index: 1;
            filter: drop-shadow(0 0 10px rgba(255, 204, 0, 0.5));
        }
        
        /* Responsive Styles */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                padding: 1rem;
            }
            
            .nav-links {
                margin: 1rem 0;
                flex-direction: column;
                gap: 1rem;
                text-align: center;
                width: 100%;
            }
            
            .hero {
                height: 70vh;
            }
            
            .hero h1 {
                font-size: 2.2rem;
            }
            
            .hero p {
                font-size: 1.1rem;
            }
            
            .card-grid {
                grid-template-columns: 1fr;
            }
            
            .footer-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
        }
        
        /* Additional Styles for Enhanced UI */
        .image-banner {
            display: flex;
            margin: 3rem auto;
            max-width: 1200px;
            width: 90%;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .image-banner img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .image-banner:hover img {
            transform: scale(1.02);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <a href="#" class="logo"><i class="fas fa-car"></i> AutoEnthusiast</a>
        <div class="nav-links">
            <a href="#"><i class="fas fa-home"></i> Home</a>
            <a href="#"><i class="fas fa-car-side"></i> Car Reviews</a>
            <a href="#"><i class="fas fa-tools"></i> Maintenance</a>
            <a href="#"><i class="fas fa-map-marked-alt"></i> Road Trips</a>
        </div>
        <div class="user-actions">
            <div class="user-icon" id="userIcon">
                <i class="fas fa-user"></i>
            </div>
        </div>
    </nav>

    <!-- Profile Modal -->
    <div class="profile-modal" id="profileModal">
        <div class="profile-content">
            <div class="close-btn" id="closeProfileModal">
                <i class="fas fa-times"></i>
            </div>
            <div class="profile-header">
                <!-- Replaced avatar div with an icon -->
                <div class="profile-icon">
                    <i class="fas fa-user-circle"></i>
                </div>
                <h2 class="profile-name"><?php echo htmlspecialchars($username); ?></h2>
            </div>
            <div class="profile-stats">
                <div class="stat-item">
                    <span class="stat-label"><i class="fas fa-calendar-alt"></i> Member Since</span>
                    <span class="stat-value"><?php echo htmlspecialchars($joinDate); ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label"><i class="fas fa-clock"></i> Last Login</span>
                    <span class="stat-value"><?php echo htmlspecialchars($lastLogin); ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label"><i class="fas fa-car"></i> Cars Owned</span>
                    <span class="stat-value">3</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label"><i class="fas fa-comments"></i> Forum Posts</span>
                    <span class="stat-value">42</span>
                </div>
            </div>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-overlay"></div>
        <div class="car-animation"><i class="fas fa-car-side"></i></div>
        <div class="hero-content">
            <h1>Welcome back, <?php echo htmlspecialchars($username); ?>!</h1>
            <p>Your dashboard for everything automotive. Discover new cars, maintenance tips, and connect with fellow car enthusiasts.</p>
            <button class="cta-btn"><i class="fas fa-search"></i> Explore Cars</button>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container">
        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <div class="content-header">
                <h2>Your Automotive Dashboard</h2>
                <p>Stay up to date with car news, maintenance reminders, and more</p>
            </div>
            <div class="card-grid">
                <div class="card">
                    <div class="card-img">
                        <img src="https://images.unsplash.com/photo-1494976388531-d1058494cdd8?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" alt="Latest News">
                    </div>
                    <div class="card-body">
                        <h3>Latest News</h3>
                        <p>Stay updated with the latest automotive news and industry trends.</p>
                        <a href="#" class="card-btn">Read News</a>
                    </div>
                </div>
                <div class="card">
                    <div class="card-img">
                        <img src="https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" alt="Car Reviews">
                    </div>
                    <div class="card-body">
                        <h3>Car Reviews</h3>
                        <p>Read expert reviews and owner experiences of the latest models.</p>
                        <a href="#" class="card-btn">View Reviews</a>
                    </div>
                </div>
                <div class="card">
                    <div class="card-img">
                        <img src="https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" alt="Maintenance Log">
                    </div>
                    <div class="card-body">
                        <h3>Maintenance Log</h3>
                        <p>Track your vehicle maintenance history and upcoming service needs.</p>
                        <a href="#" class="card-btn">Open Log</a>
                    </div>
                </div>
                <div class="card">
                    <div class="card-img">
                        <img src="https://images.unsplash.com/photo-1527786356703-4b100091cd2c?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" alt="Road Trip Planner">
                    </div>
                    <div class="card-body">
                        <h3>Road Trip Planner</h3>
                        <p>Plan your next adventure with routes, stops, and points of interest.</p>
                        <a href="#" class="card-btn">Plan Trip</a>
                    </div>
                </div>
                <div class="card">
                    <div class="card-img">
                        <img src="https://images.unsplash.com/photo-1517994112540-009c47ea476b?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" alt="Car Clubs">
                    </div>
                    <div class="card-body">
                        <h3>Car Clubs</h3>
                        <p>Connect with fellow enthusiasts in your area who share your passion.</p>
                        <a href="#" class="card-btn">Find Clubs</a>
                    </div>
                </div>
                <div class="card">
                    <div class="card-img">
                        <img src="https://images.unsplash.com/photo-1518987048-93e29699e79a?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" alt="Marketplace">
                    </div>
                    <div class="card-body">
                        <h3>Marketplace</h3>
                        <p>Buy, sell, or trade cars, parts, and accessories with our community.</p>
                        <a href="#" class="card-btn">Visit Marketplace</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Banner -->
    <div class="image-banner">
        <img src="https://images.unsplash.com/photo-1493238792000-8113da705763?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Featured car show">
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>About AutoEnthusiast</h3>
                <p>AutoEnthusiast is the premier platform for car lovers, offering expert reviews, maintenance guides, and a thriving community of automotive enthusiasts.</p>
                <div class="social-links">
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h3>Contact Us</h3>
                <p><i class="fas fa-map-marker-alt"></i> 123 Automotive Ave, Motorville</p>
                <p><i class="fas fa-phone"></i> +1 (555) 123-4567</p>
                <p><i class="fas fa-envelope"></i> info@autoenthusiast.com</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> AutoEnthusiast. All rights reserved.</p>
        </div>
    </footer>

    <!-- JavaScript for Modal -->
    <script>
        // Get the modal elements
        const userIcon = document.getElementById('userIcon');
        const profileModal = document.getElementById('profileModal');
        const closeProfileModal = document.getElementById('closeProfileModal');
        
        // Show modal when user icon is clicked
        userIcon.addEventListener('click', function() {
            profileModal.style.display = 'flex';
            document.body.style.overflow = 'hidden'; // Prevent scrolling when modal is open
        });
        
        // Close modal when close button is clicked
        closeProfileModal.addEventListener('click', function() {
            profileModal.style.display = 'none';
            document.body.style.overflow = 'auto'; // Allow scrolling again
        });
        
        // Close modal when clicking outside of it
        window.addEventListener('click', function(event) {
            if (event.target === profileModal) {
                profileModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    </script>
</body>
</html>