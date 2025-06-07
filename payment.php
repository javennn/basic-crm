<?php require_once('./components/header.php');?> 

<?php
// Get the token from the query string
$token = isset($_GET['token']) ? $_GET['token'] : null;

// Validate token presence
if (!$token) {
    echo "Token is missing.";
} else {
    // echo "Token received: $token";
}

// Optional: Fetch additional details about the payment
$sql = "SELECT * FROM `payments` WHERE `ptoken` = '$token'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$pid = $row['id'];

if ($result && mysqli_num_rows($result) > 0) {
    $payment = mysqli_fetch_assoc($result);
} else {
    echo "<h1>Invalid Token</h1>";
    exit();
}

$sqlFetchCustomer = "SELECT `customers`.* FROM `customers` INNER JOIN `payments` ON `customers`.id = `payments`.cust_id WHERE `payments`.id = $pid";
$resultFetchCustomer = mysqli_query($conn, $sqlFetchCustomer);

if ($resultFetchCustomer && mysqli_num_rows($resultFetchCustomer) > 0) {
    $rowCustomer = mysqli_fetch_assoc($resultFetchCustomer);
}

$sqlFetchInvoice = "SELECT `invoices`.* FROM `invoices` INNER JOIN `payments` ON `invoices`.id = `payments`.inv_id WHERE `payments`.id = $pid";
$resultFetchInvoice = mysqli_query($conn, $sqlFetchInvoice);

if ($resultFetchInvoice && mysqli_num_rows($resultFetchInvoice) > 0) {
    $rowInvoice = mysqli_fetch_assoc($resultFetchInvoice);
}

