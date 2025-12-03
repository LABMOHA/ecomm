<?php
require_once 'includes/config.php';
session_start();


// Destroy the session
session_destroy();
session_unset();

// Redirect to homepage
redirect('login.php');
