<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/auth.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once __DIR__ . '/inc/functions.php';

$errorMessage = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $errorMessage = 'Please enter both email and password.';
    } else {
        global $pdo;
        
        try {
            $stmt = $pdo->prepare("
                SELECT id, name, email, password, role, membership_level, points 
                FROM users 
                WHERE email = ? 
                LIMIT 1
            ");
            
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && verifyPassword($password, $user['password'])) {
                session_regenerate_id(true);
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['membership_level'] = $user['membership_level'];
                $_SESSION['points'] = $user['points'];
                
                addNotification(
                    $user['id'],
                    'Login Successful',
                    'You have successfully logged into your GameZone account.',
                    'system'
                );
                
                header('Location: dashboard.php');
                exit;
            } else {
                $errorMessage = 'Invalid email or password.';
            }
        } catch (PDOException $e) {
            $errorMessage = 'Database error. Please try again.';
        }
    }
}

$flashMessage = getFlashMessage();
if ($flashMessage) {
    if ($flashMessage['type'] === 'success') {
        $successMessage = $flashMessage['text'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GameZone Gaming Room</title>
    <link rel="stylesheet" href="assets/css/adminlte.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/img/AdminLTELogo.png" type="image/png">
</head>
<body class="login-page bg-body-secondary">
    <div class="login-box">
        <div class="login-logo">
            <a href="index.php"><b>Game</b>Zone</a>
            <p class="text-muted small">Gaming Room Booking System</p>
        </div>
        
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Sign in to start your session</p>
                
                <?php if ($successMessage): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($successMessage) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($errorMessage): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($errorMessage) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <form action="login.php" method="post">
                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email" required 
                               value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                        <div class="input-group-text">
                            <span class="bi bi-envelope"></span>
                        </div>
                    </div>
                    
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                        <div class="input-group-text">
                            <span class="bi bi-lock-fill"></span>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-8">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label" for="remember">Remember Me</label>
                            </div>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                    </div>
                </form>
                
                <div class="social-auth-links text-center mb-3 mt-4">
                    <p class="text-muted small">- OR -</p>
                </div>
                
                <div class="text-center">
                    <p class="mb-2">
                        Don't have an account? 
                        <a href="register.php" class="text-decoration-none">Register here</a>
                    </p>
                    <p class="mb-0">
                        <a href="forgot-password.php" class="text-decoration-none">Forgot password?</a>
                    </p>
                </div>
                
                <hr class="my-3">
                
                <div class="text-center">
                    <p class="small text-muted mb-0">Test Accounts:</p>
                    <div class="text-start small">
                        <p class="mb-1">
                            <strong>Admin:</strong> admin@gamezone.com / admin123
                            <span class="badge bg-danger ms-2">Full Access</span>
                        </p>
                        <p class="mb-1">
                            <strong>Customer:</strong> customer@email.com / customer123
                            <span class="badge bg-success ms-2">Book Devices</span>
                        </p>
                        <p class="mb-0">
                            <strong>Staff:</strong> staff@gamezone.com / staff123
                            <span class="badge bg-warning text-dark ms-2">Manage Bookings</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/adminlte.js"></script>
</body>
</html>