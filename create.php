<?php
include 'config.php';

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $roll = $_POST['roll'];
    $name = $_POST['name'];
    $department = $_POST['department'];
    $mobile = $_POST['mobile'];
    $gmail = $_POST['gmail'];
    
    $stmt = $conn->prepare("INSERT INTO students (roll, name, department, mobile, gmail) VALUES (:roll, :name, :department, :mobile, :gmail)");
    $stmt->bindParam(':roll', $roll);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':department', $department);
    $stmt->bindParam(':mobile', $mobile);
    $stmt->bindParam(':gmail', $gmail);
    
    try {
        $stmt->execute();
        $_SESSION['success'] = "Student added successfully!";
        header("Location: index.php");
        exit();
    } catch(PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student - StudentPortal Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
        :root {
            --bs-primary: #6f42c1;
            --bs-info: #0dcaf0;
            --bs-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }
        
        body {
            background: linear-gradient(-45deg, #1a1a2e, #16213e, #0f3460, #533483);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            min-height: 100vh;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }
        
        .navbar {
            background: rgba(33, 37, 41, 0.8) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--glass-border);
        }
        
        .form-control {
            background: rgba(33, 37, 41, 0.7) !important;
            border: 1px solid var(--glass-border);
            color: white !important;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            background: rgba(33, 37, 41, 0.9) !important;
            box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
            transform: scale(1.02);
            border-color: #6f42c1;
        }
        
        .btn-glow {
            background: var(--bs-gradient);
            border: none;
            position: relative;
            overflow: hidden;
            transition: all 0.4s ease;
        }
        
        .btn-glow::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: all 0.6s;
        }
        
        .btn-glow:hover::before {
            left: 100%;
        }
        
        .btn-glow:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(111, 66, 193, 0.4);
        }
        
        .particles-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }
        
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: linear-gradient(45deg, #6f42c1, #0dcaf0);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.6; }
            50% { transform: translateY(-20px) rotate(180deg); opacity: 1; }
        }
        
        @keyframes fadeInUp {
            0% { transform: translateY(30px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
        
        .animate-fade-up { animation: fadeInUp 0.8s ease-out; }
        
        .loading .spinner-border { display: inline-block; }
    </style>
</head>
<body>
    <!-- Advanced Particle System -->
    <div class="particles-container" id="particles"></div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class="bi bi-mortarboard-fill me-2"></i>
                <span class="fw-bold">StudentPortal Pro</span>
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-light" href="index.php">
                            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 pt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card glass-card shadow-lg animate-fade-up">
                    <div class="card-header text-white text-center py-4" style="background: var(--bs-gradient); border-radius: 15px 15px 0 0;">
                        <h3 class="mb-0 fw-bold"><i class="bi bi-person-plus me-2"></i>Add New Student</h3>
                        <p class="mb-0 mt-2 opacity-75">Fill in the details to add a new student to the system</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($message): ?>
                            <div class="alert alert-danger animate__animated animate__shakeX d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <div><?php echo $message; ?></div>
                            </div>
                        <?php endif; ?>
                        <form method="post" id="addForm" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="roll" class="form-label fw-semibold">Roll No.</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-dark border-dark">
                                            <i class="bi bi-123 text-info"></i>
                                        </span>
                                        <input type="number" class="form-control" id="roll" name="roll" required>
                                        <div class="invalid-feedback">
                                            Please provide a valid roll number.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label fw-semibold">Student Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-dark border-dark">
                                            <i class="bi bi-person text-info"></i>
                                        </span>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                        <div class="invalid-feedback">
                                            Please provide a student name.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="department" class="form-label fw-semibold">Department</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-dark">
                                        <i class="bi bi-building text-info"></i>
                                    </span>
                                    <select class="form-select" id="department" name="department" required>
                                        <option value="">Choose department...</option>
                                        <option value="Computer Science & Technology">Computer Science & Technology</option>
                                        <option value="Electrical Technology">Electrical Technology</option>
                                        <option value="Mechanical Technology">Mechanical Technology</option>
                                        <option value="Civil Technology">Civil Technology</option>
                                        <option value="Electronics Technology">Electronics Technology</option>
                                        <option value="Power Technology">Power Technology</option>
                                        <option value="Power Technology">Power Technology</option>
                                        <option value="Construction Technology">Construction Technology</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Please select a department.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="mobile" class="form-label fw-semibold">Mobile</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-dark border-dark">
                                            <i class="bi bi-phone text-info"></i>
                                        </span>
                                        <input type="tel" class="form-control" id="mobile" name="mobile" pattern="[0-9]{11}" placeholder="11-digit number">
                                        <div class="invalid-feedback">
                                            Please provide a valid 11-digit mobile number.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="gmail" class="form-label fw-semibold">Gmail</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-dark border-dark">
                                            <i class="bi bi-envelope text-info"></i>
                                        </span>
                                        <input type="email" class="form-control" id="gmail" name="gmail" required>
                                        <div class="invalid-feedback">
                                            Please provide a valid email address.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-glow text-white py-2 fw-semibold">
                                    <span class="submit-text">
                                        <i class="bi bi-person-plus me-2"></i>Add Student
                                    </span>
                                    <span class="loading-text d-none">
                                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                        Adding Student...
                                    </span>
                                </button>
                                <a href="index.php" class="btn btn-outline-light py-2">
                                    <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-light text-center py-4 mt-5 glass-card">
        <div class="container">
            <p class="mb-0">&copy; 2025 StudentPortal Pro. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Advanced Particle System
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            for (let i = 0; i < 30; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = Math.random() * 100 + '%';
                particle.style.width = Math.random() * 6 + 2 + 'px';
                particle.style.height = particle.style.width;
                particle.style.animationDelay = Math.random() * 6 + 's';
                particle.style.animationDuration = Math.random() * 3 + 3 + 's';
                particlesContainer.appendChild(particle);
            }
        }
        createParticles();

        // Form Validation and Loading Animation
        (function () {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')
            
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    } else {
                        event.preventDefault()
                        
                        const submitBtn = form.querySelector('button[type="submit"]');
                        const submitText = submitBtn.querySelector('.submit-text');
                        const loadingText = submitBtn.querySelector('.loading-text');
                        
                        submitText.classList.add('d-none');
                        loadingText.classList.remove('d-none');
                        submitBtn.disabled = true;
                        
                        // Simulate form submission
                        setTimeout(() => {
                            form.submit();
                        }, 1500);
                    }
                    
                    form.classList.add('was-validated')
                }, false)
            })
        })()
        
        // Input animations
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (this.value === '') {
                    this.parentElement.classList.remove('focused');
                }
            });
        });
    </script>
</body>
</html>