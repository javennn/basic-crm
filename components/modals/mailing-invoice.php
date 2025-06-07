<?php
$sqlFetchMail = "SELECT * FROM `invoice_mailing`";
$resultFetchMail = mysqli_query($conn, $sqlFetchMail);

if (isset($_POST['save-mailing'])) {
    $mSubject = $_POST['subject'];
    $mBody = $_POST['message'];
    $status = $_POST['status']; // Get the status of the template being edited

    // Use the status to update the correct row
    $sqlUpdateMail = "UPDATE `invoice_mailing` SET `inv_msubject`='$mSubject', `inv_mbody`='$mBody' WHERE `inv_status` = '$status'";

    $resultUpdateMail = mysqli_query($conn, $sqlUpdateMail);

    if ($resultUpdateMail) {
        redirect('app.php?to=customers&subpage=view-customer&id=' . $id, 'Success', 'Mailing updated successfully!');
    } else {
        echo "Failed to update mailing " . mysqli_error($conn);
    }
}

?>

<div class="modal fade" id="mailingInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="width: 35em; position: absolute; right: -5%; padding: 0;">
                <div class="modal-header">
                    <h3 class="modal-header-label">Invoice Mailing</h3>

                    <div class="splitter"></div>

                    <button type="button" class="close-btn" data-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x" style="color: #818181; font-size: 1em; margin: 0; padding: 0;"></i>
                    </button>
                </div>

                <div class="modal-body" style="max-height: 50em;">
                    <div
                    style="
                    width: 100%; 
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    ">
                        <div class="tabs-container">
                            <ul class="tab-links">
                                <li data-tab="new-invoice" class="normal-btn-sm active" style="border-radius: 0.3em 0em 0em 0em">New Invoice</li>
                                <li data-tab="for-payment" class="normal-btn-sm" style="border-radius: 0em">For Payment</li>
                                <li data-tab="for-partial-payment" class="normal-btn-sm" style="border-radius: 0em">For Partial Payment</li>
                                <li data-tab="overdue" class="normal-btn-sm" style="border-radius: 0em 0.3em 0em 0em">Overdue</li>
                            </ul>
                        </div>

                        <div class="tab-content">
                            <!-- New Invoice Tab -->
                            <div id="new-invoice" class="tab-pane active">
                                <?php foreach($resultFetchMail as $dataMail): ?>
                                <?php if($dataMail['inv_status'] == 'new'): ?>
                                <form method="POST">
                                    <input type="hidden" name="status" value="new"> <!-- Hidden status field -->
                                    <label class="form-labels">Subject</label>
                                    <input type="text" name="subject" class="form-inputs" value="<?php echo convert($dataMail['inv_msubject']);?>">

                                    <label class="form-labels">Message</label>
                                    <textarea oninput="autoResize(this)" name="message" class="form-textareas"><?php echo convert($dataMail['inv_mbody']);?></textarea>

                                    <button type="submit" name="save-mailing" class="update-btn saveMailingBtn" disabled>Save changes</button>

                                </form>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <!-- For Payment Tab -->
                            <div id="for-payment" class="tab-pane">
                                <?php foreach($resultFetchMail as $dataMail): ?>
                                <?php if($dataMail['inv_status'] == 'pending'): ?>
                                <form method="POST">
                                    <input type="hidden" name="status" value="pending"> <!-- Hidden status field -->
                                    <label class="form-labels">Subject</label>
                                    <input type="text" name="subject" class="form-inputs" value="<?php echo convert($dataMail['inv_msubject']);?>">

                                    <label class="form-labels">Message</label>
                                    <textarea oninput="autoResize(this)" name="message" class="form-textareas"><?php echo convert($dataMail['inv_mbody']);?></textarea>

                                    <button type="submit" name="save-mailing" class="update-btn saveMailingBtn" disabled>Save changes</button>

                                </form>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <!-- For Partial Payment Tab -->
                            <div id="for-partial-payment" class="tab-pane">
                                <?php foreach($resultFetchMail as $dataMail): ?>
                                <?php if($dataMail['inv_status'] == 'partially paid'): ?>
                                <form method="POST">
                                    <input type="hidden" name="status" value="partially paid"> <!-- Hidden status field -->
                                    <label class="form-labels">Subject</label>
                                    <input type="text" name="subject" class="form-inputs" value="<?php echo convert($dataMail['inv_msubject']);?>">

                                    <label class="form-labels">Message</label>
                                    <textarea oninput="autoResize(this)" name="message" class="form-textareas"><?php echo convert($dataMail['inv_mbody']);?></textarea>

                                    <button type="submit" name="save-mailing" class="update-btn saveMailingBtn" disabled>Save changes</button>

                                </form>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <!-- Overdue Tab -->
                            <div id="overdue" class="tab-pane">
                                <?php foreach($resultFetchMail as $dataMail): ?>
                                <?php if($dataMail['inv_status'] == 'overdue'): ?>
                                <form method="POST">
                                    <input type="hidden" name="status" value="overdue"> <!-- Hidden status field -->
                                    <label class="form-labels">Subject</label>
                                    <input type="text" name="subject" class="form-inputs" value="<?php echo convert($dataMail['inv_msubject']);?>">

                                    <label class="form-labels">Message</label>
                                    <textarea oninput="autoResize(this)" name="message" class="form-textareas"><?php echo convert($dataMail['inv_mbody']);?></textarea>

                                    <button type="submit" name="save-mailing" class="update-btn saveMailingBtn" disabled>Save changes</button>
                                </form>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>