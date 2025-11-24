<?php
session_start();
include_once 'database.php';

// Check if user is logged in
$loggedIn = isset($_SESSION['user_id']);
$userName = $loggedIn ? $_SESSION['username'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livestock Ownership Database</title>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js" defer></script>
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
                    <li><a href="#" id="dashboard-link">Dashboard</a></li>
                    <li><a href="#" id="add-livestock-btn">Add Livestock</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="logout.php">Logout (<?php echo $userName; ?>)</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="signup.php">Sign Up</a></li>
                    <li><a href="contact.php">Contact</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="hero-content">
                <h2>Efficient Livestock Management System</h2>
                <p>Track, register, and manage your livestock data in one centralized platform</p>
                <?php if(!$loggedIn): ?>
                    <a href="signup.php" class="btn">Get Started</a>
                <?php else: ?>
                    <a href="#" id="dashboard-btn" class="btn">Go to Dashboard</a>
                <?php endif; ?>
            </div>
        </section>

        <?php if($loggedIn): ?>
            <section id="dashboard" class="dashboard">
                <h2>Your Livestock Dashboard</h2>
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <h3>Total Animals</h3>
                        <p id="total-count">Loading...</p>
                    </div>
                    <div class="stat-card">
                        <h3>By Species</h3>
                        <div id="species-breakdown">Loading...</div>
                    </div>
                    <div class="stat-card">
                        <h3>Recent Activity</h3>
                        <div id="recent-activity">Loading...</div>
                    </div>
                </div>
                
                <div class="livestock-management">
                    <h3>Manage Your Livestock</h3>
                    <div class="filter-controls">
                        <select id="species-filter">
                            <option value="all">All Species</option>
                            <option value="cattle">Cattle</option>
                            <option value="sheep">Sheep</option>
                            <option value="goat">Goat</option>
                            <option value="poultry">Poultry</option>
                            <option value="pig">Pig</option>
                            <option value="other">Other</option>
                        </select>
                        <input type="text" id="search-livestock" placeholder="Search by ID or name">
                    </div>
                    
                    <div class="livestock-table-container">
                        <table id="livestock-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Species</th>
                                    <th>Breed</th>
                                    <th>Gender</th>
                                    <th>Birth Date</th>
                                    <th>Health Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="livestock-data">
                                <!-- Livestock data will be loaded here via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Add Livestock Modal -->
            <div id="add-livestock-modal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Add New Livestock</h2>
                    <form id="add-livestock-form">
                        <div class="form-group">
                            <label for="species">Species:</label>
                            <select id="species" name="species" required>
                                <option value="">Select Species</option>
                                <option value="cattle">Cattle</option>
                                <option value="sheep">Sheep</option>
                                <option value="goat">Goat</option>
                                <option value="poultry">Poultry</option>
                                <option value="pig">Pig</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="breed">Breed:</label>
                            <input type="text" id="breed" name="breed" required>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender:</label>
                            <select id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="birthdate">Birth Date:</label>
                            <input type="date" id="birthdate" name="birthdate" required>
                        </div>
                        <div class="form-group">
                            <label for="health">Health Status:</label>
                            <select id="health" name="health" required>
                                <option value="">Select Status</option>
                                <option value="healthy">Healthy</option>
                                <option value="sick">Sick</option>
                                <option value="recovering">Recovering</option>
                                <option value="quarantined">Quarantined</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="notes">Additional Notes:</label>
                            <textarea id="notes" name="notes"></textarea>
                        </div>
                        <button type="submit" class="btn">Add Livestock</button>
                    </form>
                </div>
            </div>

            <!-- Edit Livestock Modal -->
            <div id="edit-livestock-modal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Edit Livestock</h2>
                    <form id="edit-livestock-form">
                        <input type="hidden" id="edit-id" name="id">
                        <div class="form-group">
                            <label for="edit-species">Species:</label>
                            <select id="edit-species" name="species" required>
                                <option value="">Select Species</option>
                                <option value="cattle">Cattle</option>
                                <option value="sheep">Sheep</option>
                                <option value="goat">Goat</option>
                                <option value="poultry">Poultry</option>
                                <option value="pig">Pig</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit-breed">Breed:</label>
                            <input type="text" id="edit-breed" name="breed" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-gender">Gender:</label>
                            <select id="edit-gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit-birthdate">Birth Date:</label>
                            <input type="date" id="edit-birthdate" name="birthdate" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-health">Health Status:</label>
                            <select id="edit-health" name="health" required>
                                <option value="">Select Status</option>
                                <option value="healthy">Healthy</option>
                                <option value="sick">Sick</option>
                                <option value="recovering">Recovering</option>
                                <option value="quarantined">Quarantined</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit-notes">Additional Notes:</label>
                            <textarea id="edit-notes" name="notes"></textarea>
                        </div>
                        <button type="submit" class="btn">Update Livestock</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <section class="features">
            <h2>Key Features</h2>
            <div class="feature-cards">
                <div class="feature-card">
                    <div class="icon">📊</div>
                    <h3>Comprehensive Tracking</h3>
                    <p>Record and monitor all your livestock data in one centralized location.</p>
                </div>
                <div class="feature-card">
                    <div class="icon">🔄</div>
                    <h3>Real-time Updates</h3>
                    <p>Get up-to-date information on livestock status, health, and more.</p>
                </div>
                <div class="feature-card">
                    <div class="icon">📱</div>
                    <h3>Accessible Anywhere</h3>
                    <p>Access your data from any device, anytime, anywhere.</p>
                </div>
                <div class="feature-card">
                    <div class="icon">📈</div>
                    <h3>Data Analysis</h3>
                    <p>Generate reports and insights to optimize your livestock management.</p>
                </div>
            </div>
        </section>

        <section class="about">
            <h2>About the Livestock Ownership Database</h2>
            <div class="about-content">
                <div class="about-text">
                    <p>The Livestock Ownership Database is a comprehensive solution designed to address the lack of centralized information on livestock ownership. Our platform enables efficient tracking, registration, and management of animal resources, supporting better planning and implementation of livestock-related schemes.</p>
                    <p>With our system, farmers, ranchers, and agricultural organizations can maintain accurate records of their livestock, track health status, manage breeding programs, and generate valuable insights for improved decision-making.</p>
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