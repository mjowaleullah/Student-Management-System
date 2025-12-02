<?php
include 'config.php';

// Delete operation (prepared statement)
if (isset($_GET['delete'])) {
    $SL = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM students WHERE SL = :SL");
    $stmt->bindParam(':SL', $SL);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Student deleted successfully!";
        header("Location: index.php");
        exit();
    }
}

// Pagination & Search
$perPage = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$whereClause = !empty($search) ? "WHERE name LIKE :search OR gmail LIKE :search OR department LIKE :search" : '';
$countStmt = $conn->prepare("SELECT COUNT(*) as total FROM students $whereClause");
if (!empty($search)) {
    $searchParam = "%$search%";
    $countStmt->bindParam(':search', $searchParam);
}
$countStmt->execute();
$totalPages = ceil($countStmt->fetch(PDO::FETCH_ASSOC)['total'] / $perPage);

$stmt = $conn->prepare("SELECT * FROM students $whereClause ORDER BY SL DESC LIMIT :limit OFFSET :offset");
if (!empty($search)) {
    $stmt->bindParam(':search', $searchParam);
}
$stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management Dashboard</title>
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
            overflow-x: hidden;
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
        
        .sidebar {
            background: rgba(33, 37, 41, 0.6);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-right: 1px solid var(--glass-border);
        }
        
        .table-responsive {
            border-radius: 15px;
            overflow: hidden;
        }
        
        .table-dark {
            background: rgba(33, 37, 41, 0.7);
            backdrop-filter: blur(5px);
        }
        
        .table-hover tbody tr {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
        }
        
        .table-hover tbody tr:hover {
            background: rgba(13, 202, 240, 0.15) !important;
            transform: translateX(10px) scale(1.02);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
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
        
        .search-input {
            background: rgba(33, 37, 41, 0.7) !important;
            border: 1px solid var(--glass-border);
            color: white !important;
            transition: all 0.3s ease;
        }
        
        .search-input:focus {
            background: rgba(33, 37, 41, 0.9) !important;
            box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
            transform: scale(1.02);
        }
        
        .pagination .page-link {
            background: rgba(33, 37, 41, 0.7);
            border: 1px solid var(--glass-border);
            color: #fff;
            transition: all 0.3s ease;
        }
        
        .pagination .page-item.active .page-link {
            background: var(--bs-gradient);
            border-color: #6f42c1;
        }
        
        .pagination .page-link:hover {
            background: rgba(111, 66, 193, 0.3);
            transform: translateY(-2px);
        }
        
        /* Advanced Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        @keyframes slideInFromLeft {
            0% { transform: translateX(-100px); opacity: 0; }
            100% { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes slideInFromRight {
            0% { transform: translateX(100px); opacity: 0; }
            100% { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes fadeInUp {
            0% { transform: translateY(30px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
        
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-pulse-slow { animation: pulse 3s ease-in-out infinite; }
        .animate-slide-left { animation: slideInFromLeft 0.8s ease-out; }
        .animate-slide-right { animation: slideInFromRight 0.8s ease-out; }
        .animate-fade-up { animation: fadeInUp 0.8s ease-out; }
        
        /* Advanced Particle System */
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
        
        /* Stats Cards */
        .stats-card {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
        }
        
        .stats-card:hover {
            transform: translateY(-10px) scale(1.03);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4);
        }
        
        /* Loading Animation */
        .spinner-border { display: none; }
        .loading .spinner-border { display: inline-block; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(33, 37, 41, 0.5);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--bs-gradient);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
    </style>
