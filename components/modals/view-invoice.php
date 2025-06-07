<div class="modal fade" id="viewInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="width: 50em; position: absolute; right: -30%; padding: 0;">
                <div class="modal-header">
                    <h3 class="modal-header-label">View invoice</h3>

                    <div class="splitter"></div>

                    <button type="button" class="close-btn" data-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x" style="color: #818181; font-size: 1em; margin: 0; padding: 0;"></i>
                    </button>
                </div>

                <div class="modal-body" style="max-height: 50em;">
                <form method="POST">
                    <div style="width: 100%; display: flex; gap: 1em;">
                        <div style="width: 50%;">
                                <label class="form-labels"> Invoice number </label>
                                <input type="text" name="inv-number" id="invNumber" class="form-inputs-ro" readonly>

                                <label class="form-labels"> Biling </label>
                                <select name="inv-billfreq" id="invBillfreq" class="form-selects-ro" disabled>
                                    <option value="Monthly">Monthly</option>
                                    <option value="Quarterly">Quarterly</option>
                                    <option value="Annually">Annually</option>
                                </select>

                                <label class="form-labels"> Payment method </label>
                                <select name="inv-pmethod" id="invPmethod" class="form-selects-ro" disabled>
                                    <option value="Credit Card">Credit Card</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                </select>

                                <label class="form-labels"> Amount due </label>
                                <input type="text" name="inv-amount-due" id="invAmountdue" class="form-inputs-ro" readonly>
                        </div>
                        
                        <div style="width: 50%;">
                            <label class="form-labels"> Status </label>
                            <input type="text" name="cust-status" id="invNewStatus" class="form-inputs-ro1" style="display: none;" readonly value="new">
                            <input type="text" name="cust-status" id="invPendingStatus" class="form-inputs-ro1" style="display: none;" readonly value="pending">
                            <input type="text" name="cust-status" id="invPaidStatus" class="form-inputs-ro1" style="display: none;" readonly value="paid">
                            <input type="text" name="cust-status" id="invPartiallyPaidStatus" class="form-inputs-ro1" style="display: none;" readonly value="partially paid">
                            <input type="text" name="cust-status" id="invOverdueStatus" class="form-inputs-ro1" style="display: none;" readonly value="overdue">

                            <!-- <input type="text" name="inv-status" id="invoiceStatus2" class="form-inputs-ro" readonly> -->

                            <label class="form-labels"> Sales order </label>
                            <input type="text" name="inv-sales-order" id="invSalesorder" class="form-inputs-ro" readonly>

                            <label class="form-labels"> Invoice Date </label>
                            <input type="hidden" class="form-dates" readonly>
                            <input type="text" name="inv-date" id="invDate" class="form-inputs-ro" readonly>
                            
                            <label class="form-labels"> Due date </label>
                            <input type="text" name="inv-due-date" id="invDuedate" class="form-dates-ro" readonly>
                        </div>
                    </div>

                    <div class="seperator-v" style="margin: 1em 0em;"></div>

                    <div style="width: 100%; display: flex; gap: 1em;">
                        <div style="width: 50%;">
                            <label class="form-labels"> Amount paid </label>
                            <input type="text" name="inv-amount-paid" id="invAmountpaid" class="form-inputs-ro" readonly>

                            <label class="form-labels"> Note </label>
                            <textarea oninput="autoResize(this)" name="inv-note" id="invNote" class="form-textareas-ro" readonly></textarea>
                        </div>

                        <div style="width: 50%;">
                            <label class="form-labels"> Payment date </label>
                            <input type="text" name="inv-payment-date" id="invPaymentdate" class="form-inputs-ro" readonly>
                        </div>
                    </div>
                </form>
                </div> 
            </div>
        </div>
    </div>

    <script>
        // Textarea autoresizing
        function autoResize(textarea) {
            textarea.style.height = 'auto'; // Reset the height to auto to allow the textarea to grow and shrink dynamically
            textarea.style.height = textarea.scrollHeight + 'px'; // Set the height to the scrollHeight of the textarea
        }
    </script>