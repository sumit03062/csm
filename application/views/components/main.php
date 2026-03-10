<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'My Website'; ?></title>
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>

<body>
    <?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid px-2 px-sm-3 px-lg-4">
            <a class="navbar-brand fw-bold" href="<?= base_url() ?>">
                <i class="fas fa-blog me-2"></i>CMS
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if ($this->session->userdata('logged_in')): ?>
                        
                        <li class="nav-item">
                            <span class="nav-link">
                                <i class="fas fa-user-circle me-2"></i>
                                <?= html_escape($this->session->userdata('name')) ?>
                            </span>
                        </li>

                        <?php if ($this->session->userdata('role') == 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('admin/dashboard') ?>">
                                    <i class="fas fa-tachometer-alt me-1"></i>Admin
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('admin/users') ?>">
                                    <i class="fas fa-users me-1"></i>Users
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('user/dashboard') ?>">
                                    <i class="fas fa-home me-1"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('user/profile') ?>">
                                    <i class="fas fa-user me-1"></i>Profile
                                </a>
                            </li>
                        <?php endif; ?>

                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('posts') ?>">
                                <i class="fas fa-file-alt me-1"></i>Posts
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link text-warning" href="<?= base_url('logout') ?>">
                                <i class="fas fa-sign-out-alt me-1"></i>Logout
                            </a>
                        </li>

                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('login') ?>">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('register') ?>">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-2 px-sm-3 px-lg-4">
        <!-- Success Messages -->
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Success!</strong>
                <?= html_escape($this->session->flashdata('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Error Messages -->
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Error!</strong>
                <?= html_escape($this->session->flashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Form Validation Errors -->
        <?php if (validation_errors()): ?>
            <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Validation Error!</strong>
                <?= validation_errors() ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    </div>

    <div>

        <?php
        // Load page content dynamically
        if (isset($content)) {
            $this->load->view($content);
        }
        ?>

    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="<?= base_url('assets/js/script.js'); ?>"></script>
</body>

</html>