</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>

<!-- GENERAL -->
    <script>
        // Time how long the prompt message will stay
        $(document).ready(function () {
            setTimeout(function () {
                $(".custom-prompt").fadeOut("slow", function () {
                    $(this).remove();
                });
            }, 4000); // in milliseconds
        });
// End
    </script>
<!-- END -->

<!-- LOGIN -->
    <script>
        $(document).ready(function(){
            $(this).find('input[name="username_input"]').focus();
        });

        $(document).ready(function() {
            // Only set the timeout if the loader is shown
            if ($(".loader-main").hasClass("show")) {
                setTimeout(function() {
                    $(".loader-main").removeClass("show");
                    $(".login-container-sub").removeClass("hide");

                    // Set focus to the username input after the loader is hidden
                    $('input[name="username_input"]').focus();
                }, 30000); // 30 seconds in milliseconds
            }
        });
    // End
    </script>

    <script>
    // Show password toggling
        function togglePasswordVisibility() {
            var passwordField = document.getElementById("userPassword");
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    // End
    </script>
<!-- END -->

<!-- SIDEBAR -->
    <script>
    // Inventory sub menu toggling
        function toggleInventorySubMenu() {
            var submenu = document.getElementById("inventorySubmenu");
            submenu.classList.toggle("active");

            var arrowIcon = document.querySelector('#inventoryLink .arrow-icon i');
            arrowIcon.classList.toggle("fa-chevron-down");
            arrowIcon.classList.toggle("fa-chevron-up");
        }
    // End
    </script>

    <script>
    // Monitoring sub menu toggling
        function toggleMonitoringSubMenu() {
            var submenu = document.getElementById("monitoringSubmenu");
            submenu.classList.toggle("active");

            var arrowIcon = document.querySelector('#monitoringLink .arrow-icon i');
            arrowIcon.classList.toggle("fa-chevron-down");
            arrowIcon.classList.toggle("fa-chevron-up");
        }
    // End
    </script>
<!-- END -->
</html>