if (isset($_POST['submit-payment'])) {
    $invoiceId = validate($_POST['invoice-id']);
    $paymentAmount = validate($_POST['payment-amount']);

    // Update the invoice table with the payment details
    $sqlPayment = "UPDATE `invoices` SET `inv_status` = 'paid', `inv_amount_paid` = $paymentAmount, `inv_payment_date` = NOW() WHERE `id` = $invoiceId";

    $resultPayment = mysqli_query($conn, $sqlPayment);

    if ($resultPayment) {
        redirect('payment.php?token=' . $token, 'Success', 'Payment submitted successfully!');
    }
}
?>

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
        width: 52%; 
        padding: 0;
        margin: 0em 0em 1em 0em;
        ">
            <?php promptMessage();?>
        </div>

        <div 
        style="
        width: 50%; 
        padding: 1.5em 2em;
        box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
        border-radius: 0.5em;
        display: flex;
        gap: 1em;
        ">
            <div
            style="
            width: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 1em 0em;
            ">  
                <?php if($rowInvoice['inv_status'] == 'pending'): ?>
                    <label class="form-labels-b" style=" margin: 0; padding: 0; font-size: 1.6em; color: #646464;">Amount to pay</label>
                <?php elseif($rowInvoice['inv_status'] == 'paid'): ?>
                    <label class="form-labels-b" style=" margin: 0; padding: 0; font-size: 1.6em; color: #04AA6D;"><i class="bi bi-check-circle" style="color: #04AA6D;"></i> Payment Submitted</label>
                <?php endif; ?>

                <?php if($rowInvoice['inv_status'] == 'pending'): ?>
                    <label id="amountDue" class="form-labels-b" style=" margin: 0em 0em 0.3em 0em; padding: 0; line-height: 1.3; font-size: 3.5em; font-weight: bold; color: #006CBE;"><?php echo 'P'. convert(addCommaToDecimal($rowInvoice['inv_amount_due']));?></label>
                <?php elseif($rowInvoice['inv_status'] == 'paid'): ?>
                    <label id="amountDue" class="form-labels-b" style=" margin: 0em 0em 0.3em 0em; padding: 0; line-height: 1.3; font-size: 3.5em; font-weight: bold; color: #04AA6D;"><?php echo 'P'. convert(addCommaToDecimal($rowInvoice['inv_amount_paid']));?></label>
                <?php endif; ?>
                
                <?php if($rowInvoice['inv_status'] != 'pending'): ?>
                    <div style="width: 67%;">
                        <label class="form-labels" style="width: 100%; text-align: center; font-weight: bold;">Date paid</label>
                        <label class="form-labels" style="width: 100%; text-align: center; font-size: 0.8em;"><?php echo date("F d, Y, g:i A", strtotime($rowInvoice['inv_payment_date']));?></label>
                    </div>
                <?php endif; ?>

                <form method="POST" style="display: flex; flex-direction: column; gap: 0.8em; width: 67%; margin: 2em 0em 0em 0em; ">
                    <input type="hidden" name="invoice-id" value="<?php echo convert($rowInvoice['id']);?>"> <!-- Replace 123 with dynamic invoice ID -->

                    <?php if($rowInvoice['inv_status'] != 'paid'): ?>
                        <input type="hidden" name="payment-amount" id="payment" class="form-inputs-ro" readonly value="<?php echo convert($rowInvoice['inv_amount_due'])?>">
                    <?php endif; ?>
                    
                    <?php if($rowInvoice['inv_status'] != 'paid'): ?>
                        <button type="submit" name="submit-payment" class="update-btn" style="width: 100%; font-size: 1.4em; font-weight: bold;">Submit <?php echo 'P'. convert(addCommaToDecimal($rowInvoice['inv_amount_due']));?></button>
                    <?php endif; ?>
                </form>

                <div style="width: 67%; margin: 2em 0em 0em 0em;">
                    <label class="form-labels" style="width: 100%; text-align: center; font-weight: bold;">Transaction id</label>
                    <div style="margin: 0; padding: 0; width: 100%; display: flex; gap: 0.3em;">
                        <input type="text" id="transactionId" class="form-inputs" readonly value="<?php echo convert($row['ptransaction_id']);?>">
                        <button id="copyTransactionId" class="normal-btn-sm" style="width: max-content; padding: 0.3em 0.5em;" title="copy to clipboard">
                            <i id="copyIcon" class="bi bi-copy" style="color: #525252; font-size: 1.3em;"></i>
                        </button>
                    </div>
                    <p id="copyPrompt" class="form-labels" style="width: 100%; margin: 0.8em 0em 0em 0em; color: #006CBE; text-align: center; font-size: 0.8em; font-weight: bold;"></p>
                </div>

            </div>

            <div
            style="
            width: 50%;
            border: 1px solid #DEDEDE;
            border-radius: 0.3em;
            padding: 1em;
            margin: 0;
            ">
            <label class="form-labels-b" style="width: 100%; font-size: 1em; color: #525252; font-weight: bold;">Invoice details</label>

            <div style="width: 100%; display: flex; margin: 0em 0em 0.2em 0em;">
                <label class="form-labels" style="color: #646464;">Status:</label>
                <div class="splitter"></div>

                <?php if($rowInvoice['inv_status'] == 'pending'): ?>
                    <label class="form-labels" style="color: #006CBE; padding: 0em 0.9em; font-size: 0.8em; font-weight: bold; background-color: #D0EBFF; border-radius: 5em; display: flex; align-items: center;"><?php echo 'for payment';?></label>
                <?php elseif($rowInvoice['inv_status'] == 'paid'): ?>
                    <label class="form-labels" style="color: #04AA6D; padding: 0em 0.9em; font-size: 0.8em; font-weight: bold; background-color: #BBF6E0; border-radius: 5em; display: flex; align-items: center;"><?php echo 'paid';?></label>
                <?php endif; ?>
            </div>

            <div style="width: 100%; display: flex; margin: 0em 0em 0.3em 0em;">
                <label class="form-labels" style="color: #646464;">Invoice number:</label>
                <div class="splitter"></div>
                <label class="form-labels" style="color: #343434; font-weight: bold;"><?php echo '#'. convert($rowInvoice['inv_number']);?></label>
            </div>

            <div style="width: 100%; display: flex; margin: 0em 0em 0.3em 0em;">
                <label class="form-labels" style="color: #646464;">Sales order:</label>
                <div class="splitter"></div>
                <label class="form-labels" style="color: #343434;"><?php echo convert($rowInvoice['inv_sales_order']);?></label>
            </div>

            <div style="width: 100%; display: flex; margin: 0em 0em 0.3em 0em;">
                <label class="form-labels" style="color: #646464;">Invoice date:</label>
                <div class="splitter"></div>
                <label class="form-labels" style="color: #343434;"><?php echo date('m-d-Y', strtotime($rowInvoice['inv_date']));?></label>
            </div>

            <div style="width: 100%; display: flex; margin: 0em 0em 0.3em 0em;">
                <label class="form-labels" style="color: #646464; ">Due date:</label>
                <div class="splitter"></div>
                <label class="form-labels" style="color: #343434;"><?php echo date('m-d-Y', strtotime($rowInvoice['inv_due_date']));?></label>
            </div>

            <div class="seperator-v" style="margin: 0.7em 0em; background-color: #F3F3F3;"></div>

            <label class="form-labels-b" style="width: 100%; color: #525252; font-size: 1em; font-weight: bold;">Account</label>

            <div style="width: 100%; display: flex; margin: 0em 0em 0.3em 0em;">
                <label class="form-labels" style="color: #646464;">Name:</label>
                <div class="splitter"></div>
                <label class="form-labels" style="color: #343434;"><?php echo convert($rowCustomer['cust_name']);?></label>
            </div>

            <div style="width: 100%; display: flex; margin: 0em 0em 0.3em 0em;">
                <label class="form-labels" style="color: #646464;">Billing frequency:</label>
                <div class="splitter"></div>
                <label class="form-labels" style="color: #343434;"><?php echo convert($rowCustomer['cust_billfreq']);?></label>
            </div>

            <div style="width: 100%; display: flex; margin: 0em 0em 0.3em 0em;">
                <label class="form-labels" style="color: #646464;">Payment method</label>
                <div class="splitter"></div>
                <label class="form-labels" style="color: #343434;"><?php echo convert($rowCustomer['cust_pmethod']);?></label>
            </div>

            <div class="seperator-v" style="margin: 0.7em 0em; background-color: #F3F3F3;"></div>

            <label class="form-labels-b" style="width: 100%; color: #525252; font-size: 1em; font-weight: bold;">Billing address</label>

            <div style="width: 100%; display: flex; margin: 0em 0em 0.3em 0em;">
                <label class="form-labels" style="color: #646464;">Street:</label>
                <div class="splitter"></div>
                <label class="form-labels" style="color: #343434;"><?php echo convert($rowCustomer['cust_street']);?></label>
            </div>

            <div style="width: 100%; display: flex; margin: 0em 0em 0.3em 0em;">
                <label class="form-labels" style="color: #646464;">State/ Province:</label>
                <div class="splitter"></div>
                <label class="form-labels" style="color: #343434;"><?php echo convert($rowCustomer['cust_state']);?></label>
            </div>

            <div style="width: 100%; display: flex; margin: 0em 0em 0.3em 0em;">
                <label class="form-labels" style="color: #646464;">Postal/ Zip code:</label>
                <div class="splitter"></div>
                <label class="form-labels" style="color: #343434;"><?php echo convert($rowCustomer['cust_zip']);?></label>
            </div>

            <div style="width: 100%; display: flex; margin: 0em 0em 0.3em 0em;">
                <label class="form-labels" style="color: #646464; ">Country:</label>
                <div class="splitter"></div>
                <label class="form-labels" style="color: #343434;"><?php echo convert($rowCustomer['cust_country']);?></label>
            </div>

            </div>
            
        </div>
    </div>
