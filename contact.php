<?php
session_start();
include_once 'database.php';

$error = "";
$success = "";

// Check if user is logged in
$loggedIn = isset($_SESSION['user_id']);
$userName = $loggedIn ? $_SESSION['username'] : '';
$userEmail = $loggedIn ? $_SESSION['email'] : '';

// Process contact form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $subject = sanitizeInput($_POST['subject']);
    $message = sanitizeInput($_POST['message']);
    
    // Validate input
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Insert message into database
        $sql = "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $subject, $message);
        
        if ($stmt->execute()) {
            $success = "Your message has been sent successfully. We will contact you shortly.";
            
            // Clear form data after successful submission
            $name = $email = $subject = $message = "";
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Livestock Ownership Database</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo">
            <h1>Livestock Ownership Database</h1>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <?php if($loggedIn): ?>
                    <li><a href="index.php#dashboard">Dashboard</a></li>
                    <li><a href="contact.php" class="active">Contact</a></li>
                    <li><a href="logout.php">Logout (<?php echo $userName; ?>)</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="signup.php">Sign Up</a></li>
                    <li><a href="contact.php" class="active">Contact</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <section class="contact-section">
            <div class="contact-container">
                <div class="contact-info">
                    <h2>Get in Touch</h2>
                    <p>Have questions about our livestock database system? Need help with your account? Or just want to provide feedback? Use the form below to contact us.</p>
                    
                    <div class="contact-details">
                        <div class="contact-item">
                            <div class="icon">📧</div>
                            <div class="detail">
                                <h3>Email Us</h3>
                                <p>info@livestockdb.com</p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="icon">📞</div>
                            <div class="detail">
                                <h3>Call Us</h3>
                                <p>+1 (555) 123-4567</p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="icon">🏢</div>
                            <div class="detail">
                                <h3>Visit Us</h3>
                                <p>123 Farm Road, Agricultural District, Country</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="contact-form">
                    <h2>Send a Message</h2>
                    
                    <?php if (!empty($error)): ?>
                        <div class="error-message"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success)): ?>
                        <div class="success-message"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="form-group">
                            <label for="name">Your Name:</label>
                            <input type="text" id="name" name="name" value="<?php echo $loggedIn ? $userName : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Your Email:</label>
                            <input type="email" id="email" name="email" value="<?php echo $loggedIn ? $userEmail : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Subject:</label>
                            <input type="text" id="subject" name="subject" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message:</label>
                            <textarea id="message" name="message" rows="6" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn">Send Message</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-logo">
                <h3>Livestock Ownership Database</h3>
                <p>Simplifying livestock management for everyone</p>
            </div>
            <div class="footer-links">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <?php if(!$loggedIn): ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="signup.php">Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="footer-contact">
                <h4>Contact Us</h4>
                <p>Email: info@livestockdb.com</p>
                <p>Phone: +91 8279560807</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 Livestock Ownership Database. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>