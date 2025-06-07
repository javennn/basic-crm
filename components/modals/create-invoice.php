<?php
date_default_timezone_set('Asia/Manila');

// Load PHPMailer
require('C:/xampp/htdocs/basic-crm/phpmailer/src/Exception.php');
require('C:/xampp/htdocs/basic-crm/phpmailer/src/PHPMailer.php');
require('C:/xampp/htdocs/basic-crm/phpmailer/src/SMTP.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$baseUrl = "http://192.168.100.89/basic-crm/payment.php";

$paymentData = generatePaymentToken($baseUrl);
$paymentToken = $paymentData['token'];
$tokenExpiration = $paymentData['expiration'];
$paymentLink = $paymentData['link'];

$paymentTransaction = generateTransactionId();
$transactionId = $paymentTransaction['id'];
$tidExpiration = $paymentTransaction['expiration'];

// Fetch email template from the database
$sqlFetchMail = "SELECT * FROM `invoice_mailing` WHERE `inv_status` = 'new'";
$resultFetchMail = mysqli_query($conn, $sqlFetchMail);
$dataMail = mysqli_fetch_assoc($resultFetchMail);

// Fetch the next invoice number
$queryInvoice = "SELECT MAX(CAST(SUBSTRING(`inv_number`, 7) AS UNSIGNED)) AS max_id FROM `invoices` WHERE `inv_number` LIKE 'INV24-%'";
$resultInvoice = mysqli_query($conn, $queryInvoice);
$rowInvoice = mysqli_fetch_assoc($resultInvoice);

// Generate the new invoice number
$invNumberInvoice = ($rowInvoice['max_id'] !== null) ? 'INV24-' . str_pad($rowInvoice['max_id'] + 1, 3, '0', STR_PAD_LEFT) : 'INV24-001';

$sqlTableStatus = "SHOW TABLE STATUS LIKE 'invoices'";
$resultTableStatus = mysqli_query($conn, $sqlTableStatus);

// Check if query was successful
if (!$resultTableStatus) {
    die("Query failed: " . mysqli_error($conn));
}

// Fetch the table status
$table_status = mysqli_fetch_assoc($resultTableStatus);

// Get the next auto-increment value
$invId = $table_status['Auto_increment'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['form-type']) && $_POST['form-type'] === 'create_invoice') {
        // Get data from form submission
        $custId = $_POST['cust-id'] ?? '';
        $custName = $_POST['cust-name'] ?? '';
        $custEmail = $_POST['cust-email'] ?? '';
        $invSalesorder = validate($_POST['inv-sales-order'] ?? '');
        $invDuedate = validate($_POST['inv-due-date'] ?? '');
        
        // Get the invoice date and due date from the form
        $invoiceDateTime = date('Y-m-d H:i:s'); // Current invoice date with time
        
        // Get the date part of the invoice date (for comparison)
        $invoiceDate = date('Y-m-d'); // Get only the date (no time)

        // Initialize error message variable
        $dueDateError = false;

        // Check if the due date is the same as the invoice date (same date, no time)
        if (strtotime($invDuedate) == strtotime($invoiceDate)) {
            // Adjust the due date to be the end of the day (23:59:59)
            $invDuedate = $invoiceDate . ' 23:59:59';
            $dueDateError = false;
        } 
        elseif (strtotime($invDuedate) < strtotime($invoiceDate . ' 00:00:00')) {
            // If the due date is earlier than the invoice date, set an error message
            $dueDateError = true;
        } 
        else {
            // For due dates that are different from the invoice date
            // Ensure the time is always set to 23:59:59
            $invDuedate = date('Y-m-d', strtotime($invDuedate)) . ' 23:59:59';
            $dueDateError = false;

            // // Ensure the due date carries the time from the invoice date if set to a different day
            // // If the due date is different, use the time of the invoice date
            // if ($invDuedate != $invoiceDate) {
            //     // Append the invoice time to the due date
            //     $invDuedate = date('Y-m-d', strtotime($invDuedate)) . ' ' . date('H:i:s', strtotime($invoiceDateTime));
            // }
            // $dueDateError = false;  // Clear any previous errors if due date is valid
        }

        // Adjust the due date based on the selected reminder period
        $reminderPeriod = $_POST['reminder-period'] ?? '';

        // Check if the due date is the same as the invoice date
        if (strtotime($invDuedate) == strtotime($invoiceDate)) {
            // Adjust the due date to be the end of the day (23:59:59)
            $invDuedate = $invoiceDate . ' 23:59:59';

            // Adjust the due date based on reminder period
            if ($reminderPeriod == '30 minutes') {
                $invDuedate = date('Y-m-d H:i:s', strtotime($invDuedate . ' -30 minutes'));
            } 
            elseif ($reminderPeriod == '1 hour') {
                $invDuedate = date('Y-m-d H:i:s', strtotime($invDuedate . ' -1 hour'));
            }
        } 
        elseif (strtotime($invDuedate) < strtotime($invoiceDate . ' 00:00:00')) {
            // If the due date is earlier than the invoice date, set an error message
            $dueDateError = true;
        } 
        else {
            // For due dates that are different from the invoice date, ensure the time is always set to 23:59:59
            $invDuedate = date('Y-m-d', strtotime($invDuedate)) . ' 23:59:59';
            $dueDateError = false;

            // // Ensure the due date carries the time from the invoice date if set to a different day
            // // If the due date is different, use the time of the invoice date
            // if ($invDuedate != $invoiceDate) {
            //     // Append the invoice time to the due date
            //     $invDuedate = date('Y-m-d', strtotime($invDuedate)) . ' ' . date('H:i:s', strtotime($invoiceDateTime));
            // }
            // $dueDateError = false;  // Clear any previous errors if due date is valid
            
            // Adjust the due date based on reminder period
            if ($reminderPeriod == '30 minutes') {
                $invDuedate = date('Y-m-d H:i:s', strtotime($invDuedate . ' -30 minutes'));
            } 
            elseif ($reminderPeriod == '1 hour') {
                $invDuedate = date('Y-m-d H:i:s', strtotime($invDuedate . ' -1 hour'));
            }
            $dueDateError = false;
        }

        // Check if there's any error
        if ($dueDateError != null && $dueDateError != false) {
            $dueDateError = true;
        }

        $invStatus1 = 'pending';
        $invAmountdue = validate($_POST['inv-amount-due'] ?? '');
        $invAmountpaid = validate($_POST['inv-amount-paid'] ?? '');
        $invPaymentdate = validate($_POST['inv-payment-date'] ?? '');
        $invBillfreq = validate($_POST['inv-billfreq'] ?? '');
        $invPmethod = validate($_POST['inv-pmethod'] ?? '');
        $invNote = validate($_POST['inv-note'] ?? '');

        $emailSentStatus = 0; 

        // Email scheduling option
        $sendEmail = isset($_POST['send-email']);
    
        // Create the invoice only
        if (($sendEmail) == 0) {
            $invStatus1 = 'new';
            $custName = null;
            $custEmail = null;
            $scheduledTime = null;
            $emailSentStatus = null;
            
            // Insert data into invoices table
            $sqlCreateInvoiceOnly = "INSERT INTO `invoices` (
                `cust_id`, `inv_number`, `inv_sales_order`, `inv_date`, 
                `inv_due_date`, `inv_status`, `inv_amount_due`, `inv_amount_paid`, 
                `inv_payment_date`, `inv_billfreq`, 
                `inv_pmethod`, `inv_note`, `cust_name`, `cust_email`, `email_scheduled_time`, `email_sent`
            ) VALUES (
                '$custId', '$invNumberInvoice', '$invSalesorder', NOW(),
                '$invDuedate', '$invStatus1', '$invAmountdue', '$invAmountpaid', 
                '$invPaymentdate', '$invBillfreq', 
                '$invPmethod', '$invNote', '$custName', '$custEmail', '$scheduledTime', '$emailSentStatus'
            )";

            $resultCreateInvoiceOnly = mysqli_query($conn, $sqlCreateInvoiceOnly);

            if ($resultCreateInvoiceOnly) {
                redirect('app.php?to=customers&subpage=view-customer&id=' . $id, 'Success', 'Invoice created succesfully.');
            }
        }
        // Creating the invoice as well as sending a email notification to customer
        else {
            // Set default email scheduling time
            $scheduledTime = date('Y-m-d H:i:s');

            $delayOption = $_POST['delay-email'] ?? 'Immediately';
            
            // Condition based on the selected scheduled date
            switch ($delayOption) {
                case '1 minute':
                    $scheduledTime = date('Y-m-d H:i:s', strtotime('+1 minute'));
                    break;
                case '10 minutes':
                    $scheduledTime = date('Y-m-d H:i:s', strtotime('+10 minutes'));
                    break;
                case '1 hour':
                    $scheduledTime = date('Y-m-d H:i:s', strtotime('+1 hour'));
                    break;
                case '2 hours':
                    $scheduledTime = date('Y-m-d H:i:s', strtotime('+2 hours'));
                    break;
                case '3 hours':
                    $scheduledTime = date('Y-m-d H:i:s', strtotime('+3 hours'));
                    break;
                case 'Immediately':
                    // Handle sending the email Immediately if the checkbox is checked
                    if ($sendEmail) {
                        sendEmailImmediately($custEmail);
                        $emailSentStatus = 1;
                    }
                    break;
            }

            $reminderPeriod = $_POST['reminder-period'];
            
            // Actual invoice creation
            $sqlCreate = "INSERT INTO `invoices` (
                `cust_id`, `inv_number`, `inv_sales_order`, `inv_date`, 
                `inv_due_date`, `inv_status`, `inv_amount_due`, `inv_amount_paid`, 
                `inv_payment_date`, `inv_billfreq`, 
                `inv_pmethod`, `inv_note`, `cust_name`, `cust_email`, `email_scheduled_time`, `email_sent`, `reminder_period`
            ) VALUES (
                '$custId', '$invNumberInvoice', '$invSalesorder', NOW(),
                '$invDuedate', '$invStatus1', '$invAmountdue', '$invAmountpaid', 
                '$invPaymentdate', '$invBillfreq', 
                '$invPmethod', '$invNote', '$custName', '$custEmail', '$scheduledTime', '$emailSentStatus', '$reminderPeriod'
            )";

            $resultCreate = mysqli_query($conn, $sqlCreate);

            // Create the data for invoice payment
            if ($resultCreate) {
                // $paymentStatus = 'for payment';
                // $dueAmount = $invAmountdue;
                // $paymentAmount = '0.00';
                
                $sqlPayment = "INSERT INTO `payments` (
                `cust_id`, `inv_id`, `ptransaction_id`, `ptoken`, `pdate`, `ptransaction_expiration`, `ptoken_expiration`)
                VALUES (
                '$custId', '$invId', '$transactionId', '$paymentToken', NOW(), '$tidExpiration', '$tokenExpiration'
                )";

                $resultPayment = mysqli_query($conn, $sqlPayment);

                // Update customer status
                $sqlUpdateStatus = "UPDATE `customers` SET `cust_status` = 'active' WHERE `id` = '$custId'";
                mysqli_query($conn, $sqlUpdateStatus);

                redirect('app.php?to=customers&subpage=view-customer&id=' . $id, 'Success', 'Invoice created succesfully.');
            } else {
                echo "Failed to create invoice: " . mysqli_error($conn);
            }
        } 
    }
}

