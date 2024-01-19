<?php
function user_query_search()
{
    global $conn, $page_no, $username;
    if (isset($_GET['query_no'])) {
        $query_search =  !empty($_GET['query_no']) ? sanitizing($_GET['query_no']) : '';
        $admin_search_query = "select * from user_queries where id like '%$query_search%'";
        $admin_search_output = $conn->query($admin_search_query);
        if ($admin_search_output->num_rows > 0) {
            user_query($admin_search_query);
        } else {
            user_query();
        }
    } elseif (isset($_GET['id'])) {
        $id = sanitizing($_GET['id']);
        $get_complaint_query = "select * from user_queries where id='$id';";
        $get_complaint_output = $conn->query($get_complaint_query);
        if ($get_complaint_output->num_rows > 0) {
            $complaint = $get_complaint_output->fetch_assoc();
        }
?>
        <div>
            <form action="admin_dashboard.php?page=Queries&page_no=<?= $page_no ?>" method="post">
                <div class="complaint-view-form">
                    <div class="complaint-textbox">
                        <label for="query_id">ID</label>
                        <input type="text" name="query_status[]" id="query_id" value="<?= $complaint['id'] ?>" readonly>
                    </div>
                    <div class="complaint-textbox">
                        <label for="username" style="padding: 8px 17px;">USERNAME</label>
                        <input type="text" name="username" id="username" value="<?= $complaint['username'] ?>" readonly>
                    </div>
                    <div class="complaint-textbox">
                        <label for="date" style="padding: 8px 36px;">DATE</label>
                        <input type="text" name="query_date" id="date" value="<?= dateconvertion($complaint['query_date'], "d M y") ?>" readonly>
                    </div>
                </div>
                <div class="complaint-view-form">
                    <div class="complaint-textbox">
                        <label for="assigned_by">ASSIGNED BY</label>
                        <input type="text" name="assigned_by" id="assigned_by" value="<?= $complaint['assigned_by'] ?>" readonly>
                    </div>
                    <div class="complaint-textbox">
                        <label for="assigned_on">ASSIGNED ON</label>
                        <input type="text" name="assigned_on" id="assigned_on" value="<?= dateconvertion($complaint['assigned_on'], "d M y") ?>" readonly>
                    </div>
                    <div class="complaint-textbox">
                        <label for="assigned_to">ASSIGNED TO</label>
                        <input type="text" name="assigned_to" id="assigned_to" value="<?= $complaint['assigned_to'] ?>" readonly>
                    </div>
                </div>
                <div class="complaint-view-form">
                    <div class="complaint-textbox">
                        <label for="status" style="padding: 8px 27px;">STATUS</label>
                        <input type="text" name="status" id="status" value="<?= $complaint['status'] ?>" readonly>
                    </div>
                    <div class="complaint-textbox">
                        <label for="reviewed_on">REVIEWED ON</label>
                        <input type="text" name="reviewed_on" id="reviewed_on" value="<?= dateconvertion($complaint['reviewed_on'], "d M y") ?>" readonly>
                    </div>
                    <div class="complaint-textbox">
                        <label for="reviewed_by">REVIEWED BY</label>
                        <input type="text" name="reviewed_by" id="reviewed_by" value="<?= $complaint['reviewed_by'] ?>" readonly>
                    </div>
                </div>
                <div class="query">
                    <label for="query">QUERY</label>
                    <textarea name="complaint_query" id="query"><?= $complaint['query'] ?></textarea>
                </div>
            </form>
        </div>
    <?php
    } else {
    ?>
        <tr style="text-align: center;color:white; background:hsla(246, 100%, 73%, 1);box-shadow:3px 3px 6px rgb(215, 212, 255);">
            <th style="width: 5%;"></th>
            <th style="width: 10%;">USERNAME</th>
            <th style="width: 25%;">QUERY</th>
            <th style="width: 10%;">STATUS</th>
            <th style="width: 10%;">ASSIGNED TO</th>
            <th style="width: 10%;">ASSIGNED BY</th>
            <th style="width: 10%;">ASSIGNED ON</th>
            <th style="width: 10%;">REVIEWED ON</th>
            <th style="width: 10%;">DATE</th>
        </tr>
<?php

        user_query();
    }
    $checkbox_value = !empty($_POST['query_status']) ? $_POST['query_status'] : array();
    if (isset($_POST['reviewed'])) {
        foreach ($checkbox_value as $id) {
            $reviewed_query = "update user_queries set reviewed_by='$username',reviewed_on=current_timestamp,status='REVIEWED' where id='$id';";
            $conn->query($reviewed_query);
        }
    }
}
