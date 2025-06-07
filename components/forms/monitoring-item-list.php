<?php require_once('../components/header.php');?>
<?php require('../server/monitoring-item-list.php');?>

<body>
    <table id="dataTable" class="item-list-table">
        <thead class="item-list-table-header">
            <tr>
                <th>Item id</th>
                <th>Description</th>
                <th>Serial no.</th>
                <th>Status</th>
                <th>Date added</th>
            </tr>
        </thead>

        <tbody class="item-list-table-body">
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr data-dismiss="modal" onclick="handleItemClick (
                    <?php echo $row['item_main_id']; ?>, 
                        '<?php echo $row['item_id']; ?>',
                        '<?php echo $row['item_description']; ?>',
                        '<?php echo $row['item_serial']; ?>',
                        '<?php echo $row['item_status']; ?>',
                        '<?php echo $row['date_added']; ?>'
                    )">
                <td><?php echo $row['item_id'];?></td>
                <td><?php echo $row['item_description'];?></td>
                <td><?php echo $row['item_serial'];?></td>
                <td>
                <?php
                    if ($row['item_status'] == "Received") {
                        echo '<div class="received-status"><h3>Received</h3></div>';
                    } 
                    elseif ($row['item_status'] == "Used") {
                        echo '<div class="used-status"><h3>Used</h3></div>';
                    }
                    elseif ($row['item_status'] == "Returned") {
                        echo '<div class="returned-status"><h3>Returned</h3></div>';
                    }
                ?>
                </td>
                <td><?php echo date('F j, Y', strtotime($row['date_added'])); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</body>

<?php require('../components/footer.php');?>
<script type="text/javascript" src="../scripts/monitoring-item-listv1.js"></script>