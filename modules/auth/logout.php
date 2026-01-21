<?php
// modules/auth/logout.php
require_once __DIR__ . '/../../core/auth.php';
require_once __DIR__ . '/../../core/functions.php';

session_destroy();
session_start(); // Start new session to set flash
flash('success', 'Anda telah berhasil logout.');
redirect('modules/auth/login.php');