// Immediate sending of email notification to customer
function sendEmailImmediately($custEmail) {
    global $conn, $dataMail, $custId, $invId, $transactionId, $tidExpiration, $paymentToken, $tokenExpiration, $invNumberInvoice, $custName, $invoiceDate, $invDuedate, $invAmountdue, $invPmethod, $paymentLink;

    // Create PHPMailer instance
    $mail = new PHPMailer(true);

    $sqlPayment = "INSERT INTO `payments` (
    `cust_id`, `inv_id`, `ptransaction_id`, `ptoken`, `pdate`, `ptransaction_expiration`, `ptoken_expiration`)
    VALUES (
    '$custId', '$invId', '$transactionId', '$paymentToken', NOW(), '$tidExpiration', '$tokenExpiration'
    )";

    try {
        // Email server configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'javennnhabalita@gmail.com'; // Your email
        $mail->Password = 'khowqucwzlvrvmpu'; // Your SMTP password
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('javennnhabalita@gmail.com', 'localhost/basic-crm');
        $mail->addAddress($custEmail); // Customer's email
        $mail->isHTML(true);

        // Prepare email content
        $subject = $dataMail['inv_msubject'];
        $body = $dataMail['inv_mbody'];

        // Replace placeholders in email
        $subject = str_replace('$minvnumber', $invNumberInvoice, $subject);
        $subject = str_replace('$minvcustname', $custName, $subject);

        $body = str_replace('$minvnumber', $invNumberInvoice, $body);
        $body = str_replace('$minvcustname', $custName, $body);

        $invDateFormatted = date('m-d-Y', strtotime($invoiceDate));
        $body = str_replace('$minvdate', $invDateFormatted, $body);

        $invDuedateFormatted = date('m-d-Y', strtotime($invDuedate));
        $body = str_replace('$minvduedate', $invDuedateFormatted, $body);

        $body = str_replace('$minvamountdue', $invAmountdue, $body);
        $body = str_replace('$minvpmethod', $invPmethod, $body);
        $body = str_replace('$minvpaymentlink', $paymentLink, $body);

        $mail->Subject = $subject;
        $mail->Body = nl2br($body); 

        // Send email
        $mail->send();

    } catch (Exception $e) {
        echo "Failed to send email immediately: " . $mail->ErrorInfo;
    }
}
?>

