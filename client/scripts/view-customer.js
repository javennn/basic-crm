// Enable editing of fields and toggle edit button
document.addEventListener('DOMContentLoaded', function() {
    // Target the button and input fields by their class name
    const editBtn = document.getElementById('editCustomerBtn');
    const inputFields = document.querySelectorAll('.form-inputs-ro');
    const selectFields = document.querySelectorAll('.form-selects-ro');
    const saveBtn = document.getElementById('saveCustomerBtn');

    // Add an event listener to the edit button
    editBtn.addEventListener('click', function() {
        // Check if input fields are enabled or not
        const isEditable = inputFields[0] && inputFields[0].classList.contains('form-inputs');
        
        if (isEditable) {
            // Disable editing and revert the classes and attributes
            inputFields.forEach(function(input) {
                input.setAttribute('readonly', 'readonly');
                input.classList.remove('form-inputs');
                input.classList.add('form-inputs-ro');
            });

            selectFields.forEach(function(select) {
                select.setAttribute('disabled', 'disabled');
                select.classList.remove('form-selects');
                select.classList.add('form-selects-ro');
            });

            saveBtn.setAttribute('disabled', 'disabled');
            saveBtn.classList.remove('normal-btn-sm-e');
            saveBtn.classList.add('normal-btn-sm-d');

            // Change the button icon back to edit (pencil)
            editBtn.innerHTML = '<i class="fa-regular fa-pen-to-square" style="color: #525252; font-size: 1.3em;"></i>';

        } else {
            // Enable editing and change the classes and attributes
            inputFields.forEach(function(input) {
                input.removeAttribute('readonly');
                input.classList.remove('form-inputs-ro');
                input.classList.add('form-inputs');
            });

            selectFields.forEach(function(select) {
                select.removeAttribute('disabled');
                select.classList.remove('form-selects-ro');
                select.classList.add('form-selects');
            });

            saveBtn.removeAttribute('disabled');
            saveBtn.classList.remove('normal-btn-sm-d');
            saveBtn.classList.add('normal-btn-sm-e');

            // Change the button icon to 'xmark' (cancel)
            editBtn.innerHTML = '<i class="fa-solid fa-xmark" style="color: #525252; font-size: 1.3em;"></i>';
        }
    });
});

$(document).ready(function() {
    $('#createInvoiceBtn').on('click', function() {
        // Show the modal
        $('#createInvoiceModal').modal('show');
    });

    // Close modal
    $('.close-btn').on('click', function() {
        $('#createInvoiceModal').modal('hide');
    });
});

$(document).ready(function() {
    $('#mailingInvoiceBtn').on('click', function() {
        // Show the modal
        $('#mailingInvoiceModal').modal('show');
        
    });

    // Close modal
    $('.close-btn').on('click', function() {
        $('#mailingInvoiceModal').modal('hide');
    });
});

$(document).ready(function() {
    $('.invoice-btn').on('click', function() {
        // Get all the customer data from the clicked row
        var id = $(this).data("id");
        var custId = $(this).data("cust-id");
        var invNumber = $(this).data("inv-number");
        var invSalesorder = $(this).data("inv-sales-order");
        var invDate = $(this).data("inv-date");
        var invDuedate = $(this).data("inv-due-date");
        var invStatus = $(this).data("inv-status");
        var invAmountdue = $(this).data("inv-amount-due");
        var invAmountpaid = $(this).data("inv-amount-paid");
        var invPaymentdate = $(this).data('inv-payment-date');
        var dateObj = new Date(invPaymentdate);
        // Manually format the date as '01-01-2025, 2:53 AM'
        var formattedDate = ('0' + dateObj.getDate()).slice(-2) + '-' +
                            ('0' + (dateObj.getMonth() + 1)).slice(-2) + '-' +
                            dateObj.getFullYear() + ', ' +
                            (dateObj.getHours() % 12 || 12) + ':' +
                            ('0' + dateObj.getMinutes()).slice(-2) + ' ' +
                            (dateObj.getHours() >= 12 ? 'PM' : 'AM');

        console.log(formattedDate);  // Outputs: 01-01-2025, 2:53 AM

        var invBillfreq = $(this).data("inv-billfreq");
        var invPmethod = $(this).data("inv-pmethod");
        var invNote = $(this).data("inv-note");

        // Handle invoice status display logic
        if (invStatus == 'new') {
            $('#invNewStatus').css('display', '');
            $('#invNewStatus').val(invStatus);

            $('#invPendingStatus').css('display', 'none');
            $('#invPaidStatus').css('display', 'none');
            $('#invPartiallyPaidStatus').css('display', 'none');
            $('#invOverdueStatus').css('display', 'none');
        } else if (invStatus == 'pending') {
            $('#invPendingStatus').css('display', '');
            $('#invPendingStatus').val(invStatus);

            $('#invNewStatus').css('display', 'none');
            $('#invPaidStatus').css('display', 'none');
            $('#invPartiallyPaidStatus').css('display', 'none');
            $('#invOverdueStatus').css('display', 'none');
        } else if (invStatus == 'paid') {
            $('#invPaidStatus').css('display', '');
            $('#invPaidStatus').val(invStatus);

            $('#invNewStatus').css('display', 'none');
            $('#invPendingStatus').css('display', 'none');
            $('#invPartiallyPaidStatus').css('display', 'none');
            $('#invOverdueStatus').css('display', 'none');
        } else if (invStatus == 'partially paid') {
            $('#invPartiallyPaidStatus').css('display', '');
            $('#invPartiallyPaidStatus').val(invStatus);

            $('#invNewStatus').css('display', 'none');
            $('#invPendingStatus').css('display', 'none');
            $('#invPaidStatus').css('display', 'none');
            $('#invOverdueStatus').css('display', 'none');
        } else if (invStatus == 'overdue') {
            $('#invOverdueStatus').css('display', '');
            $('#invOverdueStatus').val(invStatus);

            $('#invNewStatus').css('display', 'none');
            $('#invPendingStatus').css('display', 'none');
            $('#invPaidStatus').css('display', 'none');
            $('#invPartiallyPaidStatus').css('display', 'none');
        }

        // Log data for debugging
        console.log(
            id, 
            custId, 
            invNumber,
            invSalesorder,
            invDate, 
            invDuedate, 
            invStatus, 
            invAmountdue, 
            invAmountpaid, 
            invPaymentdate, 
            invBillfreq, 
            invPmethod, 
            invNote
        );

        // Populate the modal fields
        $('#id').val(id);  
        $('#custId').val(custId);  
        $('#invNumber').val(invNumber);
        $('#invSalesorder').val(invSalesorder);
        $('#invDate').val(invDate);
        $('#invDuedate').val(invDuedate);
        $('#invAmountdue').val(invAmountdue);
        $('#invAmountpaid').val(invAmountpaid);
        $('#invPaymentdate').val(formattedDate);
        $('#custPmethod').val(invPmethod);
        $('#invBillfreq').val(invBillfreq);
        $('#invNote').val(invNote);

        // Show the modal
        $('#viewInvoiceModal').modal('show');
    });

    // Close modal
    $('.close-btn').on('click', function() {
        $('#viewInvoiceModal').modal('hide');
    });
});