<?php ob_start();?>
<?php session_start();?>
<?php require('db_conn.php');?>
<?php
// Input validation
function validate($inputData) {
    global $conn;
    return mysqli_real_escape_string($conn, $inputData);
}
function convert($inputData) {
    return htmlspecialchars($inputData);
}

function addCommaToDecimal($amount) {
    return number_format($amount, 2, '.', ',');
}

// Message prompting
function promptMessage() {
    if(isset($_SESSION['message'])) {
        if($_SESSION['status'] == 'Success') {
            echo '
            <div class="prompt-container">
                <div id="promptMessageSuccess" class="alert alert-warning alert-dismissible fade show custom-prompt" role="alert">
                    <i class="fa-solid fa-circle-check prompt-icon-success"></i>
        
                    <label class="prompt-label-success">'.$_SESSION['message'].'</label>
                </div>
            </div>';
    
            unset($_SESSION['message']);
        }
        elseif($_SESSION['status'] == 'Informational') {
            echo '
            <div class="prompt-container">
                <div id="promptMessageInfo" class="alert alert-warning alert-dismissible fade show custom-prompt" role="alert">
                    <i class="fa-solid fa-circle-exclamation prompt-icon-info"></i>
        
                    <label class="prompt-label-info">'.$_SESSION['message'].'</label>
                </div>
            </div>';
    
            unset($_SESSION['message']);
        }
        else {
            echo '
            <div class="prompt-container">
                <div id="promptMessageFailed" class="alert alert-warning alert-dismissible fade show custom-prompt" role="alert">
                    <i class="fa-solid fa-circle-xmark prompt-icon-failed"></i>
        
                    <label class="prompt-label-failed">'.$_SESSION['message'].'</label>
                </div>
            </div>';
    
            unset($_SESSION['message']);
        }
    }
}

// Message prompting
function promptMessageMain() {
    if(isset($_SESSION['message'])) {
        if($_SESSION['status'] == 'Success') {
            echo '
            <div id="promptMessageSuccess" class="alert alert-warning alert-dismissible fade show custom-prompt" role="alert">
                <i class="fa-solid fa-circle-check prompt-icon-success"></i>
    
                <label class="prompt-label-success">'.$_SESSION['message'].'</label>
            </div>';
    
            unset($_SESSION['message']);
        }
        elseif($_SESSION['status'] == 'Informational') {
            echo '
            <div id="promptMessageInfo" class="alert alert-warning alert-dismissible fade show custom-prompt" role="alert">
                <i class="fa-solid fa-circle-exclamation prompt-icon-info"></i>
    
                <label class="prompt-label-info">'.$_SESSION['message'].'</label>
            </div>';
    
            unset($_SESSION['message']);
        }
        else {
            echo '
            <div id="promptMessageFailed" class="alert alert-warning alert-dismissible fade show custom-prompt" role="alert">
                <i class="fa-solid fa-circle-xmark prompt-icon-failed"></i>
    
                <label class="prompt-label-failed">'.$_SESSION['message'].'</label>
            </div>';
    
            unset($_SESSION['message']);
        }
    }
}

// Page redirecting
function redirect($url, $status, $msg) {
    $_SESSION['status'] = $status;
    $_SESSION['message'] = $msg;
    header('location: '.$url);
    exit(0);
}

function redirectNoPrompt($url) {
    header('location: '.$url);
    exit(0);
}

function redirectWithDelay($url, $delay) {
    // Set the headers for a delayed redirect
    header("Refresh: $delay; url=$url");
    exit(0);
}

function redirectToLoader($userRole, $redirectToAdmin, $redirectToUser) {
    if ($userRole == 'Admin') {
        header('location: redirect.php?to=' . $redirectToAdmin);
    } else {
        header('location: redirect.php?to=' . $redirectToUser);
    }
    exit(0);
}

// Logout
function logoutSession() {
    unset($_SESSION['auth']);
    unset($_SESSION['loggedInUserRole']);
    unset($_SESSION['loggedInUser']);
}

// Log an activity
function logActivity($type, $user, $activity) {
    global $conn;

    $type = validate($type);
    $user = validate($user);
    $activity = validate($activity);

    $query = "INSERT INTO `user_activity_logs` (
    `type`,
    `user`, 
    `activity`) 
    VALUES (
    '$type',
    '$user', 
    '$activity')";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Unable to establish the activity log: " . mysqli_error($conn);
    }
}

// // Function to generate a unique payment token and expiration
// function generatePaymentToken($baseUrl, $expiryHours = 24) {
//     // Generate a 32-character unique token
//     $paymentToken = bin2hex(random_bytes(16));

//     // Calculate token expiration date
//     $tokenExpiration = date('Y-m-d H:i:s', strtotime("+$expiryHours hours"));

//     // Create the payment link
//     $paymentLink = $baseUrl . "&token=" . urlencode($paymentToken);

//     return [
//         'token' => $paymentToken,
//         'expiration' => $tokenExpiration,
//         'link' => $paymentLink
//     ];
// }

// Function to generate a unique payment token and expiration
function generatePaymentToken($baseUrl, $expiryHours = 24) {
    // Generate a 32-character unique token
    $paymentToken = bin2hex(random_bytes(16));

    // Calculate token expiration date
    $tokenExpiration = date('Y-m-d H:i:s', strtotime("+$expiryHours hours"));

    // Check if the base URL already has query parameters
    $separator = (strpos($baseUrl, '?') === false) ? '?' : '&';

    // Create the payment link
    $paymentLink = $baseUrl . $separator . "token=" . urlencode($paymentToken);

    return [
        'token' => $paymentToken,
        'expiration' => $tokenExpiration,
        'link' => $paymentLink
    ];
}

function generateTransactionId($tidExpiryHours = 24) {
    // Generate a 32-character unique transaction id
    $transactionId = bin2hex(random_bytes(16));

    // Calculate token expiration date
    $tidExpiration = date('Y-m-d H:i:s', strtotime("+$tidExpiryHours hours"));

    return [
        'id' => $transactionId,
        'expiration' => $tidExpiration
    ];
}
?>