<div class="modal fade" id="createInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="width: 50em; position: absolute; right: -30%; padding: 0;">
                <div class="modal-header">
                    <h3 class="modal-header-label">New invoice for <?php echo convert($row['cust_id'] . '-' . $row['cust_name']);?></h3>

                    <div class="splitter"></div>

                    <button type="button" class="close-btn" data-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x" style="color: #818181; font-size: 1em; margin: 0; padding: 0;"></i>
                    </button>
                </div>

                <div class="modal-body" style="max-height: 50em;">
                <form id="createInvoiceForm" method="POST">
                    <input type="hidden" name="form-type" value="create_invoice">

                    <div style="width: 100%; display: flex; gap: 1em;">
                        <div style="width: 50%;">
                                <input type="hidden" name="cust-id" class="form-inputs-ro" value="<?php echo $id; ?>">
                                
                                <label class="form-labels"> Invoice number </label>
                                <input type="text" name="inv-number" class="form-inputs-ro" readonly value="<?php echo convert($invNumberInvoice); ?>">

                                <label class="form-labels"> Sales order </label>
                                <input type="text" name="inv-sales-order" class="form-inputs" oninput="validateNumber(this)">

                                <label class="form-labels"> Biling </label>
                                <input type="text" name="inv-billfreq" class="form-inputs-ro" readonly value="<?php echo convert($row['cust_billfreq'])?>">

                                <label class="form-labels"> Payment method </label>
                                <input type="text" name="inv-pmethod" class="form-inputs-ro" readonly value="<?php echo convert($row['cust_pmethod'])?>">

                                <label class="form-labels"> Amount due </label>
                                <input type="text" name="inv-amount-due" class="form-inputs" oninput="validateNumber(this)" onblur="addDecimal(this)">
                                <!-- <input type="text" name="inv-amount-due" class="form-inputs" oninput="validateNumber(this)" onblur="addDecimalAndCommas(this)"> -->
                        </div>
                        
                        <div style="width: 50%;">
                            <label class="form-labels"> Status </label>
                            <input type="text" name="inv-status" id="invoiceStatus1" class="form-inputs-ro" readonly value="new">

                            <label class="form-labels"> Invoice date </label>
                            <input type="hidden" id="dateTime" class="form-dates" readonly>
                            <input type="text" id="preferredDateTime" class="form-inputs-ro" readonly>                            
                            
                            <label class="form-labels">Due date</label>
                            <input type="date" id="invDueDateInput" class="form-dates" name="inv-due-date">
                            <div class="form-group-custom-flex" style="margin: 0.1em 0em 0.3em 0em;">
                                <input type="checkbox" name="" class="form-checkboxes" id="">
                                <label class="form-labels-sm"> Use billing frequency instead </label>
                            </div>
                            <p id="duedateError" style="color: #F03B33; font-size: 0.8em; font-weight: bold; display: none;"></p>

                            <label class="form-labels"> Note </label>
                            <textarea id="" oninput="autoResize(this)" name="inv-note" class="form-textareas"></textarea>
                        </div>
                    </div>

                    <div class="seperator-v" style="margin: 1em 0em;"></div>

                    <div style="width: 100%; display: flex; gap: 1em;">
                        <div style="width: 50%;">
                            <label class="form-labels"> Amount paid </label>
                            <input type="text" name="inv-amount-paid" class="form-inputs" oninput="validateNumber(this)" onblur="addDecimal(this)">

                            <label class="form-labels"> Payment date </label>
                            <input type="date" name="inv-payment-date" class="form-dates">
                        </div>

                        <div style="width: 50%;">
                            <label class="form-labels-bold"> Automated mailing </label>
                            <div class="form-group-custom-flex" style="margin: 0.1em 0em 0.3em 0em; align-items: center;">
                                <input type="checkbox" name="send-email" class="form-checkboxes" id="sendEmailToggle" checked>
                                <label class="form-labels-sm"> Enable sending of email notification to customer </label>
                            </div>

                            <div id="customer-fields" style="display: block;">
                                <!-- <label class="form-labels"> Customer name </label> -->
                                <input type="hidden" name="cust-name" class="form-inputs-ro" readonly value="<?php echo convert($row['cust_name']);?>">

                                <!-- <label class="form-labels"> Sent to </label> -->
                                <input type="hidden" name="cust-email" class="form-inputs-ro" readonly value="<?php echo convert($row['cust_email']);?>">

                                <label class="form-labels-sm" style="font-weight: bold;"> Select the time when the email should be sent </label>
                                <select name="delay-email" class="form-selects">
                                    <option value="Immediately">Immediately</option>
                                    <option value="1 minute">1 minute</option>
                                    <option value="10 minutes">10 minutes</option>
                                    <option value="1 hour">1 hour</option>
                                    <option value="2 hours">2 hours</option>
                                    <option value="3 hours">3 hours</option>
                                </select>

                                <div id="paymentReminderPrompt" style="margin: 0.3em 0em 0em 0em; display: none;">
                                    <div style="display: flex; align-items: center; gap: 0.5em;">
                                        <p style="margin: 0; color: #646464; font-size: 0.8em; font-weight: bold;">This feature is set to disabled</p>
                                        <i id="infoIcon" class="bi bi-exclamation-diamond" style="color: #646464; position: relative; cursor: pointer;">
                                            <span id="tooltip" 
                                                style="
                                                    display: none; 
                                                    position: absolute; 
                                                    top: 100%; 
                                                    left: -1110%;         
                                                    color: #E78735;
                                                    background: #ffffff; 
                                                    border: 1px solid #ccc; 
                                                    padding: 0.8em;       
                                                    border-radius: 5px; 
                                                    font-size: 0.8em;
                                                    font-style: normal;
                                                    box-shadow: 0px 2px 4px rgba(0,0,0,0.2); 
                                                    z-index: 10; 
                                                    white-space: nowrap;">
                                                Payment reminder is disabled when the set value on the <br> due date is the same as the invoice date or 1 day ahead of it.
                                            </span>
                                        </i>
                                    </div>
                                </div>

                                <div id="paymentReminder">
                                    <label class="form-labels-bold"> Payment reminder </label>
                                    <div class="form-group-custom-flex" style="margin: 0.1em 0em 0.3em 0em;">
                                        <input type="checkbox" name="remind-payment" class="form-checkboxes" id="paymentReminderToggle" checked>
                                        <label class="form-labels-sm"> Enable sending of email reminder to customer</label>
                                    </div>

                                    <div id="paymentReminderSelection">
                                        <label class="form-labels-sm" style="font-weight: bold;"> Select the schedule when the email reminder should be sent</label>
                                        <select name="reminder-period" id="reminderPeriod" class="form-selects">
                                            <option value="1 day">1 day before the due date</option>
                                            <option value="3 days">3 days before the due date</option>
                                            <option value="5 days">5 days before the due date</option>
                                            <option value="1 week">1 week before the due date</option>
                                            <option value="2 weeks">2 weeks before the due date</option>
                                        </select>
                                    </div>
                                </div>
                                

                                <label class="form-labels-bold"> Overdue </label>
                                <div class="form-group-custom-flex" style="margin: 0.1em 0em 0.3em 0em; align-items: center;">
                                <input type="checkbox" id="overdueCheckbox" class="form-checkboxes" checked>
                                <label class="form-labels-sm"> Automatically inform the customer if invoice is overdue </label>
                            </div>
                            </div>
                        </div>
                    </div>
                    <input type="button" name="create-invoice" class="add-btn" value="Create" style="margin: 1em 0em 0em 0em;" onclick="submitFormCreate()">
                </form>
                </div> 
            </div>
        </div>
    </div>

    