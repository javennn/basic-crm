$(document).ready(function(){
    // Automatically focus on the username input field when the modal opens
    $('#createInvoiceModal').on('shown.bs.modal', function () {
        $(this).find('input[name="inv-sales-order"]').focus();
    });
});

// Function to check if the due date is same as or 1 day ahead of the invoice date
function checkDueDateCondition() {
    const invoiceDateInput = document.getElementById('dateTime'); // Invoice date
    const dueDateInput = document.getElementById('invDueDateInput'); // Due date input
    const paymentReminderPrompt = document.getElementById('paymentReminderPrompt'); // Payment reminder prompt

    const invoiceDateValue = new Date(invoiceDateInput.value);
    const dueDateValue = new Date(dueDateInput.value);

    // Reset the time components for accurate day comparison (ignore the time part)
    invoiceDateValue.setHours(0, 0, 0, 0); // Set to midnight
    dueDateValue.setHours(0, 0, 0, 0); // Set to midnight

    // Calculate the difference in days between the invoice date and due date
    const timeDifference = dueDateValue.getTime() - invoiceDateValue.getTime();
    const oneDayInMillis = 24 * 60 * 60 * 1000;

    // Check if the due date is the same as the invoice date or 1 day ahead
    const isSameOrOneDayAhead = timeDifference === 0 || timeDifference === oneDayInMillis;

    if (isSameOrOneDayAhead) {
        // Show the prompt if the condition is met
        paymentReminderPrompt.style.display = 'block';
    } else {
        // Hide the prompt if the condition is not met
        paymentReminderPrompt.style.display = 'none';
    }
}

