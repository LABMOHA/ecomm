<?php
require_once 'includes/config.php';

// Clear all session data
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to homepage
redirect('login.php');
