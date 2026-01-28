<?php
// index.php
require_once 'core/auth.php';
require_once 'core/functions.php';

if (isset($_SESSION['user_id'])) {
    redirect('modules/dashboard/index.php');
} else {
    redirect('modules/auth/login.php');
}
