<?php
// Check if the HTTPS protocol is being used
if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
    // If HTTPS is on, set the protocol to 'https://'
    $uri = 'https://';
} else {
    // If HTTPS is not on, set the protocol to 'http://'
    $uri = 'http://';
}

// Append the host name (e.g., www.example.com) to the protocol
$uri .= $_SERVER['HTTP_HOST'];

// Append the current request URI (e.g., /path/to/page) to the host name
$uri .= $_SERVER["REQUEST_URI"];

// Get the base URL without the current path
$uri = rtrim(dirname($uri), '/');

// Redirect to the 'login' page
header('Location: user/login.php');

// Ensure no further code is executed after the redirect
exit;
