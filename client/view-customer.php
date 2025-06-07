<?php
$id = $_GET['id'];

$sql = "SELECT * FROM `customers` WHERE id = $id LIMIT 1";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

$sqlFetch = "SELECT * FROM `invoices` WHERE `cust_id` = $id ORDER BY inv_number DESC";
$resultFetch = mysqli_query($conn, $sqlFetch);

$invoiceCount = mysqli_num_rows($resultFetch);

// Check and update overdue invoices
$overdueQuery = "SELECT * FROM `invoices` 
                WHERE inv_due_date < CURDATE() 
                AND inv_amount_paid = 0 
                AND inv_status != 'overdue';";

$overdueResult = mysqli_query($conn, $overdueQuery);

if ($overdueResult) {
    while ($data = mysqli_fetch_assoc($overdueResult)) {
        $updateQuery = "UPDATE `invoices` SET `inv_status` = 'overdue' WHERE `id` = $id";
        mysqli_query($conn, $updateQuery);
    }
}
?>

<!-- Modals -->
<?php include('./components/modals/create-invoice.php');?>
<?php include('./components/modals/view-invoice.php');?>
<?php include('./components/modals/mailing-invoice.php');?>

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
        width: 100%; 
        display: flex;
        gap: 1em;
        ">
        <div style="width: 23%;">
            <div style="margin: 0em 0em 0.5em 0em; width: relative; display: flex; gap: 0.3em; align-items: center;">
                <a href="app.php?to=customers" class="normal-btn-sm"><i class="bi bi-arrow-left" style="margin-right: 0.3em;"></i>Back</a>

                <div class="splitter"></div>

                <button id="editCustomerBtn" class="normal-btn-sm" style="width: max-content; padding: 0.3em 0.5em;" title="edit"><i class="fa-regular fa-pen-to-square" style="color: #525252; font-size: 1.3em;"></i></button>
                <button id="saveCustomerBtn" class="normal-btn-sm-d" style="width: max-content; padding: 0.3em 0.5em;" title="save" disabled><i class="fa-regular fa-floppy-disk save-icon" style="font-size: 1.3em;"></i></button>
            </div>
            <form method="POST">
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
                        <input type="hidden" name="id" class="form-inputs-ro1" readonly value="<?php echo convert($row['id']); ?>">
                        <input type="text" name="cust-id" class="form-inputs-ro1" readonly value="<?php echo convert($row['cust_id']); ?>">
                    </div>

                    <div>
                        <label class="form-labels">Status</label>
                            <?php
                                if (convert($row['cust_status']) == "new") {
                                    echo '<input type="text" name="cust-status" id="custNewStatus" class="form-inputs-ro1" readonly value="new">';
                                } 
                                if (convert($row['cust_status']) == "active") {
                                    echo '<input type="text" name="cust-status" id="custActiveStatus" class="form-inputs-ro1" readonly value="active">';
                                } 
                                elseif (convert($row['cust_status']) == "collection") {
                                    echo '<input type="text" name="cust-status" id="custCollectionStatus" class="form-inputs-ro1" readonly value="collection">';
                                }
                                elseif (convert($row['cust_status']) == "overdue") {
                                    echo '<input type="text" name="cust-status" id="custOverdueStatus" class="form-inputs-ro1" readonly value="overdue">';
                                }
                                elseif (convert($row['cust_status']) == "cancelled") {
                                    echo '<input type="text" name="cust-status" id="custCancelledStatus" class="form-inputs-ro1" readonly value="cancelled">';
                                }
                                ?>
                    </div>
                </div>

                <label class="form-labels">Customer name</label>
                <input type="text" name="cust-name" class="form-inputs-ro" readonly value="<?php echo convert($row['cust_name']); ?>">

                <label class="form-labels">Billing</label>
                <select name="cust-billfreq" class="form-selects-ro" disabled>
                    <option value="Monthly" <?php if(convert($row['cust_billfreq']) == 'Monthly') echo 'selected'; ?>>Monthly</option>
                    <option value="Quarterly" <?php if(convert($row['cust_billfreq']) == 'Quarterly') echo 'selected'; ?>>Quarterly</option>
                    <option value="Annually" <?php if(convert($row['cust_billfreq']) == 'Annually') echo 'selected'; ?>>Annually</option>
                </select>

                <div class="seperator-v" style="margin: 1em 0em;"></div>

                <label class="form-labels-b" style="margin: 0; font-weight: bold; color: #646464;">Contact information</label>
                <br>

                <label class="form-labels">Email address</label>
                <input type="text" name="cust-email" class="form-inputs-ro" readonly value="<?php echo convert($row['cust_email']); ?>">

                <label class="form-labels">Phone number</label>
                <input type="text" name="cust-phone" class="form-inputs-ro" readonly value="<?php echo convert($row['cust_phone']); ?>">

                <div class="seperator-v" style="margin: 1em 0em;"></div>

                <label class="form-labels-b" style="margin: 0; font-weight: bold; color: #646464;">Billing address</label>
                <br>
                
                <label class="form-labels">Street</label>
                <input type="text" name="cust-street" class="form-inputs-ro" readonly value="<?php echo convert($row['cust_street']); ?>">

                <label class="form-labels">State/Province</label>
                <input type="text" name="cust-street" class="form-inputs-ro" readonly value="<?php echo convert($row['cust_state']); ?>">

                <label class="form-labels">Postal/ZIP Code</label>
                <input type="text" name="cust-street" class="form-inputs-ro" readonly value="<?php echo convert($row['cust_zip']); ?>">

                <label class="form-labels">Country</label>
                <input type="text" name="cust-street" class="form-inputs-ro" readonly value="<?php echo convert($row['cust_country']); ?>">

                <div class="seperator-v" style="margin: 1em 0em;"></div>

                <label class="form-labels-b" style="margin: 0; font-weight: bold; color: #646464;">Payment information</label>
                <br>
                
                <label class="form-labels">Payment Method</label>
                <select name="cust-pmethod" class="form-selects-ro" disabled>
                    <option value="Credit Card" <?php if(convert($row['cust_pmethod']) == 'Credit Card') echo 'selected'; ?>>Credit Card</option>
                    <option value="Bank Transfer" <?php if(convert($row['cust_pmethod']) == 'Bank Transfer') echo 'selected'; ?>>Bank Transfer</option>
                </select>

                <label class="form-labels">Credit Card Details (number)</label>
                <input type="text" name="cust-cdetails" class="form-inputs-ro" readonly value="<?php echo convert($row['cust_cdetails']); ?>">

                <div class="seperator-v" style="margin: 1em 0em;"></div>

                <label class="form-labels-b" style="margin: 0; font-weight: bold; color: #646464;">Tax information</label>
                <br>
                
                <label class="form-labels">TIN number</label>
                <input type="text" name="cust-cdetails" class="form-inputs-ro" readonly value="<?php echo convert($row['cust_tin']); ?>">
            </form>
        </div>

        <div style="width: 77%;">

            <div style="margin: 0em 0em 0.5em 0em; width: relative; display: flex; gap: 0.4em; align-items: center;">
                <label class="form-labels-b" style="margin: 0; font-weight: bold; color: #646464;">Invoices</label>
                <label class="form-labels" title="Total invoice(s)" style="color: #949494; padding: 0em 0em 0.1em 0em;">
                    <?php echo convert($invoiceCount);?>
                </label>
                <div class="splitter"></div>
                <button id="createInvoiceBtn" class="add-normal-btn-sm"><i class="bi bi-plus-circle" style="margin-right: 0.4em;"></i>Create invoice</button>
                <button id="mailingInvoiceBtn" class="normal-btn-sm"><i class="bi bi-envelope-arrow-up" style="margin-right: 0.4em;"></i>Mailing</button>
            </div>

            <table id="stockInventoryTables" class="custom-table">
                <thead class="table-head" style="background-color: transparent; border-bottom: 1px solid #F9F9F9;">
                    <tr>
                        <th style="width: 1%;">#</th>
                        <th>invoice number</th>
                        <th>invoice date</th>
                        <th>due date</th>
                        <th>amount due</th>
                        <th>amount paid</th>
                        <th>payment date</th>
                        <th>status</th>
                    </tr>
                </thead>
                <tbody class="table-body">
                    <?php $i = 1; ?>
                    <?php while ($row = mysqli_fetch_assoc($resultFetch)) {?>
                        <tr class="invoice-btn"
                        data-cust-id="<?php echo convert($row['id']);?>"
                        data-inv-number="<?php echo convert($row['inv_number']);?>"
                        data-inv-sales-order="<?php echo convert($row['inv_sales_order']);?>"
                        data-inv-date="<?php echo convert($row['inv_date']);?>"
                        data-inv-due-date="<?php echo convert($row['inv_due_date']);?>"
                        data-inv-status="<?php echo convert($row['inv_status']);?>"
                        data-inv-amount-due="<?php echo convert($row['inv_amount_due']);?>"
                        data-inv-amount-paid="<?php echo convert($row['inv_amount_paid']);?>"
                        data-inv-payment-date="<?php echo convert($row['inv_payment_date']);?>"
                        data-inv-billfreq="<?php echo convert($row['inv_billfreq']);?>"
                        data-inv-pmethod="<?php echo convert($row['inv_pmethod']);?>"
                        data-inv-note="<?php echo convert($row['inv_note']);?>">
                            <td style="width: 1%;"><?php echo $i++; ?></td>
                            <td><?php echo convert($row['inv_number']);?></td>
                            <td><?php if($row['inv_date'] != '0000-00-00 00:00:00') { echo date('m-d-Y', strtotime($row['inv_date'])); } ?></td>
                            <td><?php echo date('m-d-Y', strtotime($row['inv_due_date'])); ?></td>
                            <td><?php echo convert($row['inv_amount_due']);?></td>
                            <td><?php echo convert($row['inv_amount_paid']);?></td>
                            <td><?php if($row['inv_payment_date'] != null) { echo date("m-d-Y, g:i A", strtotime($row['inv_payment_date'])); }?></td>
                            <td>
                                <?php
                                if (convert($row['inv_status']) == "new") {
                                    echo '<div class="new-status"><h3>new</h3></div>';
                                } 
                                if (convert($row['inv_status']) == "pending") {
                                    echo '<div class="pending-status"><h3>pending</h3></div>';
                                } 
                                elseif (convert($row['inv_status']) == "paid") {
                                    echo '<div class="paid-status"><h3>paid</h3></div>';
                                }
                                elseif (convert($row['inv_status']) == "partially paid") {
                                    echo '<div class="partiallypaid-status"><h3>partially paid</h3></div>';
                                }
                                elseif (convert($row['inv_status']) == "overdue") {
                                    echo '<div class="overdue-status"><h3>overdue</h3></div>';
                                }
                                ?>
                            </td>
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

<script type="text/javascript" src="./client/scripts/view-customer.js"></script>
<script type="text/javascript" src="./client/scripts/create-invoice.js"></script>
<script type="text/javascript" src="./client/scripts/invoice-mailing.js"></script>
