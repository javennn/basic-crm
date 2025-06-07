// Add user modal
$(document).ready(function(){
    // Automatically focus on the username input field when the modal opens
    $('#addUserModal').on('shown.bs.modal', function () {
        $(this).find('input[name="username-input"]').focus();
    });
});

$(document).ready(function() {
    $('.add-btn').on('click',function(){
        $('#addUserModal').modal('show');
    });

    $('.close-btn').on('click', function() {
        $('#addUserModal').modal('hide');
    });
});

// Update user modal
$(document).ready(function(){
    // Automatically focus on the username input field when the modal opens
    $('#updateUserModal').on('shown.bs.modal', function () {
        $(this).find('input[name="username-input"]').focus();
    });
});

$(document).ready(function() {
    $('.edit-user-btn').on('click',function(){
        $('#updateUserModal').modal('show');

        $tr = $(this).closest('tr');

        var data = $tr.children('td').map(function() {
            return $(this).text();
        }).get();

        console.log(data);

        $('#idInput').val(data[0]);   
        $('#userUsernameInput').val(data[1]);
        $('#userPasswordInput').val(data[2]);
        $('#userRoleInput').val(data[3]);
    });

    $('.close-btn').on('click', function() {
        $('#updateUserModal').modal('hide');
    });
});

// Delete user modal
$(document).ready(function() {
    $('.add-btn-xsm2-s').on('click', function() {
        const id = $(this).data('id'); // Get the server ID from data attribute
        $('#deleteUserModal').modal('show');

        // Update the href for the confirmation link
        $('#deleteUserModal .add-btn').attr('href', "app.php?to=administration&subpage=delete-user&id=" + id);
    });

    $('.cancel-btn').on('click', function() {
        $('#deleteUserModal').modal('hide');
    });
});