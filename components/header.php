<?php require_once('config/function.php'); ?>

<?php
// // Check if the user is not logged in
// if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
//     // Redirect the user to the login page
//     redirect('././index.php', 'Failed', 'Access denied');
//     exit(); // Stop further execution of the script
// }

// $userRole = $_SESSION['loggedInUserRole'];

// Define public pages that can be accessed without authentication
$publicPages = ['index.php', 'payment.php'];

$current_page = basename($_SERVER['PHP_SELF']);

if (!in_array($current_page, $publicPages) && (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true)) {
    // Redirect the user to the login page
    redirect('././index.php', 'Failed', 'Access denied');
    exit(); // Stop further execution of the script
}
$userRole = $_SESSION['loggedInUserRole'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="shortcut icon" href="../../sg.ico" type="image/x-icon"/>

    <!-- Custom styles -->
    <link rel="stylesheet" href="./assets/main-styles2.css">
    <link rel="stylesheet" href="./assets/main-custom-tables-styles1.css">
    <link rel="stylesheet" href="./assets/main-datatables-styles.css" />
    <link rel="stylesheet" href="./assets/sidebar-styles2.css" />
    <link rel="stylesheet" href="./assets/navbar-styles3.css" />

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Fontawesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <?php 
        $to = isset($_GET['to']) ? $_GET['to'] : '';
        $subpage = isset($_GET['subpage']) ? $_GET['subpage'] : '';

        // Define default title
        $title = '';

        // Set title based on page and subpage
        if ($to === 'customers') {
            $title = 'Customers';
        }
        elseif ($to === 'about') {
            $title = 'About';
        }
        elseif ($to === 'services') {
            $title = 'Services';
        }
        elseif ($to === 'administration') {
            $title = 'Administration';
        }

        // Print the title tag with the dynamically set title
        echo "<title>$title</title>";
    ?>
</head>

