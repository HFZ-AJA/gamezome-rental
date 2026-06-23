<?php
require_once __DIR__ . '/inc/functions.php';

global $pdo;

$statDevices = 0;
$statCustomers = 0;
$statBookings = 0;
$devices = [];

try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM devices WHERE status = 'available'");
    $statDevices = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'customer'");
    $statCustomers = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM bookings");
    $statBookings = $stmt->fetch()['total'];
    
    $devices = $pdo->query("SELECT * FROM devices WHERE status != 'maintenance' ORDER BY type, name LIMIT 6")->fetchAll();
} catch (PDOException $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameZone - Gaming Room Booking</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        :root { --g-purple: #7C3AED; --g-cyan: #06B6D4; --g-green: #22C55E; --g-dark: #0F172A; }
        body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; }
        .hero {
            background: linear-gradient(135deg, var(--g-dark) 0%, #1e293b 50%, var(--g-purple) 100%);
            color: white;
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 500px;
            height: 500px;
            background: linear-gradient(45deg, transparent 30%, rgba(124,58,237,0.15) 100%);
            border-radius: 50%;
        }
        .hero h1 { font-size: 3.5rem; font-weight: 800; }
        .hero .gaming-gradient { background: linear-gradient(135deg, var(--g-purple), var(--g-cyan)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .btn-gaming { background: linear-gradient(135deg, var(--g-purple), var(--g-cyan)); border: none; color: white; padding: 12px 30px; border-radius: 8px; font-weight: 600; transition: transform 0.2s; }
        .btn-gaming:hover { transform: translateY(-2px); color: white; }
        .feature-icon { font-size: 2.5rem; color: var(--g-purple); }
        .stat-card { background: white; border-radius: 12px; padding: 30px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.07); transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-number { font-size: 2.5rem; font-weight: 800; color: var(--g-purple); }
        .device-card { border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.07); transition: transform 0.3s; height: 100%; }
        .device-card:hover { transform: translateY(-5px); }
        .device-badge { position: absolute; top: 10px; right: 10px; }
        footer { background: var(--g-dark); color: white; padding: 40px 0; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: var(--g-dark);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><span class="text-purple" style="color: var(--g-purple);">Game</span>Zone</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#devices">Devices</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php" class="text-light">Login</a></li>
                    <li class="nav-item ms-2"><a class="btn btn-gaming btn-sm" href="register.php">Register</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="container position-relative">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <h1>Welcome to <span class="gaming-gradient">GameZone</span></h1>
                    <p class="lead mb-4 text-light opacity-75">The ultimate gaming experience. Book your gaming room, compete in tournaments, and level up your play.</p>
                    <div class="d-flex gap-3">
                        <a href="register.php" class="btn btn-gaming btn-lg"><i class="bi bi-controller me-2"></i>Get Started</a>
                        <a href="#devices" class="btn btn-outline-light btn-lg">Browse Devices</a>
                    </div>
                </div>
                <div class="col-lg-5 text-center d-none d-lg-block">
                    <i class="bi bi-controller" style="font-size: 12rem; opacity: 0.15;"></i>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5" style="background: #f8fafc;">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-number"><?= $statDevices ?></div>
                        <div class="text-muted">Available Devices</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-number"><?= $statCustomers ?></div>
                        <div class="text-muted">Happy Gamers</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-number"><?= $statBookings ?></div>
                        <div class="text-muted">Bookings Made</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="devices" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Our <span style="color: var(--g-purple);">Gaming Devices</span></h2>
                <p class="text-muted">Choose from our premium gaming setups</p>
            </div>
            <div class="row g-4">
                <?php foreach ($devices as $device): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card device-card">
                            <div class="position-relative">
                                <div style="height: 160px; background: linear-gradient(135deg, #1e293b, #334155);" class="d-flex align-items-center justify-content-center">
                                    <i class="bi bi-controller text-white" style="font-size: 4rem; opacity: 0.5;"></i>
                                </div>
                                <div class="device-badge"><?= getDeviceStatusBadge($device['status']) ?></div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?= escape($device['name']) ?></h5>
                                <p class="text-muted small mb-2"><i class="bi bi-tag me-1"></i><?= escape($device['type']) ?></p>
                                <p class="card-text small text-muted"><?= escape(substr($device['specification'] ?? '', 0, 80)) ?>...</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h5 mb-0" style="color: var(--g-purple);"><?= formatRupiah($device['price_per_hour']) ?><small class="text-muted fs-6">/hr</small></span>
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <a href="dashboard.php?page=bookings_create&device_id=<?= $device['id'] ?>" class="btn btn-gaming btn-sm">Book Now</a>
                                    <?php else: ?>
                                        <a href="login.php" class="btn btn-outline-primary btn-sm">Login to Book</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="login.php" class="btn btn-gaming">View All Devices</a>
            </div>
        </div>
    </section>

    <section id="features" class="py-5" style="background: #f8fafc;">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Why Choose <span style="color: var(--g-purple);">GameZone</span></h2>
            </div>
            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <i class="bi bi-controller feature-icon"></i>
                    <h5>Premium Devices</h5>
                    <p class="text-muted">PS5, PC Gaming, VR - all the latest gaming hardware</p>
                </div>
                <div class="col-md-4 text-center">
                    <i class="bi bi-calendar-check feature-icon"></i>
                    <h5>Easy Booking</h5>
                    <p class="text-muted">Book online in minutes with instant confirmation</p>
                </div>
                <div class="col-md-4 text-center">
                    <i class="bi bi-trophy feature-icon"></i>
                    <h5>Tournaments</h5>
                    <p class="text-muted">Compete in regular tournaments with prizes</p>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><span style="color: var(--g-purple);">Game</span><span class="text-white">Zone</span></h5>
                    <p class="text-light opacity-50 small">Your ultimate gaming destination. Book, play, and compete.</p>
                </div>
                <div class="col-md-3">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled small opacity-75">
                        <li><a href="login.php" class="text-light text-decoration-none">Login</a></li>
                        <li><a href="register.php" class="text-light text-decoration-none">Register</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6>Contact</h6>
                    <ul class="list-unstyled small opacity-75">
                        <li>info@gamezone.com</li>
                        <li>021-12345678</li>
                    </ul>
                </div>
            </div>
            <hr class="opacity-25">
            <p class="text-center small opacity-50 mb-0">&copy; 2026 GameZone Gaming Room. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>