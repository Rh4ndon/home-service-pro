<?php
include '../models/functions.php';
session_start();

// Check if the user is logged in
echo "
<script>
if (localStorage.getItem('is_logged_in')) {
    // User is logged in, do nothing
} else {
    // User is not logged in, redirect to the login page
    window.location.href = '../index.html?error=You are not logged in.';
}
</script>
";
