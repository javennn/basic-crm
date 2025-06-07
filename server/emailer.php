<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Log any errors to a text file
ini_set('log_errors', 1);
ini_set('error_log', 'C:/xampp/htdocs/basic-crm/components/php_error_log2.txt');

// Database connection and customize functions
require_once('C:/xampp/htdocs/basic-crm/components/config/db_conn.php');

// Load PHPMailer
require('C:/xampp/htdocs/basic-crm/phpmailer/src/Exception.php');
require('C:/xampp/htdocs/basic-crm/phpmailer/src/PHPMailer.php');
require('C:/xampp/htdocs/basic-crm/phpmailer/src/SMTP.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Create PHPMailer instance
$mail = new PHPMailer(true);

// Email template for new invoice notification
$sqlMailNew = "SELECT * FROM `invoice_mailing` WHERE `inv_status` = 'new'";
$resultMailNew = mysqli_query($conn, $sqlMailNew);
$dataMailNew = mysqli_fetch_assoc($resultMailNew);

// Email template for payment reminder
$sqlMailPending = "SELECT * FROM `invoice_mailing` WHERE `inv_status` = 'pending'";
$resultMailPending = mysqli_query($conn, $sqlMailPending);
$dataMailPending = mysqli_fetch_assoc($resultMailPending);

// Email template for overdue invoice
$sqlMailOverdue = "SELECT * FROM `invoice_mailing` WHERE `inv_status` = 'overdue'";
$resultMailOverdue = mysqli_query($conn, $sqlMailOverdue);
$dataMailOverdue = mysqli_fetch_assoc($resultMailOverdue);

$query = "SELECT * FROM `invoices`
        -- For new invoice email notification
        WHERE (`email_scheduled_time` <= NOW() AND `email_sent` = 0 AND `email_scheduled_time` IS NOT NULL)

        OR
        -- For payment reminder
        (`reminder_sent` = 0 AND `reminder_period` IS NOT NULL 
        AND CURDATE() = DATE_SUB(DATE(`inv_due_date`), INTERVAL 
            CASE 
                WHEN `reminder_period` = '1 day' THEN 1 
                WHEN `reminder_period` = '3 days' THEN 3 
                WHEN `reminder_period` = '5 days' THEN 5 
                WHEN `reminder_period` = '1 week' THEN 7 
                WHEN `reminder_period` = '2 weeks' THEN 14 
            END DAY))
            
        OR
        -- For overdue invoices
        (`overdue_sent` = 0 AND `inv_due_date` < CURDATE());";

$result = mysqli_query($conn, $query);

// Updating of invoice status to overdue
$overdueQuery = "SELECT * FROM `invoices` 
                WHERE inv_due_date < CURDATE() 
                AND inv_amount_paid = 0
                AND inv_status != 'overdue';";

$overdueResult = mysqli_query($conn, $overdueQuery);

if ($overdueResult) {
    while ($data = mysqli_fetch_assoc($overdueResult)) {
        $invoiceId = $data['id'];
        $updateQuery = "UPDATE `invoices` SET `inv_status` = 'overdue' WHERE `id` = $invoiceId";
        mysqli_query($conn, $updateQuery);
    }
}

while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['id'];

    $sqlFetchPayment = "SELECT * FROM `payments` WHERE `inv_id` = $id";
    $resultFetchPayment = mysqli_query($conn, $sqlFetchPayment);

    // Check if a matching payment record exists
    if ($resultFetchPayment && mysqli_num_rows($resultFetchPayment) > 0) {
        $rowPayment = mysqli_fetch_assoc($resultFetchPayment);

        // Generate the payment link
        $baseUrl = "http://192.168.100.89/basic-crm/payment.php";
        $paymentLink = $baseUrl . "?token=" . $rowPayment['ptoken'];

        if ($row['email_scheduled_time'] !== 'immediately' && $row['email_sent'] == 0) {
            // Process scheduled email (new invoice notification)
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'javennnhabalita@gmail.com';
                $mail->Password = 'khowqucwzlvrvmpu';
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;

                $mail->setFrom('javennnhabalita@gmail.com', 'localhost/basic-crm');
                $mail->addAddress($row['cust_email']);
                $mail->isHTML(true);

                $subject = $dataMailNew['inv_msubject'];
                $body = $dataMailNew['inv_mbody'];

                $subject = str_replace('$minvnumber', $row['inv_number'], $subject);
                $subject = str_replace('$minvcustname', $row['cust_name'], $subject);

                $body = str_replace('$minvcustname', $row['cust_name'], $body);
                $body = str_replace('$minvnumber', $row['inv_number'], $body);

                $invDateFormatted = date('m-d-Y', strtotime($row['inv_date']));
                $body = str_replace('$minvdate', $invDateFormatted, $body);

                $invDuedateFormatted = date('m-d-Y', strtotime($row['inv_due_date']));
                $body = str_replace('$minvduedate', $invDuedateFormatted, $body);

                $body = str_replace('$minvamountdue', $row['inv_amount_due'], $body);
                $body = str_replace('$minvpmethod', $row['inv_pmethod'], $body);
                $body = str_replace('$minvpaymentlink', $paymentLink, $body);

                $mail->Subject = $subject;
                $mail->Body = nl2br($body);

                $mail->send();

                $updateQuery = "UPDATE `invoices` SET `email_sent` = 1 WHERE `id` = " . $id;
                mysqli_query($conn, $updateQuery);

            } catch (Exception $e) {
                error_log("Failed to send email for invoice ID " . $id . ": " . $mail->ErrorInfo);
            }
        } 
        else if ($row['inv_status'] !== 'overdue' && $row['reminder_period'] !== NULL && $row['reminder_sent'] == 0 && $row['inv_amount_paid'] == 0) {
            // Process reminder email (only when the due date and reminder period match and payment conditions are met)
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'javennnhabalita@gmail.com';
                $mail->Password = 'khowqucwzlvrvmpu';
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;

                $mail->setFrom('javennnhabalita@gmail.com', 'localhost/basic-crm');
                $mail->addAddress($row['cust_email']);
                $mail->isHTML(true);

                $subject = $dataMailPending['inv_msubject'];
                $body = $dataMailPending['inv_mbody'];

                $subject = str_replace('$minvnumber', $row['inv_number'], $subject);
                $subject = str_replace('$minvcustname', $row['cust_name'], $subject);

                $body = str_replace('$minvcustname', $row['cust_name'], $body);
                $body = str_replace('$minvnumber', $row['inv_number'], $body);
                
                $invDateFormatted = date('m-d-Y', strtotime($row['inv_date']));
                $body = str_replace('$minvdate', $invDateFormatted, $body);

                $invDuedateFormatted = date('m-d-Y', strtotime($row['inv_due_date']));
                $body = str_replace('$minvduedate', $invDuedateFormatted, $body);

                $body = str_replace('$minvduedate', $invDuedateFormatted, $body);
                $body = str_replace('$minvamountdue', $row['inv_amount_due'], $body);
                $body = str_replace('$minvpmethod', $row['inv_pmethod'], $body);
                $body = str_replace('$minvpaymentlink', $paymentLink, $body);

                $mail->Subject = $subject;
                $mail->Body = nl2br($body);

                $mail->send();

                $updateQuery = "UPDATE `invoices` SET `reminder_sent` = 1 WHERE `id` = " . $id;
                mysqli_query($conn, $updateQuery);

            } catch (Exception $e) {
                error_log("Failed to send reminder email for invoice ID " . $id . ": " . $mail->ErrorInfo);
            }
        }
        else if ($row['inv_status'] === 'overdue' && $row['inv_amount_paid'] == 0) {
            // Process sending of overdue email to customer, then update the status of the invoice to 'overdue'
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'javennnhabalita@gmail.com';
                $mail->Password = 'khowqucwzlvrvmpu';
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;

                $mail->setFrom('javennnhabalita@gmail.com', 'localhost/basic-crm');
                $mail->addAddress($row['cust_email']);
                $mail->isHTML(true);

                $subject = $dataMailOverdue['inv_msubject'];
                $body = $dataMailOverdue['inv_mbody'];

                $subject = str_replace('$minvnumber', $row['inv_number'], $subject);
                $subject = str_replace('$minvcustname', $row['cust_name'], $subject);

                $body = str_replace('$minvcustname', $row['cust_name'], $body);
                $body = str_replace('$minvnumber', $row['inv_number'], $body);
                
                $invDateFormatted = date('m-d-Y', strtotime($row['inv_date']));
                $body = str_replace('$minvdate', $invDateFormatted, $body);

                $invDuedateFormatted = date('m-d-Y', strtotime($row['inv_due_date']));
                $body = str_replace('$minvduedate', $invDuedateFormatted, $body);

                $body = str_replace('$minvduedate', $invDuedateFormatted, $body);
                $body = str_replace('$minvamountdue', $row['inv_amount_due'], $body);
                $body = str_replace('$minvpmethod', $row['inv_pmethod'], $body);
                $body = str_replace('$minvpaymentlink', $paymentLink, $body);

                $mail->Subject = $subject;
                $mail->Body = nl2br($body);

                $mail->send();

                $updateQuery = "UPDATE `invoices` SET `inv_status` = 'overdue', `overdue_sent` = 1 WHERE `id` = " . $id;
                mysqli_query($conn, $updateQuery);

            } catch (Exception $e) {
                error_log("Failed to send reminder email for invoice ID " . $id . ": " . $mail->ErrorInfo);
            }
        }
    } 
    else {
        error_log("No payment record found for invoice ID: $id");
    }
}

// Close database connection
mysqli_close($conn);

?>
