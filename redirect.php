<?php require_once('./components/config/function.php');?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="sg.ico" type="image/x-icon"/>
    <link rel="stylesheet" href="./assets/redirect4.css">
    
    <title>Loading...</title>
</head>
<body>
<div class="main-container">
    <div class="login-container">
        <div class="loader-main">
            <div class="loader-submain">
                <div class="logo-container">
                    <label class="logo-bg">basic-crm app</label>
                    <div class="splitter"></div>
                    <label class="ver-bg">v1.18.2025</label>
                </div>
                <svg class="pl" viewBox="0 0 128 128" width="128px" height="128px" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="pl-grad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#04AA6D"></stop>
                            <stop offset="100%" stop-color="#007648"></stop>
                        </linearGradient>
                    </defs>
                    <circle class="pl__ring" r="56" cx="64" cy="64" fill="none" stroke="hsla(0,10%,10%,0.1)" stroke-width="16" stroke-linecap="round"></circle>
                    <path class="pl__worm" d="M92,15.492S78.194,4.967,66.743,16.887c-17.231,17.938-28.26,96.974-28.26,96.974L119.85,59.892l-99-31.588,57.528,89.832L97.8,19.349,13.636,88.51l89.012,16.015S81.908,38.332,66.1,22.337C50.114,6.156,36,15.492,36,15.492a56,56,0,1,0,56,0Z" fill="none" stroke="url(#pl-grad)" stroke-width="16" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="44 1111" stroke-dashoffset="10"></path>
                </svg>

                <label id="loaderLabel" class="loader-label"></label>
                <label id="loaderLabelSub" class="loader-label-sub">⚠️ Do not refresh the page, <br> <span>as the verification will take longer.</span></label>
            </div>
        </div>
    </div>
</div>

<?php
// Get the URL from the query string
$redirectUrl = isset($_GET['to']) ? $_GET['to'] : $redirectToUser; // Default to user if 'to' is not set
?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const labels = [
            "Please wait while we log you in...",
            "✅ Username is valid.",
            "✅ Password is valid.",
            "📝 Identifying your user's role... almost there.",
            "🚀 Redirecting you to the app..."
        ];

        const totalDuration = 2000; // in seconds
        const displayDuration = (totalDuration - (labels.length - 1) * 500) / labels.length; // Calculate duration per label

        let currentIndex = 0;
        const loaderLabel = document.getElementById("loaderLabel");
        const loaderLabelSub = document.getElementById("loaderLabelSub");

        if (totalDuration >= 8000) {
            loaderLabelSub.style.display = "block"; // Show the loaderLabelSub
        } else {
            loaderLabelSub.style.display = "none"; // Ensure it's hidden
        }

        function updateLabel() {
            loaderLabel.style.opacity = 0; // Fade out

            setTimeout(() => {
                loaderLabel.textContent = labels[currentIndex]; // Update text
                loaderLabel.style.opacity = 1; // Fade in
                currentIndex++;

                

                if (currentIndex < labels.length) {
                    setTimeout(updateLabel, displayDuration); // Change message according to calculated duration
                } else {
                    // Final redirect after the last message
                    setTimeout(() => {
                        window.location.href = "<?php echo $redirectUrl; ?>"; // Redirect to the desired page
                    }, 500); // Delay for final redirect
                }
            }, 500); // Delay for fading out
        }

        updateLabel(); // Start the process
    });
</script>
</html>