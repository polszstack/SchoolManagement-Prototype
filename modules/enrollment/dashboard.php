<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestlink College of the Philippines</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f4f4f4;
        }

        /* Header Styles */
        .top-header {
            background-color: #003366;
            color: white;
            padding: 8px 20px;
            font-size: 14px;
        }

        .main-header {
            background-color: #004080;
            padding: 15px 20px;
            color: white;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .logo {
            width: 100px;
            height: 100px;
            background: linear-gradient(145deg, #003366, #ffd700);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            text-align: center;
            padding: 5px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            border: 3px solid #ffd700;
        }

        .logo .year {
            font-size: 16px;
            color: #ffd700;
            border-bottom: 1px solid #ffd700;
            padding-bottom: 2px;
            margin-bottom: 2px;
        }

        .logo .bestlink {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 1px;
            color: white;
            text-transform: uppercase;
        }

        .logo .college {
            font-size: 12px;
            color: #ffd700;
            text-transform: uppercase;
        }

        .logo .of {
            font-size: 10px;
            color: white;
        }

        .school-name h1 {
            font-size: 36px;
            margin-bottom: 5px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .school-name p {
            font-style: italic;
            font-size: 16px;
            color: #ffd700;
        }

        /* Navigation */
        nav {
            background-color: #ffd700;
            padding: 15px 20px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        nav ul {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
            justify-content: center;
        }

        nav ul li {
            font-weight: bold;
            color: #003366;
            cursor: pointer;
            font-size: 15px;
            padding: 5px 10px;
            border-radius: 5px;
            transition: all 0.3s;
        }

        nav ul li:hover {
            background-color: #003366;
            color: #ffd700;
            transform: translateY(-2px);
        }

        nav ul li:after {
            content: '▼';
            font-size: 10px;
            margin-left: 5px;
            color: #003366;
            opacity: 0.7;
        }

        nav ul li:hover:after {
            color: #ffd700;
        }

        /* Main Banner */
        .banner {
            background: linear-gradient(rgba(0, 51, 102, 0.85), rgba(0, 64, 128, 0.85)), 
                        url('https://placehold.co/1200x400/003366/ffd700?text=Bestlink+College+Campus') center/cover;
            color: white;
            padding: 80px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .banner::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 10px,
                rgba(255, 215, 0, 0.1) 10px,
                rgba(255, 215, 0, 0.1) 20px
            );
            animation: moveStripes 20s linear infinite;
        }

        @keyframes moveStripes {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        .banner h2 {
            font-size: 48px;
            margin-bottom: 20px;
            position: relative;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .banner p {
            font-size: 20px;
            max-width: 800px;
            margin: 0 auto;
            position: relative;
        }

        .enroll-btn {
            background-color: #ffd700;
            color: #003366;
            border: none;
            padding: 15px 50px;
            font-size: 20px;
            font-weight: bold;
            border-radius: 50px;
            margin-top: 40px;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
        }

        .enroll-btn:hover {
            transform: scale(1.05);
            background-color: #ffed4a;
            box-shadow: 0 6px 20px rgba(255, 215, 0, 0.5);
        }

        /* Content Container */
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        /* Announcement Section */
        .announcement-section {
            background-color: white;
            border-radius: 15px;
            padding: 35px;
            margin-bottom: 40px;
            box-shadow: 0 5px 20px rgba(0,51,102,0.1);
            border-top: 5px solid #ffd700;
        }

        .section-title {
            color: #003366;
            font-size: 32px;
            margin-bottom: 25px;
            border-bottom: 3px solid #ffd700;
            padding-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            font-size: 35px;
        }

        .announcement-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }

        .announcement-card {
            border: 1px solid #eee;
            border-radius: 10px;
            padding: 25px;
            transition: all 0.3s;
            background: linear-gradient(145deg, #ffffff, #f8f9fa);
        }

        .announcement-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,51,102,0.15);
            border-color: #ffd700;
        }

        .announcement-date {
            color: #ffd700;
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .announcement-card h3 {
            color: #003366;
            margin-bottom: 10px;
            font-size: 20px;
        }

        .announcement-card p {
            color: #666;
            line-height: 1.6;
        }

        /* Requirements Section */
        .requirements-section {
            background-color: white;
            border-radius: 15px;
            padding: 35px;
            margin-bottom: 40px;
            box-shadow: 0 5px 20px rgba(0,51,102,0.1);
            border-top: 5px solid #ffd700;
        }

        .requirements-tabs {
            display: flex;
            gap: 15px;
            margin-bottom: 35px;
            flex-wrap: wrap;
        }

        .tab-btn {
            padding: 12px 30px;
            border: none;
            background-color: #f0f0f0;
            cursor: pointer;
            font-weight: bold;
            border-radius: 25px;
            transition: all 0.3s;
            font-size: 16px;
        }

        .tab-btn.active {
            background-color: #003366;
            color: white;
            box-shadow: 0 4px 10px rgba(0,51,102,0.3);
        }

        .tab-btn:hover:not(.active) {
            background-color: #ffd700;
            color: #003366;
            transform: translateY(-2px);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .requirements-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }

        .requirement-category {
            background: linear-gradient(145deg, #f8f9fa, #ffffff);
            padding: 25px;
            border-radius: 10px;
            border-left: 4px solid #ffd700;
        }

        .requirement-category h3 {
            color: #003366;
            margin-bottom: 20px;
            font-size: 22px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .requirement-category ul {
            list-style: none;
        }

        .requirement-category li {
            padding: 12px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 15px;
        }

        .requirement-category li:last-child {
            border-bottom: none;
        }

        .requirement-category li:before {
            content: "✓";
            color: #ffd700;
            font-weight: bold;
            font-size: 18px;
            min-width: 20px;
        }

        /* Important Dates Table */
        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        th {
            background-color: #003366;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: bold;
        }

        td {
            padding: 15px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #ddd;
        }

        tr:hover td {
            background-color: #fff3cd;
        }

        /* Footer */
        footer {
            background-color: #003366;
            color: white;
            padding: 50px 20px 20px;
            margin-top: 50px;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
        }

        .footer-section h3 {
            color: #ffd700;
            margin-bottom: 20px;
            font-size: 20px;
            border-bottom: 2px solid #ffd700;
            padding-bottom: 10px;
            display: inline-block;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section li {
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-section li:hover {
            color: #ffd700;
            transform: translateX(5px);
        }

        .copyright {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #004080;
            color: #ffd700;
        }

        /* Floating Info Bar */
        .info-bar {
            background-color: #ffd700;
            padding: 10px 20px;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 15px;
            color: #003366;
            font-weight: bold;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .school-name h1 {
                font-size: 24px;
            }
            
            nav ul {
                gap: 10px;
                justify-content: flex-start;
            }
            
            nav ul li {
                font-size: 13px;
            }
            
            .banner h2 {
                font-size: 32px;
            }

            .logo-container {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <?php
    // PHP variables for dynamic content
    $current_date = date('F j, Y');
    $current_year = date('Y');
    $establishment_year = 2002;
    
    $announcements = [
        [
            'date' => 'February 15, 2026',
            'title' => 'Enrollment for School Year 2026-2027 Now Open!',
            'description' => 'Enrollment is now ongoing for incoming freshmen, transferees, and old students. Enroll early to secure your slot!',
            'icon' => '📢'
        ],
        [
            'date' => 'February 20, 2026',
            'title' => 'Entrance Examination Schedule',
            'description' => 'Entrance exams for new students will be held every Saturday at 8:00 AM. Please come early and bring necessary documents.',
            'icon' => '📝'
        ],
        [
            'date' => 'February 25, 2026',
            'title' => 'Scholarship Applications',
            'description' => 'Academic and athletic scholarship applications are now being accepted at the Student Personnel Services office.',
            'icon' => '🎓'
        ],
        [
            'date' => 'March 1, 2026',
            'title' => 'Orientation for New Students',
            'description' => 'Mandatory orientation for all new and transferee students will be on March 15, 2026 at the school auditorium.',
            'icon' => '🗓️'
        ]
    ];

    // Requirements data
    $new_student_requirements = [
        'Admission Requirements' => [
            'Form 138 (Report Card)',
            'PSA Birth Certificate (Original & Photocopy)',
            '2 pcs 2x2 ID Pictures (White Background)',
            'Certificate of Good Moral Character',
            'NSO/PSA Marriage Certificate (if married)',
            'Long Brown Envelope'
        ],
        'Additional Requirements' => [
            'Entrance Exam Result',
            'Interview Clearance',
            'Medical Certificate',
            'Barangay Clearance'
        ]
    ];

    $transferee_requirements = [
        'Transfer Credentials' => [
            'Honorable Dismissal (Original)',
            'Transcript of Records (TOR)',
            'Certificate of Transfer Credentials',
            'Course Description/Curriculum Received'
        ],
        'Personal Documents' => [
            'PSA Birth Certificate (Original & Photocopy)',
            '2 pcs 2x2 ID Pictures (White Background)',
            'Certificate of Good Moral Character',
            'Medical Certificate',
            'Accomplished Transfer Application Form'
        ]
    ];

    // Check for form submission
    $show_new = true;
    if(isset($_GET['tab'])) {
        $show_new = $_GET['tab'] == 'new';
    }
    ?>

    <!-- Top Header -->
    <div class="top-header">
        <div class="container" style="margin: 0 auto; display: flex; justify-content: space-between;">
            <span>📞 (02) 1234-5678</span>
            <span>✉️ admissions@bestlink.edu.ph</span>
            <span>📍 Novaliches, Quezon City</span>
        </div>
    </div>

    <!-- Main Header with Logo -->
    <div class="main-header">
        <div class="container" style="margin: 0 auto;">
            <div class="logo-container">
                <div class="logo">
                    <div class="year"><?php echo $establishment_year; ?></div>
                    <div class="bestlink">BESTLINK</div>
                    <div class="college">COLLEGE OF</div>
                    <div class="of">THE PHILIPPINES</div>
                </div>
                <div class="school-name">
                    <h1>Bestlink College of the Philippines</h1>
                    <p>Quality Education for Globally Competitive Citizens</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Info Bar -->
    <div class="info-bar">
        <div class="info-item">📅 Enrollment Period: February 15 - May 30, 2026</div>
        <div class="info-item">⏰ Office Hours: Mon-Fri 8:00 AM - 5:00 PM</div>
        <div class="info-item">🎓 Limited Slots Available!</div>
    </div>

    <!-- Navigation -->
    <nav>
        <div class="container" style="margin: 0 auto;">
            <ul>
                <li><a href="aboutbcp.php">ABOUT BCP</a></li>
                <li>ADMISSION</li>
                <li>BCP NEWS</li>
                <li>STUDENT PERSONNEL SERVICES</li>
                <li>ACADEMICS</li>
                <li>CAREERS</li>
                <li>SCHOOL FACILITIES</li>
            </ul>
        </div>
    </nav>

    <!-- Banner -->
    <div class="banner">
        <h2>Welcome to Bestlink College of the Philippines</h2>
        <p>At Bestlink College of the Philippines, we provide and promote quality education with modern and globally competitive citizens. Since <?php echo $establishment_year; ?>, we have been shaping the future leaders of tomorrow.</p>
        <button class="enroll-btn" onclick="window.location.href='register.php'">ENROLL NOW!</button>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Announcement Section -->
        <section class="announcement-section">
            <h2 class="section-title">📢 BCP NEWS & ANNOUNCEMENTS</h2>
            <div class="announcement-grid">
                <?php foreach($announcements as $announcement): ?>
                <div class="announcement-card">
                    <div class="announcement-date"><?php echo $announcement['icon']; ?> <?php echo $announcement['date']; ?></div>
                    <h3><?php echo $announcement['title']; ?></h3>
                    <p><?php echo $announcement['description']; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Requirements Section -->
        <section class="requirements-section" id="requirements">
            <h2 class="section-title">📋 ADMISSION REQUIREMENTS</h2>
            
            <div class="requirements-tabs">
                <form method="GET" style="display: flex; gap: 10px;">
                    <button type="submit" name="tab" value="new" class="tab-btn <?php echo $show_new ? 'active' : ''; ?>">
                        🆕 New Students
                    </button>
                    <button type="submit" name="tab" value="transferee" class="tab-btn <?php echo !$show_new ? 'active' : ''; ?>">
                        🔄 Transferees
                    </button>
                </form>
            </div>

            <!-- New Students Tab -->
            <div class="tab-content <?php echo $show_new ? 'active' : ''; ?>" id="new-students">
                <div class="requirements-list">
                    <?php foreach($new_student_requirements as $category => $items): ?>
                    <div class="requirement-category">
                        <h3>📌 <?php echo $category; ?></h3>
                        <ul>
                            <?php foreach($items as $item): ?>
                            <li><?php echo $item; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div style="margin-top: 25px; padding: 15px; background-color: #e3f2fd; border-radius: 8px; border-left: 4px solid #003366;">
                    <p style="color: #003366;">
                        <strong>📌 Important Note:</strong> All documents must be submitted in a long brown envelope. 
                        Please bring the original copies for verification purposes. Application fee: ₱500.00
                    </p>
                </div>
            </div>

            <!-- Transferees Tab -->
            <div class="tab-content <?php echo !$show_new ? 'active' : ''; ?>" id="transferees">
                <div class="requirements-list">
                    <?php foreach($transferee_requirements as $category => $items): ?>
                    <div class="requirement-category">
                        <h3>📌 <?php echo $category; ?></h3>
                        <ul>
                            <?php foreach($items as $item): ?>
                            <li><?php echo $item; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div style="margin-top: 25px; padding: 15px; background-color: #fff3cd; border-radius: 8px; border-left: 4px solid #ffd700;">
                    <p style="color: #856404;">
                        <strong>⚠️ For Transferees:</strong> Please secure your Honorable Dismissal from your previous school 
                        before proceeding with the enrollment process. Evaluation of credentials will be done by the Registrar's Office.
                        Transfer fee: ₱300.00
                    </p>
                </div>
            </div>
        </section>

        <!-- Important Dates Section -->
        <section class="announcement-section">
            <h2 class="section-title">📅 IMPORTANT DATES & SCHEDULES</h2>
            <table>
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Date</th>
                        <th>Venue</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Entrance Examination</td>
                        <td>Every Saturday</td>
                        <td>BCP Testing Center</td>
                        <td>8:00 AM - 12:00 PM</td>
                    </tr>
                    <tr>
                        <td>Student Orientation</td>
                        <td>March 15, 2026</td>
                        <td>School Auditorium</td>
                        <td>9:00 AM - 12:00 PM</td>
                    </tr>
                    <tr>
                        <td>Enrollment Deadline</td>
                        <td>May 30, 2026</td>
                        <td>Registrar's Office</td>
                        <td>8:00 AM - 5:00 PM</td>
                    </tr>
                    <tr>
                        <td>Start of Classes</td>
                        <td>June 1, 2026</td>
                        <td>BCP Campus</td>
                        <td>7:30 AM</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- Contact Section -->
        <section class="requirements-section">
            <h2 class="section-title">📞 ADMISSIONS CONTACT</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div style="text-align: center; padding: 20px;">
                    <div style="font-size: 40px; margin-bottom: 10px;">🏢</div>
                    <h3 style="color: #003366;">Registrar's Office</h3>
                    <p>2nd Floor, Administration Building</p>
                    <p>📞 (02) 1234-5678 loc. 101</p>
                </div>
                <div style="text-align: center; padding: 20px;">
                    <div style="font-size: 40px; margin-bottom: 10px;">👥</div>
                    <h3 style="color: #003366;">Student Personnel Services</h3>
                    <p>Ground Floor, Student Center</p>
                    <p>📞 (02) 1234-5678 loc. 102</p>
                </div>
                <div style="text-align: center; padding: 20px;">
                    <div style="font-size: 40px; margin-bottom: 10px;">📱</div>
                    <h3 style="color: #003366;">Admissions Hotline</h3>
                    <p>Globe: 0917-123-4567</p>
                    <p>Smart: 0999-123-4567</p>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>About BCP</h3>
                <ul>
                    <li>🏛️ Mission & Vision</li>
                    <li>📜 History</li>
                    <li>👥 Administration</li>
                    <li>📞 Contact Us</li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Admissions</h3>
                <ul>
                    <li>📋 Requirements</li>
                    <li>📝 Enrollment Process</li>
                    <li>🎓 Scholarships</li>
                    <li>❓ FAQ</li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Academics</h3>
                <ul>
                    <li>📚 Programs</li>
                    <li>👨‍🏫 Faculty</li>
                    <li>📖 Research</li>
                    <li>🏆 Achievements</li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact Information</h3>
                <ul>
                    <li>📍 Quirino Highway, Novaliches</li>
                    <li>📞 (02) 1234-5678</li>
                    <li>📱 0917-123-4567</li>
                    <li>✉️ info@bestlink.edu.ph</li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; <?php echo $current_year; ?> Bestlink College of the Philippines. All rights reserved. | Founded <?php echo $establishment_year; ?></p>
        </div>
    </footer>

    <script>
        // Smooth scroll for enroll button
        document.querySelector('.enroll-btn').addEventListener('click', function() {
            document.getElementById('requirements').scrollIntoView({ behavior: 'smooth' });
        });

        // Add animation on scroll
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('nav');
            if (window.scrollY > 100) {
                nav.style.backgroundColor = '#ebebe4';
            } else {
                nav.style.backgroundColor = '#ebebe4';
            }
        });
    </script>
</body>
</html>