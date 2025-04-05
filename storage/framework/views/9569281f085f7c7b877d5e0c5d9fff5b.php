

<?php $__env->startSection('title', 'Super Admin Dashboard'); ?>

<?php $__env->startSection('body-class', 'superadmin-dashboard'); ?>

<?php $__env->startSection('styles'); ?>
<style>
    .dashboard-container {
        padding: 20px;
    }
    .card {
        background: white;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .header {
        margin-bottom: 20px;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container dashboard-container">
    <div class="header">
        <h2 class="page-title">Super Admin Dashboard</h2>
    </div>

    <div class="card">
        <h3>Tenancy Management</h3>
        <div class="content">
            <!-- Add your tenancy management content here -->
        </div>
    </div>

    <form method="POST" action="<?php echo e(route('superadmin.logout')); ?>">
        <?php echo csrf_field(); ?>
        <button type="submit">Logout</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    // Add any dashboard-specific JavaScript here
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.layoutTemplate', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\BukSkwela\resources\views/superadmin/dashboard.blade.php ENDPATH**/ ?>