// Function to disable or enable payment reminder based on the due date
function checkInvoiceAndDueDates() {
    const invoiceDateInput = document.getElementById('dateTime'); // Invoice date
    const dueDateInput = document.getElementById('invDueDateInput'); // Due date input
    const paymentReminderSection = document.getElementById('paymentReminder'); // Payment reminder section
    const reminderPeriodSelect = document.getElementById('reminderPeriod'); // Reminder period select
    const remindPaymentCheckbox = document.querySelector('input[name="remind-payment"]'); // Checkbox

    const paymentReminderPrompt = document.getElementById('paymentReminderPrompt');

    // Get the values and convert them to Date objects
    const invoiceDateValue = new Date(invoiceDateInput.value);
    const dueDateValue = new Date(dueDateInput.value);

    // Reset the time components for accurate day comparison (ignore the time part)
    invoiceDateValue.setHours(0, 0, 0, 0); // Set to midnight
    dueDateValue.setHours(0, 0, 0, 0); // Set to midnight

    // Calculate the difference in days between the invoice date and due date
    const timeDifference = dueDateValue - invoiceDateValue;
    const oneDayInMillis = 24 * 60 * 60 * 1000;
    const isOneDayAhead = timeDifference === oneDayInMillis;
    const isSameDate = invoiceDateValue.getTime() === dueDateValue.getTime();

    const dayDifference = timeDifference / oneDayInMillis;
    
    // Reset all options to enabled initially
    Array.from(reminderPeriodSelect.options).forEach(option => {
        option.disabled = false;
    });

    // Remove options when dayDifference is 2, but keep "1 day" option
    if (dayDifference === 2 || dayDifference === 3) {
        // Remove "3 days", "5 days", "1 week", "2 weeks" options
        Array.from(reminderPeriodSelect.options).forEach(option => {
            if (option.value === "3 days" || option.value === "5 days" || option.value === "1 week" || option.value === "2 weeks") {
                option.remove(); // Remove these options from dropdown
            }
        });

        // Set default value to "1 day"
        reminderPeriodSelect.value = "1 day";
    } 
    else if (dayDifference === 4 || dayDifference === 5) {
        // Remove "5 days", "1 week", "2 weeks" options
        Array.from(reminderPeriodSelect.options).forEach(option => {
            if (option.value === "5 days" || option.value === "1 week" || option.value === "2 weeks") {
                option.remove(); // Remove these options from dropdown
            }
        });

        // Set default value to "1 day"
        reminderPeriodSelect.value = "1 day";
    }
    else if (dayDifference === 6 || dayDifference === 7) {
        // Remove "1 week", "2 weeks" options
        Array.from(reminderPeriodSelect.options).forEach(option => {
            if (option.value === "1 week" || option.value === "2 weeks") {
                option.remove(); // Remove these options from dropdown
            }
        });

        // Set default value to "1 day"
        reminderPeriodSelect.value = "1 day";
    }
    else if (dayDifference === 8 || dayDifference === 9 || dayDifference === 10 || dayDifference === 11 || dayDifference === 12 || dayDifference === 13 || dayDifference === 14) {
        // Remove "2 weeks" option
        Array.from(reminderPeriodSelect.options).forEach(option => {
            if (option.value === "2 weeks") {
                option.remove(); // Remove this option from dropdown
            }
        });

        // Set default value to "1 day"
        reminderPeriodSelect.value = "1 day";
    }
    else if (dayDifference >= 15) {
        // If dayDifference >= 15, restore all options
        // Remove all options first
        while (reminderPeriodSelect.options.length > 0) {
            reminderPeriodSelect.options[0].remove();
        }

        // Add the original options back to the dropdown
        const options = [
            { value: '1 day', text: '1 day before the due date' }, // This should always be there
            { value: '3 days', text: '3 days before the due date' },
            { value: '5 days', text: '5 days before the due date' },
            { value: '1 week', text: '1 week before the due date' },
            { value: '2 weeks', text: '2 weeks before the due date' }
        ];

        // Add the options to the select element
        options.forEach(optionData => {
            const option = document.createElement('option');
            option.value = optionData.value;
            option.text = optionData.text;
            reminderPeriodSelect.appendChild(option); // Add option back to the select
        });

        // Now, move the "1 day" option to the top
        const firstOption = reminderPeriodSelect.querySelector('option[value="1 day"]');
        reminderPeriodSelect.prepend(firstOption); // Move the "1 day" option to the top

        // Set default value for the reminder period (1 day before the due date)
        reminderPeriodSelect.value = "1 day"; // Restore the default value
    } 
    else {
        // If dayDifference is not in the range above, restore all options
        // Remove all options first
        while (reminderPeriodSelect.options.length > 0) {
            reminderPeriodSelect.options[0].remove();
        }

        // Add the original options back to the dropdown
        const options = [
            { value: '1 day', text: '1 day before the due date' }, // This should always be there
            { value: '3 days', text: '3 days before the due date' },
            { value: '5 days', text: '5 days before the due date' },
            { value: '1 week', text: '1 week before the due date' },
            { value: '2 weeks', text: '2 weeks before the due date' }
        ];

        // Add the options to the select element
        options.forEach(optionData => {
            const option = document.createElement('option');
            option.value = optionData.value;
            option.text = optionData.text;
            reminderPeriodSelect.appendChild(option); // Add option back to the select
        });

        // Now, move the "1 day" option to the top
        const firstOption = reminderPeriodSelect.querySelector('option[value="1 day"]');
        reminderPeriodSelect.prepend(firstOption); // Move the "1 day" option to the top

        // Set default value for the reminder period (1 day before the due date)
        reminderPeriodSelect.value = "1 day"; // Restore the default value
    }

    // Disable the payment reminder if the invoice date and due date are the same or 1 day ahead
    if (isSameDate || isOneDayAhead) {
        paymentReminderSection.style.opacity = '0.3'; // Optional: make it look disabled
        paymentReminderSection.querySelectorAll('input, select').forEach(function(element) {
            element.disabled = true; // Disable checkbox and select elements

            paymentReminderPrompt.style.display = 'block'; // Show the prompt message
        });
        paymentReminderSection.style.border = '1px solid #979797'; 
        paymentReminderSection.style.borderRadius = '0.3em';
        paymentReminderSection.style.padding = '0.3em 0.6em';
        paymentReminderSection.style.margin = '0em 0em 0.3em 0em';

        // Set the reminder_period to null or empty string
        reminderPeriodSelect.value = ''; // Set the reminder period to empty
    } 
    else {
        paymentReminderSection.style.opacity = ''; // Reset the opacity
        paymentReminderSection.style.border = '1px solid transparent'; 
        paymentReminderSection.style.borderRadius = '0.3em';
        paymentReminderSection.style.padding = '0';
        paymentReminderSection.querySelectorAll('input, select').forEach(function(element) {
            element.disabled = false; // Enable checkbox and select elements
        });

        // Set the default value for the reminder period (1 day before the due date)
        reminderPeriodSelect.value = '1 day'; // Restore the default value
    }
}

