<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/auth.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$errorMessage = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($name) || empty($email) || empty($password)) {
        $errorMessage = 'Name, email, and password are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Invalid email format.';
    } elseif (strlen($password) < 6) {
        $errorMessage = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirmPassword) {
        $errorMessage = 'Passwords do not match.';
    } else {
        try {
            global $pdo;
            
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $errorMessage = 'Email is already registered.';
            } else {
                $hashedPassword = generatePasswordHash($password);
                
                $stmt = $pdo->prepare("
                    INSERT INTO users (name, email, phone, password, role, membership_level, points) 
                    VALUES (?, ?, ?, ?, 'customer', 'bronze', 0)
                ");
                
                if ($stmt->execute([$name, $email, $phone ?: null, $hashedPassword])) {
                    $successMessage = 'Registration successful! You can now login.';
                } else {
                    $errorMessage = 'Registration failed. Please try again.';
                }
            }
        } catch (PDOException $e) {
            $errorMessage = 'Database error. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - GameZone Gaming Room</title>
    <link rel="stylesheet" href="assets/css/adminlte.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body class="login-page bg-body-secondary">
    <div class="login-box" style="width: 500px;">
        <div class="login-logo">
            <a href="index.php"><b>Game</b>Zone</a>
            <p class="text-muted small">Create Your Account</p>
        </div>
        
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Register a new membership</p>
                
                <?php if ($errorMessage): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($errorMessage) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($successMessage): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($successMessage) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <form action="register.php" method="post">
                    <div class="input-group mb-3">
                        <input type="text" name="name" class="form-control" placeholder="Full name" required
                               value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
                        <div class="input-group-text">
                            <span class="bi bi-person"></span>
                        </div>
                    </div>
                    
                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email" required
                               value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                        <div class="input-group-text">
                            <span class="bi bi-envelope"></span>
                        </div>
                    </div>
                    
                    <div class="input-group mb-3">
                        <input type="text" name="phone" class="form-control" placeholder="Phone (optional)"
                               value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>">
                        <div class="input-group-text">
                            <span class="bi bi-telephone"></span>
                        </div>
                    </div>
                    
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password (min 6 characters)" required>
                        <div class="input-group-text">
                            <span class="bi bi-lock-fill"></span>
                        </div>
                    </div>
                    
                    <div class="input-group mb-3">
                        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password" required>
                        <div class="input-group-text">
                            <span class="bi bi-lock-fill"></span>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-8">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" required id="terms">
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#">terms</a>
                                </label>
                            </div>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Register</button>
                        </div>
                    </div>
                </form>
                
                <div class="text-center mt-4">
                    <a href="login.php" class="text-decoration-none">
                        Already have an account? Sign in
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/adminlte.js"></script>
</body>
</html>