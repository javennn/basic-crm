<div class="modal fade" id="clientModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="width: 50em; position: absolute; right: -30%; padding: 0;">
                <div class="modal-header">
                    <h3 class="modal-header-label">View customer</h3>

                    <div class="splitter"></div>

                    <button type="button" class="close-btn" data-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x" style="color: #818181; font-size: 1em; margin: 0; padding: 0;"></i>
                    </button>
                </div>

                <div class="modal-body" style="max-height: 35em; display: flex; gap: 1.5em; margin: 0em 0em 1em 0em;">
                    <div style="width: 60%;">
                        <label class="form-labels-b" style="margin: 0; font-weight: bold; color: #646464;">Account information</label>
                        <br>

                        <label class="form-labels">Customer id</label>
                        <div
                        style="
                        display: flex;
                        align-items: center;
                        gap: 0.5em;
                        ">
                            <!-- <input type="hidden" id="id" class="form-inputs-ro" readonly value=""> -->
                            <input type="text" name="cust-id" id="custId" class="form-inputs-ro" readonly value="">
                            <input type="text" name="cust-status" id="customerStatus" class="form-inputs-ro" readonly value="">
                        </div>

                        <label class="form-labels">Customer name</label>
                        <input type="text" name="cust-name" id="custName" class="form-inputs-ro" readonly value="">

                        <label class="form-labels">Billing frequency</label>
                        <input type="text" id="custBillFreq" class="form-inputs-ro" readonly value="Quarterly">
                        <select name="cust-billfreq" class="form-selects" style="display: none;">
                            <option value="Monthly">Monthly</option>
                            <option value="Quarterly">Quarterly</option>
                            <option value="Annually">Annually</option>
                        </select>

                        <div class="seperator-v" style="margin: 1em 0em;"></div>

                        <label class="form-labels-b" style="margin: 0; font-weight: bold; color: #646464;">Contact information</label>
                        <br>

                        <label class="form-labels">Email address</label>
                        <input type="text" name="cust-email" id="custEmail" class="form-inputs-ro" readonly value="">

                        <label class="form-labels">Phone number</label>
                        <input type="text" name="cust-phone" id="custPhone" class="form-inputs-ro" readonly value="">

                        <div class="seperator-v" style="margin: 1em 0em;"></div>

                        <label class="form-labels-b" style="margin: 0; font-weight: bold; color: #646464;">Billing address</label>
                        <br>
                        
                        <label class="form-labels">Street address</label>
                        <input type="text" name="cust-street" id="custStreet" class="form-inputs-ro" readonly value="">

                        <label class="form-labels">State/Province</label>
                        <input type="text" name="cust-state" id="custState" class="form-inputs-ro" readonly value="">

                        <label class="form-labels">Postal/ZIP Code</label>
                        <input type="text" name="cust-zip" id="custZip" class="form-inputs-ro" readonly value="">

                        <label class="form-labels">Country</label>
                        <input type="text" name="cust-country" id="custCountry" class="form-inputs-ro" readonly value="">

                        <div class="seperator-v" style="margin: 1em 0em;"></div>

                        <label class="form-labels-b" style="margin: 0; font-weight: bold; color: #646464;">Payment information</label>
                        <br>
                        
                        <label class="form-labels">Payment Method</label>
                        <input type="text" id="custPmethod" class="form-inputs-ro" readonly value="">
                        <select name="cust-pmethod" class="form-selects" style="display: none;">
                            <option value="Credit Card">Credit Card</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                        </select>

                        <label class="form-labels">Credit Card Details (number)</label>
                        <input type="text" name="cust-cdetails" id="custCdetails" class="form-inputs-ro" readonly value="">

                        <div class="seperator-v" style="margin: 1em 0em;"></div>

                        <label class="form-labels-b" style="margin: 0; font-weight: bold; color: #646464;">Tax information</label>
                        <br>
                        
                        <label class="form-labels">TIN number</label>
                        <input type="text" name="cust-tin" id="custTin" class="form-inputs-ro" readonly value=""> 
                    </div>

                    <div style="width: 40%;">
                    </div>
                </div> 
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            // Automatically focus on the username input field when the modal opens
            $('#addDeptModal').on('shown.bs.modal', function () {
                $(this).find('input[name="dept-name-input"]').focus();
            });
        });
    </script>