// Call the check function after the modal is shown
$('#createInvoiceModal').on('shown.bs.modal', function () {
    checkInvoiceAndDueDates(); // Check on modal show
});

// Trigger the check when the due date is changed
document.getElementById('invDueDateInput').addEventListener('input', function() {
    checkInvoiceAndDueDates(); // Check on due date input change
});

// Call checkDueDateCondition when the page loads to handle the initial state
document.addEventListener('DOMContentLoaded', checkDueDateCondition);

// Trigger check when the due date is changed
document.getElementById('invDueDateInput').addEventListener('input', function () {
    checkDueDateCondition(); // Check on due date input change
});

// Tooltip show/hide logic
document.getElementById('infoIcon').addEventListener('mouseenter', function () {
    document.getElementById('tooltip').style.display = 'block'; // Show tooltip on hover
    document.getElementById('infoIcon').style.color = '#E78735'; // Show tooltip on hover
});

document.getElementById('infoIcon').addEventListener('mouseleave', function () {
    document.getElementById('tooltip').style.display = 'none'; // Hide tooltip when mouse leaves
    document.getElementById('infoIcon').style.color = '#646464'; // Show tooltip on hover
});

function resetErrorStylesOnInput() {
    const dueDateInput = document.getElementById('invDueDateInput');
    const duedateError = document.getElementById('duedateError');
    const invoiceDateInput = document.getElementById('dateTime'); // Get the invoice date

    // Listen for the input event to reset the error styles
    dueDateInput.addEventListener('input', function () {
        const dueDateValue = new Date(dueDateInput.value);
        const invoiceDateValue = new Date(invoiceDateInput.value);

        const dueDateYear = dueDateValue.getFullYear();
        const currentYear = new Date().getFullYear();

        // Check if the year entered is valid (between 1990 and current year or greater)
        if (dueDateYear >= 1990 && dueDateYear.toString().length <= 4) {
            const dueDateOnly = dueDateValue.toLocaleDateString('en-CA'); // YYYY-MM-DD format
            const invoiceDateOnly = invoiceDateValue.toLocaleDateString('en-CA'); // YYYY-MM-DD format

            // Check if the new due date is valid (not empty and equal to or greater than the invoice date)
            if (dueDateInput.value.trim() !== "" && dueDateOnly >= invoiceDateOnly) {
                dueDateInput.style.border = ''; // Reset border style
                dueDateInput.style.outline = ''; // Reset outline
                duedateError.style.display = 'none'; // Hide the error message
                duedateError.innerText = ''; // Clear the error message
            } 
            else if (dueDateInput.value.trim() !== "") {
                // If the due date is not empty but earlier than the invoice date, reset styles and show error
                dueDateInput.style.border = '1px solid #F03B33'; // Change the border style
                dueDateInput.style.outline = 'none'; // Remove outline
                duedateError.style.display = 'block'; // Show the error message
                duedateError.innerText = 'Due date cannot be earlier than the invoice date.'; // Set the error message
            }
        } 
        else {
            // If the year is invalid (too large or not 4 digits), reset styles and show error
            dueDateInput.style.border = '1px solid #F03B33'; // Change the border style
            dueDateInput.style.outline = 'none'; // Remove outline
            duedateError.style.display = 'block'; // Show the error message
            duedateError.innerText = 'Please enter a valid year.'; // Set the error message
        }
    });
}

