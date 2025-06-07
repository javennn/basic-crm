$(document).ready(function() {
    // Initially disable all Save buttons
    $('.saveMailingBtn').prop('disabled', true);
    
    // Listen for any input in the "Subject" field
    $('input[name="subject"]').on('input', function() {
        checkChanges($(this).closest('form'));
    });
    
    // Listen for any input in the "Message" textarea field
    $('textarea[name="message"]').on('input', function() {
        checkChanges($(this).closest('form'));
    });

    // Function to check if any input field is changed
    function checkChanges(form) {
        var subject = form.find('input[name="subject"]').val().trim();
        var message = form.find('textarea[name="message"]').val().trim();

        // Find the Save button in the current form
        var saveButton = form.find('.saveMailingBtn');

        // If either subject or message field is changed, enable the Save button
        if (subject !== '' || message !== '') {
            saveButton.prop('disabled', false);
        } else {
            saveButton.prop('disabled', true);
        }
    }

    // Resize textarea when modal is shown (This ensures all textareas are visible)
    $('#mailingInvoiceModal').on('shown.bs.modal', function () {
        // Call autoResize on all textareas when the modal is shown
        resizeTextareas();
    });

    // Click event on tab links
    $('.tab-links li').on('click', function() {
        var tab_id = $(this).data('tab');

        // Remove active class from all tabs and panes
        $('.tab-links li').removeClass('active');
        $('.tab-pane').removeClass('active');

        // Add active class to clicked tab and corresponding content pane
        $(this).addClass('active');
        $('#' + tab_id).addClass('active');

        // Resize textareas in the newly active tab
        resizeTextareas();
    });

    // Add input listener to textareas to resize as user types
    $(document).on('input', '.form-textareas', function() {
        autoResize(this);
    });

    // Resize all textareas when the page loads
    window.onload = function() {
        resizeTextareas();
    };
});

// Function to handle resizing logic for textareas
function resizeTextareas() {
    document.querySelectorAll('.form-textareas').forEach(function(textarea) {
        autoResize(textarea);
    });
}

// Textarea autoresizing function
function autoResize(textarea) {
    textarea.style.height = 'auto'; // Reset height to auto to allow the textarea to grow and shrink
    textarea.style.height = textarea.scrollHeight + 'px'; // Set height based on scrollHeight
}