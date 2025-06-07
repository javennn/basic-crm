<?php
$sqlLogs = "SELECT * FROM `user_activity_logs` ORDER BY id DESC LIMIT 30";
$resultLogs = mysqli_query($conn, $sqlLogs);

if(isset($_POST['read-notif'])) {
    $id = $_POST['id'];

    $sql = "UPDATE `user_activity_logs` SET `is_read` = 1 WHERE `id`=$id";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        echo 'Failed to read notification' . mysqli_error($conn);
    }
}

if(isset($_POST['readall-notif'])) {
    $sql = "UPDATE `user_activity_logs` SET `is_read` = 1";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        echo 'Failed to read all notification' . mysqli_error($conn);
    }
}
?>

<div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="
        width: 30em;
        position: absolute;
        right: 5%;
        padding: 0;
        ">
            <div class="modal-header">
                <div>
                    <h1 class="modal-header-label">Notifications</h1>
                </div>

                <div class="splitter"></div>

                <form method="POST">
                    <button type="submit" name="readall-notif" class="custom-btn-sm">
                        <i class="fa-solid fa-check-double" style="font-size: 1em; margin-right: 0.1em;"></i>Mark all as read
                    </button>
                </form>

                <button type="button" class="close-btn" data-dismiss="modal" aria-label="Close">
                <i class="bi bi-x" style="color: #818181; font-size: 1em; margin: 0; padding: 0;"></i>
                </button>
            </div>

            <div id="modalBody" class="modal-body-n">
                <div class='notif-container'
                style="
                margin: 0;
                padding: 0;
                width: relative;
                height: auto;
                display: flex;
                flex-direction: column;
                ">  
                <?php foreach($resultLogs as $row): ?>
                <div class="notif-content"
                style="
                margin: 0;
                padding: 0.3em 1em;
                width: relative;
                height: auto;
                display: flex;
                gap: 0.8em;
                align-items: flex-start;
                ">
                    <div class="circle"
                    style="
                    margin: 0;
                    padding: 0.5em;
                    width: relative;
                    height: auto;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    background-color: #F3F3F3;
                    border-radius: 3em;
                    ">  
                        <?php if ($row['type'] == 'Success'): ?>
                            <i class="fa-solid fa-circle-check" style="color: #04AA6D; font-size: 1.1em;"></i>
                        <?php elseif ($row['type'] == 'Informational'): ?>
                            <i class="fa-solid fa-circle-exclamation" style="color: #006CBE; font-size: 1.1em;"></i>
                        <?php elseif ($row['type'] == 'Warning'): ?>
                            <i class="fa-solid fa-triangle-exclamation" style="color: #E78835; font-size: 1.1em;"></i>
                        <?php else: ?>
                            <i class="fa-solid fa-circle-minus" style="color: #F03B33; font-size: 1.1em;"></i>
                        <?php endif; ?>
                    </div>

                    <div
                    style="
                    margin: 0;
                    padding: 0;
                    height: auto;
                    display: flex;
                    flex-grow: 1;
                    flex-direction: column;
                    ">
                        <label
                        style="
                        margin: 0;
                        padding: 0;
                        font-size: 0.9em;
                        color: #525252;
                        ">
                        <span
                        style="
                        font-weight: bold;
                        "><?php echo $row['user'];?></span> 
                        <?php echo $row['activity'];?></label>

                        <label
                        style="
                        margin: 0;
                        padding: 0;
                        font-size: 0.7em;
                        color: #8E8E8E;
                        ">
                        <?php 
                            $date = new DateTime($row['timestamp']);
                            $formattedDate = $date->format('D, m-d-Y, h:i A');
                            echo convert($formattedDate);?>
                        </label>

                        <?php if($row['is_read'] != 1): ?>
                            <form method="POST">
                                <input type="hidden" name="id" value="<?php echo $row['id'];?>">
                                <button type="submit" name="read-notif" class="custom-btn-sm-outlined">
                                <i class="fa-solid fa-check" style="font-size: 0.9em; margin-right: 0.1em;"></i> Mark as read
                                </button>
                            </form>
                        <?php endif; ?>

                    </div>
                    
                    <?php if($row['is_read'] != 1): ?>
                    <div
                    style="
                    margin: 0;
                    padding: 0;
                    width: relative;
                    height: auto;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    ">
                        <div class="new-notif-circle"
                        style="
                        height: 0.5em;
                        width: 0.5em;
                        background-color: #006CBE;
                        border-radius: 3em;
                        "></div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="seperator-v"></div>
                <?php endforeach; ?>    </div>
            </div>  

            <div class="modal-footer"
            style="
            margin: 0;
            padding: 0.5em 1em;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            ">
                <a href="" class="redirect-btn">View all notification</a>
            </div>
        </div>
    </div>
</div>