function submitFormCreate() {
    const dueDateInput = document.getElementById('invDueDateInput');
    const duedateError = document.getElementById('duedateError');

    // Get the due date and invoice date values
    const dueDateValue = new Date(dueDateInput.value);
    const invoiceDateValue = new Date(document.getElementById('dateTime').value); // Invoice date field

    duedateError.style.display = 'none'; // Hide the error message initially
    duedateError.innerText = ''; // Clear previous error message

    let hasError = false;

    // Validate the year range (e.g., between 1990 and the current year or greater)
    const dueDateYear = dueDateValue.getFullYear();
    const currentYear = new Date().getFullYear();

    // Validate if the entered year is less than 1990 or more than 4 digits
    if (dueDateYear < 1990 || dueDateYear.toString().length > 4) {
        dueDateInput.style.border = '1px solid #F03B33'; // Change the border style of the input field
        dueDateInput.style.outline = 'none'; // Remove outline
        duedateError.style.display = 'block'; // Show the error message
        duedateError.innerText = 'Please enter a valid year.'; // Set the error message
        hasError = true;
    } else if (dueDateInput != null && dueDateInput.value !== "") {
        // Extract only the date part (YYYY-MM-DD)
        const dueDateOnly = dueDateValue.toLocaleDateString('en-CA'); // Using "en-CA" for YYYY-MM-DD format
        const invoiceDateOnly = invoiceDateValue.toLocaleDateString('en-CA'); // Using "en-CA" for YYYY-MM-DD format

        // Validate if the due date is earlier than the invoice date (consider only the date part)
        if (dueDateOnly < invoiceDateOnly) {
            dueDateInput.style.border = '1px solid #F03B33'; // Change the border style of the input field
            dueDateInput.style.outline = 'none'; // Remove outline
            duedateError.style.display = 'block'; // Show the error message
            duedateError.innerText = 'Due date cannot be earlier than the invoice date.'; // Set the error message
            hasError = true;
        }
    } else {
        dueDateInput.style.border = '1px solid #F03B33'; // Change the border style of the input field
        dueDateInput.style.outline = 'none'; // Remove outline
        duedateError.style.display = 'block'; // Show the error message
        duedateError.innerText = 'Due date is required.'; // Set the error message
        hasError = true;
    }

    // If there are no errors, submit the form
    if (!hasError) {
        document.getElementById('createInvoiceForm').submit();
    }
}

// Initialize the error reset function when the page loads or the user interacts with the form
resetErrorStylesOnInput();

function updateDateTime() {
    const now = new Date();

    // Get the formatted date in YYYY-MM-DD format
    const formattedDate = now.getFullYear() + '-' + 
                        String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                        String(now.getDate()).padStart(2, '0');
    
    // Get the formatted time in HH:MM:SS format
    const formattedTime = now.toLocaleTimeString('en-US', { hour12: true });

    // Combine date and time in the format YYYY-MM-DD HH:MM:SS
    const formattedDateTime = formattedDate + ' ' + formattedTime;

    // Update the content of the dateTime input field
    document.getElementById("dateTime").value = formattedDateTime;

    // PREFERRED DATE FORMAT (MM-DD-YYYY)
    const preferredFormattedDateTime = `${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}-${now.getFullYear()}`;

    // Update the content of the preferredDateTime input field
    document.getElementById("preferredDateTime").value = preferredFormattedDateTime;
}

setInterval(updateDateTime, 1000);
updateDateTime();

function validateNumber(input) {
    // Remove non-numeric characters except for a decimal point
    input.value = input.value.replace(/[^0-9.]/g, '');
}

function addDecimal(input) {
    // Only append ".00" if the value is not empty and doesn't already have ".00"
    if (input.value && !input.value.includes('.')) {
        input.value = input.value + '.00';
    }
}

$(document).ready(function() {
    // Resize textarea when modal is shown (This ensures all textareas are visible)
    $('#createInvoiceModal').on('shown.bs.modal', function () {
        // Call autoResize on all textareas when the modal is shown
        resizeTextareas();
    });
});

document.getElementById('sendEmailToggle').addEventListener('change', function() {
    var customerFields = document.getElementById('customer-fields');
    if (this.checked) {
        customerFields.style.display = 'block'; // Show fields if checked
    } else {
        customerFields.style.display = 'none'; // Hide fields if unchecked
    }
});


// Initial state check (in case the page is loaded with the checkbox unchecked)
if (!document.getElementById('sendEmailToggle').checked) {
    document.getElementById('customer-fields').style.display = 'none';
}

document.getElementById('paymentReminderToggle').addEventListener('change', function() {
    const paymentReminderSelection = document.getElementById('paymentReminderSelection');
    const reminderPeriodSelect = document.getElementById('reminderPeriod'); // Reminder period select

    if (this.checked) {
        paymentReminderSelection.style.display = 'block'; // Show fields if checked
        reminderPeriodSelect.value = '1 day'; // Set the reminder period to it's default value
    } else {
        paymentReminderSelection.style.display = 'none'; // Hide fields if unchecked
        reminderPeriodSelect.value = ''; // Set the reminder period to empty
    }
});

// Initial state check (in case the page is loaded with the checkbox unchecked)
if (!document.getElementById('paymentReminderToggle').checked) {
    document.getElementById('paymentReminderSelection').style.display = 'none';
}