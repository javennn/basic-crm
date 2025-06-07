<?php
$query = "SELECT MAX(CAST(SUBSTRING(`cust_id`,6) AS UNSIGNED)) AS max_id FROM `customers` WHERE `cust_id` LIKE 'CU24-%'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

$customerId = ($row['max_id'] !== null) ? 'CU24-' . str_pad($row['max_id'] + 1, 3, '0', STR_PAD_LEFT) : 'CU24-001';

$emailError = '';  // Default value for the email error message

if (isset($_POST['register'])) {
    $custName = validate($_POST['cust-name']);
    $custEmail = validate($_POST['cust-email']);
    $custPhone = validate($_POST['cust-phone']);
    $custStreet = validate($_POST['cust-street']);
    $custState = validate($_POST['cust-state']);
    $custZip = validate($_POST['cust-zip']);
    $custCountry = validate($_POST['cust-country']);
    $custBillFreq = validate($_POST['cust-billfreq']);
    $custPmethod = validate($_POST['cust-pmethod']);
    $custCdetails = validate($_POST['cust-cdetails']);
    $custTin = validate($_POST['cust-tin']);
    $custStatus = validate($_POST['cust-status']);

    // Validate the email format using filter_var
    if (!filter_var($custEmail, FILTER_VALIDATE_EMAIL)) {
        // If the email is not valid, set the error message
        $emailError;
    } else {
        // If email is valid, proceed with the insert
        $emailError = '';  // Clear any previous errors

        $sqlRegister = "INSERT INTO `customers`(
            `cust_id`, 
            `cust_name`,
            `cust_email`, 
            `cust_phone`, 
            `cust_street`, 
            `cust_state`, 
            `cust_zip`, 
            `cust_country`,
            `cust_billfreq`,  
            `cust_pmethod`, 
            `cust_cdetails`, 
            `cust_tin`,
            `cust_status`) 
            VALUES (
            '$customerId',
            '$custName',
            '$custEmail',
            '$custPhone',
            '$custStreet',
            '$custState',
            '$custZip',
            '$custCountry',
            '$custBillFreq',
            '$custPmethod',
            '$custCdetails',
            '$custTin',
            '$custStatus')";

        $resultRegister = mysqli_query($conn, $sqlRegister);

        if ($resultRegister) {
            // redirect('sidebar.php?page=customers', 'Success', 'Customer registered successfully');
        } else {
            echo "Failed to create item: " . mysqli_error($conn);
        }
    }
}

$sqlFetch = "SELECT * FROM `customers` ORDER by cust_id DESC
-- WHERE `item_status` != 'Used'
";
$resultFetch = mysqli_query($conn, $sqlFetch);
?>

<!-- Modals -->

