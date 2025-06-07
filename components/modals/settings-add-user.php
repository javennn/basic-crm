<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="width: 20em; position: absolute; right: 5%; padding: 0;">
            <div class="modal-header">
                <h3 class="modal-header-label">Add user</h3>
                <div class="splitter"></div>
                <button type="button" class="close-btn" data-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x" style="color: #818181; font-size: 1em; margin: 0; padding: 0;"></i>
                </button>
            </div>

            <div class="modal-body">
                <form id="addUserForm" method="POST">
                    <input type="hidden" name="form_type" value="add_user">

                    <label class="form-labels"> Username </label>
                    <input type="text" name="username-input" class="form-inputs" id="usernameInput">
                    <p id="unAddError"></p>

                    <label class="form-labels"> Password </label>
                    <input type="password" name="password-input" class="form-inputs" id="passwordInput">
                    <p id="pwAddError"></p>

                    <label class="form-labels"> Role </label>
                    <select name="user-role-select" class="form-selects">
                        <option value="Admin">Admin</option>
                        <option value="User">User</option>
                    </select>

                    <input type="button" name="add-user" class="add-btn-sm-w" value="Add" onclick="submitFormAdd()">
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function submitFormAdd() {
    // Clear previous error messages
    const usernameError = document.getElementById('unAddError');
    const passwordError = document.getElementById('pwAddError');
    
    usernameError.style.display = 'none';
    usernameError.innerText = '';
    
    passwordError.style.display = 'none';
    passwordError.innerText = '';
    
    // Get input values
    const usernameInput = document.getElementById('usernameInput'); // Get the input element
    const passwordInput = document.getElementById('passwordInput'); // Get the input element

    const username = usernameInput.value.trim();
    const password = passwordInput.value.trim();

    // Validate input fields
    let hasError = false;

    if (username === '') {
        usernameInput.style.border = '1px solid #F03B33'; // Change border style of the input field
        usernameInput.style.outline = 'none';
        usernameError.style.display = 'block'; // Show error
        usernameError.innerText = 'Username is required.';
        hasError = true;
    } else {
        usernameInput.style.border = ''; // Reset border style if no error
    }

    if (password === '') {
        passwordInput.style.border = '1px solid #F03B33'; // Change border style of the input field
        passwordInput.style.outline = 'none';
        passwordError.style.display = 'block'; // Show error
        passwordError.innerText = 'Password is required.';
        hasError = true;
    } else if (password.length < 5) {
        passwordInput.style.border = '1px solid #F03B33'; // Change border style of the input field
        passwordInput.style.outline = 'none';
        passwordError.style.display = 'block'; // Show error
        passwordError.innerText = 'Password must be at least 5 characters long.';
        hasError = true;
    } else {
        passwordInput.style.border = ''; // Reset border style if no error
    }

    // If there are no errors, submit the form
    if (!hasError) {
        document.getElementById('addUserForm').submit();
    }
}
</script>