</head>
<body>
    <!-- Advanced Particle System -->
    <div class="particles-container" id="particles"></div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top animate__animated animate__fadeInDown">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class="bi bi-mortarboard-fill me-2 animate-pulse-slow"></i>
                <span class="fw-bold">StudentPortal Pro</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Analytics</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Reports</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link btn-glow text-white px-3 py-2 rounded" href="create.php">
                            <i class="bi bi-plus-circle me-2"></i>Add Student
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-2"></i>Admin
                        </a>
                        <ul class="dropdown-menu glass-card border-0">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-5 pt-3">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-none d-md-block sidebar vh-100 position-fixed">
                <div class="position-sticky pt-5">
                    <ul class="nav flex-column">
                        <li class="nav-item mb-3 animate-slide-left" style="animation-delay: 0.1s;">
                            <a class="nav-link active text-light d-flex align-items-center p-3 rounded" href="index.php">
                                <i class="bi bi-speedometer2 me-3 fs-5"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item mb-3 animate-slide-left" style="animation-delay: 0.2s;">
                            <a class="nav-link text-light d-flex align-items-center p-3 rounded" href="#">
                                <i class="bi bi-people me-3 fs-5"></i>
                                <span>Students</span>
                            </a>
                        </li>
                        <li class="nav-item mb-3 animate-slide-left" style="animation-delay: 0.3s;">
                            <a class="nav-link text-light d-flex align-items-center p-3 rounded" href="#">
                                <i class="bi bi-bar-chart me-3 fs-5"></i>
                                <span>Analytics</span>
                            </a>
                        </li>
                        <li class="nav-item mb-3 animate-slide-left" style="animation-delay: 0.4s;">
                            <a class="nav-link text-light d-flex align-items-center p-3 rounded" href="#">
                                <i class="bi bi-file-text me-3 fs-5"></i>
                                <span>Reports</span>
                            </a>
                        </li>
                        <li class="nav-item mb-3 animate-slide-left" style="animation-delay: 0.5s;">
                            <a class="nav-link text-light d-flex align-items-center p-3 rounded" href="#">
                                <i class="bi bi-gear me-3 fs-5"></i>
                                <span>Settings</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto px-md-4" style="margin-left: 16.666667% !important;">
                <!-- Header Stats -->
                <div class="row mb-4 animate-fade-up">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card glass-card stats-card border-0 h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-uppercase text-info">Total Students</h6>
                                        <h2 class="mb-0 fw-bold"><?php echo $countStmt->fetch()['total'] ?? 0; ?></h2>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-people-fill text-info fs-1 animate-float"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card glass-card stats-card border-0 h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-uppercase text-success">Active</h6>
                                        <h2 class="mb-0 fw-bold"><?php echo $countStmt->fetch()['total'] ?? 0; ?></h2>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-check-circle-fill text-success fs-1 animate-float" style="animation-delay: 1s;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card glass-card stats-card border-0 h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-uppercase text-warning">Departments</h6>
                                        <h2 class="mb-0 fw-bold">6</h2>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-building text-warning fs-1 animate-float" style="animation-delay: 2s;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card glass-card stats-card border-0 h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-uppercase text-primary">This Month</h6>
                                        <h2 class="mb-0 fw-bold">12</h2>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-graph-up-arrow text-primary fs-1 animate-float" style="animation-delay: 3s;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary animate-fade-up">
                    <h1 class="h2 text-light fw-bold">Student Management</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-light">Export</button>
                            <button type="button" class="btn btn-sm btn-outline-light">Print</button>
                        </div>
                    </div>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show glass-card animate__animated animate__bounceIn" role="alert" id="successAlert">
                        <i class="bi bi-check-circle-fill me-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Search & Add -->
                <div class="row mb-4 animate-fade-up" style="animation-delay: 0.2s;">
                    <div class="col-md-6">
                        <form method="GET" class="d-flex">
                            <div class="input-group">
                                <input type="text" class="form-control search-input" name="search" placeholder="Search by name, department, or email..." value="<?php echo htmlspecialchars($search); ?>">
                                <button type="submit" class="btn btn-glow text-white">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="create.php" class="btn btn-glow text-white me-2">
                            <i class="bi bi-plus-circle me-2"></i>Add New Student
                        </a>
                        <?php if (!empty($search)): ?>
                            <a href="index.php" class="btn btn-outline-light">Clear</a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive glass-card p-3 animate-fade-up" style="animation-delay: 0.3s;">
                    <table class="table table-dark table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Roll</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Mobile</th>
                                <th>Gmail</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($result) > 0): ?>
                                <?php foreach($result as $index => $row): ?>
                                    <tr class="animate__animated animate__fadeIn" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                                        <td><?php echo $row['SL']; ?></td>
                                        <td><?php echo htmlspecialchars($row['roll']); ?></td>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td>
                                            <span class="badge bg-info"><?php echo htmlspecialchars($row['department']); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['mobile']); ?></td>
                                        <td>
                                            <a href="mailto:<?php echo htmlspecialchars($row['gmail']); ?>" class="text-decoration-none text-info">
                                                <?php echo htmlspecialchars($row['gmail']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="edit.php?SL=<?php echo $row['SL']; ?>" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="index.php?delete=<?php echo $row['SL']; ?>" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure you want to delete this student?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                                <a href="#" class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip" title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-search display-4 d-block mb-3"></i>
                                        No students found. <a href="create.php" class="text-info">Add the first student</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Pagination" class="mt-4 animate-fade-up" style="animation-delay: 0.4s;">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light text-center py-4 mt-5 glass-card">
        <div class="container">
            <p class="mb-0">&copy; 2025 StudentPortal Pro. All rights reserved. Designed By Mj Owaleullah <i class="bi bi-heart-fill text-danger"></i> </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script>
        // Advanced Particle System
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            for (let i = 0; i < 50; i++) {
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

        // Confetti on Success
        <?php if (isset($_SESSION['success'])): ?>
        setTimeout(() => {
            confetti({
                particleCount: 150,
                spread: 70,
                origin: { y: 0.6 },
                colors: ['#6f42c1', '#0dcaf0', '#dc3545', '#198754', '#ffc107']
            });
        }, 500);
        <?php endif; ?>

        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Animate on Scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'fadeInUp 0.8s ease-out forwards';
                }
            });
        }, observerOptions);

        // Observe all cards and table rows
        document.querySelectorAll('.stats-card, .table-responsive, .pagination').forEach(el => {
            observer.observe(el);
        });

        // Add loading animation to buttons
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('click', function() {
                this.classList.add('loading');
                setTimeout(() => {
                    this.classList.remove('loading');
                }, 2000);
            });
        });

        // Auto-hide success alert
        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            setTimeout(() => {
                successAlert.classList.remove('show');
                setTimeout(() => successAlert.remove(), 150);
            }, 5000);
        }
    </script>
</body>
</html>