<?php promptMessage();?>
<body>
    <div
    style="
    width: 100%; 
    padding: 1em;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    ">
        <div
        style="
        width: relative; 
        display: flex;
        gap: 1em;
        ">
            <div style="width: 30%;">
            <form id="registerCustomerForm" method="POST">
                <label class="form-labels-b" style="margin: 0; font-weight: bold; color: #646464;">Account information</label>
                <br>

                <div
                style="
                display: flex;
                align-items: center;
                gap: 0.5em;
                ">
                    <div>
                        <label class="form-labels">Customer id</label>
                        <input type="text" name="cust-id" class="form-inputs-ro" readonly value="<?php echo convert($customerId); ?>">
                    </div>

                    <div>
                        <label class="form-labels">Status</label>
                        <input type="text" name="cust-status" id="custNewStatus" class="form-inputs-ro" readonly value="new">
                    </div>
                </div>

                <label class="form-labels">Customer name</label>
                <input type="text" name="cust-name" class="form-inputs">

                <label class="form-labels">Billing</label>
                <select name="cust-billfreq" class="form-selects">
                    <option value="Monthly">Monthly</option>
                    <option value="Quarterly">Quarterly</option>
                    <option value="Annually">Annually</option>
                </select>

                <div class="seperator-v" style="margin: 1em 0em;"></div>

                <label class="form-labels-b" style="margin: 0; font-weight: bold; color: #646464;">Contact information</label>
                <br>

                <label class="form-labels">Email address</label>
                <!-- Hidden input to store the PHP email error message -->
                <input type="hidden" id="emailErrorMsg" value="<?php echo $emailError; ?>">
                <input type="text" name="cust-email" id="custEmailInput" class="form-inputs">
                <p id="emailRegError"
                style="
                margin: 0em 0em 0.3em 0em;
                padding: 0;
                color: #F03B33;
                font-size: 0.8em;
                font-weight: bold;
                display: none;"></p>

                <label class="form-labels">Phone number</label>
                <input type="text" name="cust-phone" class="form-inputs" oninput="validateNumber(this)">

                <div class="seperator-v" style="margin: 1em 0em;"></div>

                <label class="form-labels-b" style="margin: 0; font-weight: bold; color: #646464;">Billing address</label>
                <br>
                
                <label class="form-labels">Street</label>
                <input type="text" name="cust-street" class="form-inputs">

                <label class="form-labels">State/Province</label>
                <input type="text" name="cust-state" class="form-inputs">

                <label class="form-labels">Postal/ZIP Code</label>
                <input type="text" name="cust-zip" class="form-inputs">

                <label class="form-labels">Country</label>
                <input type="text" name="cust-country" class="form-inputs">

                <div class="seperator-v" style="margin: 1em 0em;"></div>

                <label class="form-labels-b" style="margin: 0; font-weight: bold; color: #646464;">Payment information</label>
                <br>
                
                <label class="form-labels">Payment Method</label>
                <select name="cust-pmethod" class="form-selects">
                    <option value="Credit Card">Credit Card</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                </select>

                <label class="form-labels">Credit Card Details (number)</label>
                <input type="text" name="cust-cdetails" class="form-inputs" oninput="validateNumber(this)">

                <div class="seperator-v" style="margin: 1em 0em;"></div>

                <label class="form-labels-b" style="margin: 0; font-weight: bold; color: #646464;">Tax information</label>
                <br>
                
                <label class="form-labels">TIN number</label>
                <input type="text" name="cust-tin" class="form-inputs" oninput="formatTin(this)">

                <button type="submit" name="register" class="add-btn" style="margin-top: 1em;">Register</button>
            </form>
            </div>

            <div style="width: 70%;">

            <div style="margin: 0em 0em 0.5em 0em; width: relative; display: flex; gap: 0.3em; align-items: center;">
                <label class="form-labels-b" style="margin: 0; font-weight: bold; color: #646464;">Customer list</label>
                <div class="splitter"></div>
                <button class="add-btn-sm">Delete</button>
            </div>

            <table id="stockInventoryTables" class="custom-table">
                <thead class="table-head" style="background-color: transparent; border-bottom: 1px solid #F9F9F9;">
                    <tr>
                        <th style="width: 1%;">#</th>
                        <th>id</th>
                        <th>name</th>
                        <th>email</th>
                        <th>billing</th>
                        <th>method</th>
                        <th>status</th>
                        <!-- <th>actions</th> -->
                    </tr>
                </thead>
                <tbody class="table-body">
                    <?php $i = 1; ?>
                    <?php while ($row = mysqli_fetch_assoc($resultFetch)) {?>
                        <tr class="client-btn" onclick="viewCustomer(<?php echo convert($row['id']); ?>)"
                        data-id="<?php echo convert($row['id']);?>"
                        data-cust-id="<?php echo convert($row['cust_id']);?>"
                        data-cust-billfreq="<?php echo convert($row['cust_billfreq']);?>"
                        data-cust-name="<?php echo convert($row['cust_name']);?>"
                        data-cust-email="<?php echo convert($row['cust_email']);?>"
                        data-cust-phone="<?php echo convert($row['cust_phone']);?>"
                        data-cust-street="<?php echo convert($row['cust_street']);?>"
                        data-cust-state="<?php echo convert($row['cust_state']);?>"
                        data-cust-zip="<?php echo convert($row['cust_zip']);?>"
                        data-cust-country="<?php echo convert($row['cust_country']);?>"
                        data-cust-pmethod="<?php echo convert($row['cust_pmethod']);?>"
                        data-cust-cdetails="<?php echo convert($row['cust_cdetails']);?>"
                        data-cust-tin="<?php echo convert($row['cust_tin']);?>"
                        data-cust-status="<?php echo convert($row['cust_status']);?>">
                            <td style="width: 1%;"><?php echo $i++; ?></td>
                            <td><?php echo convert($row['cust_id']);?></td>
                            <td><?php echo convert($row['cust_name']);?></td>
                            <td><?php echo convert($row['cust_email']);?></td>
                            <td><?php echo convert($row['cust_billfreq']);?></td>
                            <td><?php echo convert($row['cust_pmethod']);?></td>
                            <td>
                                <?php
                                if (convert($row['cust_status']) == "new") {
                                    echo '<div class="new-status"><h3>new</h3></div>';
                                } 
                                if (convert($row['cust_status']) == "active") {
                                    echo '<div class="active-status"><h3>active</h3></div>';
                                } 
                                elseif (convert($row['cust_status']) == "collection") {
                                    echo '<div class="collection-status"><h3>collection</h3></div>';
                                }
                                elseif (convert($row['cust_status']) == "overdue") {
                                    echo '<div class="overdue-status"><h3>overdue</h3></div>';
                                }
                                elseif (convert($row['cust_status']) == "cancelled") {
                                    echo '<div class="cancelled-status"><h3>cancelled</h3></div>';
                                }
                                ?>
                            </td>
                            <!-- <td>
                                <a class="view-btn" href="sidebar.php?page=inventory&subpage=view-item&item_main_id=<?php echo convert($row['item_main_id']) ?>">View details</a>
                            </td> -->
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
    
<?php include('./components/footer.php');?>
<script>
    $(document).ready(function() {
        const input = $('input[name="cust-name"]');
        input.focus();

        // Move the cursor to the end of the input field
        input[0].setSelectionRange(input.val().length, input.val().length);
    });

    function viewCustomer(cid) {
        window.location.href = "app.php?to=customers&subpage=view-customer&id=" + cid;
    }

    function validateNumber(input) {
        // Remove non-numeric characters, keeping only numbers
        input.value = input.value.replace(/[^0-9]/g, '');
    }

    function formatTin(input) {
        // Remove any non-numeric characters (keep only digits)
        let value = input.value.replace(/[^0-9]/g, '');

        // Limit the value to 12 digits (since the dashes will add 3 more characters)
        if (value.length > 12) {
            value = value.substring(0, 12);
        }

        // Add hyphens every 3 digits
        let formattedValue = '';
        for (let i = 0; i < value.length; i++) {
            if (i > 0 && i % 3 === 0) {
                formattedValue += '-';
            }
            formattedValue += value[i];
        }

        // Update the input field with the formatted value
        input.value = formattedValue;
    }

    document.addEventListener("DOMContentLoaded", function () {
        const emailErrorMsg = document.getElementById('emailErrorMsg').value;
        const emailError = document.getElementById('emailRegError');
        const emailInput = document.getElementById('custEmailInput');

        // Display PHP error message if any
        if (emailErrorMsg) {
            emailError.innerText = emailErrorMsg;
            emailError.style.display = 'block';
            emailInput.style.border = '1px solid #F03B33';  // Highlight input field with red border
        }

        // JavaScript function for client-side validation before form submission
        document.getElementById('registerCustomerForm').onsubmit = function (e) {
            const email = emailInput.value.trim();

            emailError.style.display = 'none'; // Hide error message by default
            emailInput.style.border = ''; // Reset border

            if (email === '') {
                e.preventDefault(); // Prevent form submission
                emailInput.style.border = '1px solid #F03B33'; // Highlight input field with red border
                emailInput.style.outline = 'none';
                emailError.style.display = 'block'; // Show error message
                emailError.innerText = 'Email address is required.';
            }
            else {
                // Simple email validation
                if (!validateEmail(email)) {
                    e.preventDefault(); // Prevent form submission
                    emailInput.style.border = '1px solid #F03B33'; // Highlight input field with red border
                    emailInput.style.outline = 'none';
                    emailError.style.display = 'block'; // Show error message
                    emailError.innerText = 'Please enter a valid email address.';
                }
            }
        };

        // Basic email validation function
        function validateEmail(email) {
            const regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            return regex.test(email);
        }

        // Reset error message and styles if the user deletes the input
        emailInput.addEventListener('input', function () {
            if (emailInput.value === '') {
                emailError.style.display = 'none'; // Hide error message
                emailInput.style.border = ''; // Reset the border style
                emailInput.style.outline = '1px solid #04AA6D';
            } else {
                // Reset the error message and border style if the user starts typing
                emailError.style.display = 'none'; // Hide error message
                emailInput.style.border = ''; // Reset the border style
                emailInput.style.outline = '1px solid #04AA6D'; // Optional, set a default style when the user types
            }
        });
    });

</script>