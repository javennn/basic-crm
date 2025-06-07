<?php require('./server/administration.php');?>

<!-- Modals -->
<?php include('./components/modals/settings-add-user.php');?>
<?php include('./components/modals/settings-update-user.php');?>
<?php include('./components/modals/settings-delete-user.php');?>

<?php promptMessage();?>
<body>
    <div style="width: 70%;">

        <div style="margin: 0em 0em 0.5em 0em; width: relative; display: flex; gap: 0.3em; align-items: center;">
            <label class="form-labels-b" style="margin: 0; font-weight: bold; color: #646464;">User list</label>
            <div class="splitter"></div>
            <button class="add-btn"
                <?php if(mysqli_num_rows($resultUsers) >= $totalUsers) echo 'disabled'; ?>>
                <i class="bi bi-plus-circle" style="margin-right: 0.4em;"></i>Add user
            </button>
        </div>

        <table id="stockInventoryTables" class="custom-table">
            <thead class="table-head" style="background-color: transparent; border-bottom: 1px solid #F9F9F9;">
                <tr>
                    <th>Id</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="table-body">
                <?php while($rowUsers = mysqli_fetch_assoc($resultUsers)) {?>
                    <tr>
                        <td><?php echo convert($rowUsers['id']);?></td>
                        <td><?php echo convert($rowUsers['user_username']);?></td>
                        <td><?php echo convert($rowUsers['user_password']);?></td>
                        <td><?php echo convert($rowUsers['user_role']);?></td>
                        <td>
                            <?php if($rowUsers['user_islock'] != 0 ) {
                                echo "Locked";
                            }
                            else {
                                echo "Active";
                            } ?>
                        </td>
                        <td>
                            <?php
                            // Assuming $row_users['user_islock'] contains the ban status of the user
                            $isLocked = $rowUsers['user_islock'] == 1;
                            $isUnlocked = !$isLocked; // Assuming user is activated if not banned
                            ?>

                            <!-- <form method="POST" id="lockForm_<?php echo convert($rowUsers['id']); ?>" style="display: inline;">
                                <input type="hidden" name="id" value="<?php echo convert($rowUsers['id']); ?>">
                                <input type="submit" name="lock-user" value="Lock" onclick="return confirmLock();" <?php if($isLocked) echo 'disabled'; ?> class="delete-btn-xsm-s">
                            </form>

                            <form method="POST" id="unlockForm_<?php echo convert($rowUsers['id']); ?>" style="display: inline;">
                                <input type="hidden" name="id" value="<?php echo convert($rowUsers['id']); ?>">
                                <input type="submit" name="unlock-user" value="Unlock" onclick="return confirmUnlock();" <?php if($isUnlocked) echo 'disabled'; ?> class="delete-btn-xsm-s">
                            </form> -->
                                    
                            <!-- As one -->
                            <form method="POST" id="toggleForm_<?php echo convert($rowUsers['id']); ?>" style="display: inline;">
                                <input type="hidden" name="form_type" value="toggle_user">

                                <input type="hidden" name="id" value="<?php echo convert($rowUsers['id']); ?>">
                                <button type="submit" name="toggle-user" onclick="return confirmToggle();" class="delete-btn-xsm-s">
                                    <?php if ($isLocked): ?>
                                        <i class="fa-solid fa-lock" title="unlock user"></i>
                                    <?php else: ?>
                                        <i class="fa-solid fa-lock-open" title="lock user"></i>
                                    <?php endif; ?>
                                </button>
                            </form>
                                            
                            <button class="edit-user-btn">Edit</button>

                            <button type="button" class="add-btn-xsm2-s" data-id="<?php echo convert($rowUsers['id']); ?>">Delete</button>
                        </td>
                    </tr>
                <?php }?>
            </tbody>
        </table>
    </div>

<?php include('./components/footer.php');?>
<script type="text/javascript" src="./client/scripts/administration.js"></script>