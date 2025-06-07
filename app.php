<?php require_once('./components/header.php');?>

<body>
<div class="sidebar" id="sidebar">
        <button class="toggle-btn" id="toggle-btn">
            <i>☰</i><span>Menu</span>
        </button>
        
        <div class="menu-list">
            <a href="?to=customers"><i>🏠</i><span>Customers</span></a>
            <a href="?to=about"><i>ℹ️</i><span>About</span></a>
            <a href="?to=services"><i>💼</i><span>Services</span></a>
            <a href="?to=administration"><i>📞</i><span>Administration</span></a>
        </div>
    </div>

    <div id="contentContainer" class="content">
        <?php include('./components/navbar.php');?>

        <?php
            $to = isset($_GET['to']) ? $_GET['to'] : 'customers';
            
            if ($to === 'customers') {
                if ($subpage === 'view-customer') {
                    include "./client/view-customer.php"; 
                }
                else {
                    include('./client/customers.php'); 
                }
            }
            
            elseif ($to === 'about') {
                include('./client/about.php'); 
            }

            elseif ($to === 'services') {
                include('./client/services.php'); 
            }

            elseif ($to === 'administration') {
                if ($subpage === 'delete-user') {
                    include "./client/delete-user.php"; 
                }
                else {
                    include "./client/administration.php"; 
                }
            }

            else {
                // Optionally handle the case where the page doesn't exist
                include("./client/$to"); // Or redirect or show an error
            }    
        ?>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggle-btn');

        // Load sidebar state from localStorage
        const sidebarState = localStorage.getItem('sidebarState');
        if (sidebarState === 'mini') {
            sidebar.classList.add('mini');
            contentContainer.classList.add('mini');
        }

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('mini');
            contentContainer.classList.toggle('mini');

            // Save the sidebar state to localStorage
            if (sidebar.classList.contains('mini')) {
                localStorage.setItem('sidebarState', 'mini');
            } else {
                localStorage.setItem('sidebarState', 'maximized');
            }
        });
    </script>
</body>
</html>