</body>

<script>
    document.getElementById('copyTransactionId').addEventListener('click', function() {
        // Get the transaction ID input field
        const transactionId = document.getElementById('transactionId');

        // Select the text in the input field
        transactionId.select();
        transactionId.setSelectionRange(0, 99999); // For mobile devices

        // Execute the copy command
        document.execCommand('copy');

        // Find the button and prompt elements
        const buttonContainer = document.getElementById('copyTransactionId');
        const copyPrompt = document.getElementById('copyPrompt');

        // Change button style
        buttonContainer.innerHTML = `<i class="bi bi-clipboard-check" style="color: white; font-size: 1.3em;"></i>`;
        buttonContainer.style.border = "1px solid transparent";
        buttonContainer.style.backgroundColor = "#006CBE";
        buttonContainer.style.color = "white";
        buttonContainer.style.padding = "0.3em 0.5em";
        buttonContainer.style.fontSize = "0.8em";
        buttonContainer.style.borderRadius = "0.3em";
        buttonContainer.style.cursor = "not-allowed";

        // Show the prompt
        copyPrompt.innerText = "Copied to clipboard!";
        copyPrompt.style.display = "block";  // Ensure it's visible when shown

        // Optional: Reset the button and prompt back after a few seconds (if desired)
        setTimeout(function() {
            // Restore the original button
            buttonContainer.innerHTML = `<i class="bi bi-copy" style="color: #525252; font-size: 1.3em;"></i>`;
            buttonContainer.style.border = "1px solid #cfcfcf";
            buttonContainer.style.backgroundColor = ""; // Reset to original color
            buttonContainer.style.color = "";
            buttonContainer.style.cursor = "pointer"; // Re-enable the cursor for clicking

            // Hide the prompt again
            copyPrompt.style.display = "none";
        }, 4000); // Button will revert back after 4 seconds
    });
</script>
</html>