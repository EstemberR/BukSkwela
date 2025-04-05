<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo e(env('APP_NAME')); ?> | Login</title>

    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Work Sans font -->
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom Login CSS -->
    <link href="<?php echo e(asset('assets/css/pages/login.css')); ?>" rel="stylesheet">
</head>

<body>
    <div class="split-layout">
        <!-- Form Side -->
        <div class="form-side">
            <div class="auth-form-light p-5">
                <div class="form-header-logo">
                    <img src="<?php echo e(asset('assets/images/logo.png')); ?>" alt="<?php echo e(env('APP_NAME')); ?>">
                </div>

                <h4 class="mb-2">Welcome back!</h4>
                <p class="text-muted mb-4">Log in to continue to your workspace</p>

                <?php if(session('error')): ?>
                    <div class="alert alert-danger">
                        <?php echo e(session('error')); ?>

                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('login.post')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <label>Email</label>
                        <div class="position-relative">
                            <div class="input-icon-wrapper">
                                <i class="fas fa-envelope input-icon"></i>
                            </div>
                            <input type="email" class="form-control" name="email" 
                                placeholder="Enter your email" required>
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <div class="position-relative">
                            <div class="input-icon-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                            </div>
                            <input type="password" class="form-control" name="password" 
                                placeholder="Enter your password" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="remember-me">
                                <input type="checkbox" name="remember" id="remember">
                                <label for="remember">Remember Me</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-block btn-primary btn-lg auth-form-btn">
                            Login
                        </button>

                        <div class="text-center mt-3">
                            <small>
                                Don't have an account? 
                                <a href="<?php echo e(route('register')); ?>" class="font-weight-medium login-link">Create one</a>
                            </small>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Image Side -->
        <div class="image-side">
            <div class="image-overlay">
                <h2>Welcome to BukSkwela!</h2>
                <p class="lead">Less paper, less hassle—manage documents with ease, whether you're a student or instructor.</p>
                <p class="subtext">Sign in and take control of your documents today!</p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html><?php /**PATH C:\Users\User\Documents\BukSkwela\resources\views/login.blade.php ENDPATH**/ ?>