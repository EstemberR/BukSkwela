<!DOCTYPE html>
<html>
<head>
    <title><?php echo $__env->yieldContent('title'); ?> - <?php echo e(tenant('id')); ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <?php echo $__env->yieldContent('styles'); ?>
</head>
<body class="<?php echo $__env->yieldContent('body-class'); ?>">
    <?php echo $__env->yieldContent('content'); ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <?php echo $__env->yieldContent('scripts'); ?>
</body>
</html><?php /**PATH C:\Users\User\Documents\BukSkwela\resources\views/layout/layoutTemplate.blade.php ENDPATH**/ ?>