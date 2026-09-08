<?php
include 'dbconn.php';

function insertRecord($table, $data)
{
    global $conn;
    $columns = implode(", ", array_keys($data));
    // Escape each value
    $escapedValues = array_map(function ($value) use ($conn) {
        return mysqli_real_escape_string($conn, $value);
    }, array_values($data));
    $values = implode("', '", $escapedValues);
    $query = "INSERT INTO $table ($columns) VALUES ('$values')";
    return mysqli_query($conn, $query);
}

function insertOrder($table, $orderData)
{
    global $conn;

    $columns = implode(", ", array_keys($orderData));
    $escapedValues = array_map(function ($value) use ($conn) {
        if ($value === null || $value === '') {
            return 'NULL'; // unquoted NULL for SQL
        }
        return "'" . mysqli_real_escape_string($conn, $value) . "'";
    }, array_values($orderData));

    $values = implode(", ", $escapedValues); // note: no extra quotes now
    $query = "INSERT INTO `$table` ($columns) VALUES ($values)";

    if (!mysqli_query($conn, $query)) {
        throw new Exception("Failed to create order in database: " . mysqli_error($conn));
    }

    return mysqli_insert_id($conn);
}

function editRecord($table, $data, $condition)
{
    global $conn;
    $updateData = [];
    foreach ($data as $column => $value) {
        $escapedValue = mysqli_real_escape_string($conn, $value);
        $updateData[] = "$column = '$escapedValue'";
    }
    $updateString = implode(", ", $updateData);
    $query = "UPDATE $table SET $updateString WHERE $condition";
    return mysqli_query($conn, $query);
}

function deleteRecord($table, $condition)
{
    global $conn;
    // WARNING: $condition must NOT contain unsanitized user input!
    // This function cannot be made safe if $condition is user-controlled.
    $query = "DELETE FROM $table WHERE $condition";
    return mysqli_query($conn, $query);
}

function getAllRecords($table, $condition = '')
{
    global $conn;
    // WARNING: $condition must be safe (e.g., "AND status = 'active'")
    // Do NOT pass raw user input here.
    $query = "SELECT * FROM $table $condition";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getRecord($table, $condition)
{
    global $conn;
    // Same warning: $condition must be pre-validated
    $query = "SELECT * FROM $table WHERE $condition";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

function getRecordMultiTable($table1, $table2, $onCondition, $whereCondition)
{
    global $conn;
    // All parameters assumed safe (not user-controlled)
    $query = "SELECT * FROM $table1 LEFT JOIN $table2 ON $onCondition WHERE $whereCondition";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

function countAllRecords($table, $whereCondition = '1')
{
    global $conn;
    // $whereCondition must be safe
    $query = "SELECT COUNT(*) as total FROM $table WHERE $whereCondition";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

function updateRecord($table, $data, $condition)
{
    global $conn;
    $updates = [];
    foreach ($data as $key => $value) {
        if ($value === null || $value === 'NULL') {
            $updates[] = "$key = NULL";
        } else {
            $updates[] = "$key = '" . mysqli_real_escape_string($conn, $value) . "'";
        }
    }
    $sql = "UPDATE $table SET " . implode(', ', $updates) . " WHERE $condition";
    return mysqli_query($conn, $sql);
}

// Transaction functions unchanged (they don't handle data)
function startTransaction()
{
    global $conn;
    mysqli_autocommit($conn, false);
    mysqli_begin_transaction($conn);
}

function commitTransaction()
{
    global $conn;
    mysqli_commit($conn);
    mysqli_autocommit($conn, true);
}

function rollbackTransaction()
{
    global $conn;
    mysqli_rollback($conn);
    mysqli_autocommit($conn, true);
}

function hideNameGCashStyle($firstName, $lastName)
{
    $hiddenParts = [];

    // Process firstName (can contain multiple names)
    if (!empty($firstName)) {
        $firstNames = explode(' ', trim($firstName));

        foreach ($firstNames as $name) {
            if (empty($name)) {
                continue;
            }

            $length = strlen($name);

            if ($length <= 2) {
                // Short names: show first character only
                $hiddenParts[] = $name[0] . str_repeat('*', $length - 1);
            } else {
                // Longer names: show first, hide middle, show last
                $hiddenParts[] = $name[0] . str_repeat('*', $length - 2) . $name[$length - 1];
            }
        }
    }

    // Process lastName - always show first character with period
    if (!empty($lastName)) {
        $hiddenParts[] = $lastName[0] . '.';
    }

    return implode(' ', $hiddenParts);
}

// ==========================================
// AUTH MODEL FUNCTIONS
// ==========================================

function findUserByEmail($email) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function findAdminByEmail($email) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM admin WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function createUser($name, $email, $password, $role) {
    global $conn;
    $hashed = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO users (Name, Email, Password, Role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashed, $role);
    $stmt->execute();
    return $conn->insert_id;
}

function createAdmin($name, $email, $password) {
    global $conn;
    $hashed = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO admin (Name, Email, Password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashed);
    $stmt->execute();
    return $conn->insert_id;
}

function isEmailTaken($email) {
    global $conn;
    $stmt = $conn->prepare("SELECT ID FROM users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

// ==========================================
// CUSTOMER MODEL FUNCTIONS
// ==========================================

function getCustomerProfileByUserId($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM user_clientprofile WHERE User_ID = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getClientAddressById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM clientaddress WHERE ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getClientContactById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM clientcontact WHERE ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateCustomerProfile($profile_id, $name, $email) {
    global $conn;
    $stmt = $conn->prepare("UPDATE user_clientprofile SET Name = ?, Email = ? WHERE ID = ?");
    $stmt->bind_param("ssi", $name, $email, $profile_id);
    return $stmt->execute();
}

function updateClientAddress($id, $line1, $line2, $brgy, $city, $province, $region, $zip) {
    global $conn;
    $stmt = $conn->prepare("UPDATE clientaddress SET Address_Line1=?, Address_Line2=?, Brgy=?, City_Min=?, Province=?, Region=?, Zip_Code=? WHERE ID=?");
    $stmt->bind_param("sssssssi", $line1, $line2, $brgy, $city, $province, $region, $zip, $id);
    return $stmt->execute();
}

function updateClientContact($id, $prl_mobile, $prl_tel, $sec_mobile, $sec_tel) {
    global $conn;
    $stmt = $conn->prepare("UPDATE clientcontact SET Prl_MobileNo=?, Prl_TelNo=?, Sec_MobileNo=?, Sec_TelNo=? WHERE ID=?");
    $stmt->bind_param("ssssi", $prl_mobile, $prl_tel, $sec_mobile, $sec_tel, $id);
    return $stmt->execute();
}

function getCustomerAppliances($client_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM clientappliances WHERE Client_ID = ?");
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function addCustomerAppliance($client_id, $type, $name, $make, $year, $details) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO clientappliances (Client_ID, Type, Name, Make, Year, Details) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssis", $client_id, $type, $name, $make, $year, $details);
    $stmt->execute();
    return $conn->insert_id;
}

function updateCustomerAppliance($id, $client_id, $type, $name, $make, $year, $details) {
    global $conn;
    $stmt = $conn->prepare("UPDATE clientappliances SET Type=?, Name=?, Make=?, Year=?, Details=? WHERE ID=? AND Client_ID=?");
    $stmt->bind_param("ssssisi", $type, $name, $make, $year, $details, $id, $client_id);
    return $stmt->execute();
}

function deleteCustomerAppliance($id, $client_id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM clientappliances WHERE ID=? AND Client_ID=?");
    $stmt->bind_param("ii", $id, $client_id);
    return $stmt->execute();
}

function getCustomerTickets($client_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT repairticket.*, repairschedule.Date_Time AS ScheduleDate, repairschedule.Status AS ScheduleStatus,
               user_repairmanprofile.Name AS RepairmanName
        FROM repairticket
        LEFT JOIN repairschedule ON repairticket.Schedule_ID = repairschedule.ID
        LEFT JOIN user_repairmanprofile ON repairticket.Repairman_ID = user_repairmanprofile.ID
        WHERE repairticket.Client_ID = ?
        ORDER BY repairticket.ID DESC
    ");
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function createCustomerTicket($client_id, $appliance_type, $appliance_name, $make_brand, $year, $issue_desc, $date_time) {
    global $conn;
    startTransaction();
    try {
        // 1. Insert ticket first (no schedule yet)
        $ticket_id = insertOrder('repairticket', [
            'Client_ID' => $client_id, 'Status' => 'Open', 'Details' => $issue_desc
        ]);
        
        // 2. Insert issue linked to ticket
        $issue_id = insertOrder('applianceissue', [
            'Issue' => $issue_desc, 'Details' => $issue_desc, 'Ticket_ID' => $ticket_id
        ]);
        
        // 3. Insert appliance linked to issue
        $appliance_id = insertOrder('clientappliances', [
            'Client_ID' => $client_id, 'Type' => $appliance_type, 'Name' => $appliance_name,
            'Make' => $make_brand, 'Year' => $year, 'Details' => $issue_desc, 'Issue_ID' => $issue_id
        ]);
        
        // 4. Insert schedule
        $schedule_id = insertOrder('repairschedule', [
            'Client_ID' => $client_id, 'Date_Time' => $date_time, 'Status' => 'Scheduled'
        ]);
        
        // 5. Update ticket with schedule_id
        editRecord('repairticket', ['Schedule_ID' => $schedule_id], "ID = $ticket_id");
        
        commitTransaction();
        return ['ticket_id' => $ticket_id, 'schedule_id' => $schedule_id];
    } catch (Exception $e) {
        rollbackTransaction();
        throw $e;
    }
}

function cancelCustomerTicket($ticket_id, $client_id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE repairticket SET Status = 'Cancelled' WHERE ID = ? AND Client_ID = ?");
    $stmt->bind_param("ii", $ticket_id, $client_id);
    return $stmt->execute();
}

function getCustomerDashboardStats($client_id) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM repairticket WHERE Client_ID = ? AND Status IN ('Open', 'In Progress')");
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    $active_tickets = $stmt->get_result()->fetch_assoc()['total'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM repairschedule WHERE Client_ID = ? AND Status = 'Scheduled' AND Date_Time >= NOW()");
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    $scheduled_repairs = $stmt->get_result()->fetch_assoc()['total'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM clientappliances WHERE Client_ID = ?");
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    $total_appliances = $stmt->get_result()->fetch_assoc()['total'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM repairticket WHERE Client_ID = ? AND Status = 'Completed'");
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    $completed_repairs = $stmt->get_result()->fetch_assoc()['total'];
    
    // Recent tickets
    $stmt = $conn->prepare("
        SELECT repairticket.*, applianceissue.Issue, clientappliances.Name AS ApplianceName 
        FROM repairticket 
        LEFT JOIN applianceissue ON applianceissue.Ticket_ID = repairticket.ID 
        LEFT JOIN clientappliances ON clientappliances.Issue_ID = applianceissue.ID 
        WHERE repairticket.Client_ID = ? 
        ORDER BY repairticket.ID DESC LIMIT 5
    ");
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    $recent_tickets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Upcoming schedule
    $stmt = $conn->prepare("
        SELECT repairschedule.*, user_repairmanprofile.Name AS RepairmanName 
        FROM repairschedule 
        LEFT JOIN user_repairmanprofile ON repairschedule.Repairman_ID = user_repairmanprofile.ID 
        WHERE repairschedule.Client_ID = ? AND repairschedule.Date_Time >= NOW() 
        AND repairschedule.Status IN ('Scheduled', 'In Progress') 
        ORDER BY repairschedule.Date_Time ASC LIMIT 1
    ");
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    $upcoming_schedule = $stmt->get_result()->fetch_assoc();
    
    return compact('active_tickets', 'scheduled_repairs', 'total_appliances', 'completed_repairs', 'recent_tickets', 'upcoming_schedule');
}

function getCustomerSchedule($client_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT repairschedule.*, user_repairmanprofile.Name AS RepairmanName 
        FROM repairschedule 
        LEFT JOIN user_repairmanprofile ON repairschedule.Repairman_ID = user_repairmanprofile.ID 
        WHERE repairschedule.Client_ID = ? 
        ORDER BY repairschedule.Date_Time DESC
    ");
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getCustomerIdFromUser($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT ID AS Client_ID FROM user_clientprofile WHERE User_ID = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// ==========================================
// REPAIRMAN MODEL FUNCTIONS
// ==========================================

function getRepairmanIdFromUser($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT ID FROM user_repairmanprofile WHERE User_ID = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result ? $result['ID'] : null;
}

function getRepairmanProfile($user_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT rp.*, rb.Skills, rb.Education, rb.Certifications, rb.Assessment, rb.Ratings 
        FROM user_repairmanprofile rp 
        LEFT JOIN repairmanagerbackground rb ON rp.RepairmanBG_ID = rb.ID 
        WHERE rp.User_ID = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateRepairmanProfile($user_id, $name, $email, $address, $mobile, $tel) {
    global $conn;
    $stmt = $conn->prepare("UPDATE user_repairmanprofile SET Name=?, Email=?, Address=?, MobileNo=?, TelNo=? WHERE User_ID=?");
    $stmt->bind_param("sssssi", $name, $email, $address, $mobile, $tel, $user_id);
    return $stmt->execute();
}

function updateRepairmanAvailability($user_id, $availability) {
    global $conn;
    $stmt = $conn->prepare("UPDATE user_repairmanprofile SET Availability=? WHERE User_ID=?");
    $stmt->bind_param("si", $availability, $user_id);
    return $stmt->execute();
}

function updateRepairmanSkills($bg_id, $skills) {
    global $conn;
    $stmt = $conn->prepare("UPDATE repairmanagerbackground SET Skills=? WHERE ID=?");
    $stmt->bind_param("si", $skills, $bg_id);
    return $stmt->execute();
}

function updateRepairmanCerts($bg_id, $certs) {
    global $conn;
    $stmt = $conn->prepare("UPDATE repairmanagerbackground SET Certifications=? WHERE ID=?");
    $stmt->bind_param("si", $certs, $bg_id);
    return $stmt->execute();
}

function getRepairmanTickets($repairman_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT t.ID as ticket_id, t.Status as ticket_status, t.Details as ticket_details, 
               cp.Name as client_name, ca.Name as appliance_name, ca.Type as appliance_type, 
               ai.Issue as issue, ai.Details as issue_details,
               rs.Date_Time as schedule_date, rs.Status as schedule_status
        FROM repairticket t 
        LEFT JOIN user_clientprofile cp ON t.Client_ID = cp.ID 
        LEFT JOIN applianceissue ai ON t.ID = ai.Ticket_ID 
        LEFT JOIN clientappliances ca ON ai.ID = ca.Issue_ID
        LEFT JOIN repairschedule rs ON t.Schedule_ID = rs.ID 
        WHERE t.Repairman_ID = ? 
        ORDER BY t.ID DESC
    ");
    $stmt->bind_param("i", $repairman_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function acceptRepairmanTicket($ticket_id, $repairman_id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE repairticket SET Status = 'In Progress', Repairman_ID = ? WHERE ID = ?");
    $stmt->bind_param("ii", $repairman_id, $ticket_id);
    return $stmt->execute();
}

function completeRepairmanTicket($ticket_id, $repairman_id) {
    global $conn;
    startTransaction();
    try {
        $stmt = $conn->prepare("UPDATE repairticket SET Status = 'Completed' WHERE ID = ? AND Repairman_ID = ?");
        $stmt->bind_param("ii", $ticket_id, $repairman_id);
        $stmt->execute();
        
        // Get ticket details for history
        $stmt = $conn->prepare("SELECT Client_ID, Schedule_ID FROM repairticket WHERE ID = ?");
        $stmt->bind_param("i", $ticket_id);
        $stmt->execute();
        $ticket = $stmt->get_result()->fetch_assoc();
        
        if ($ticket) {
            // Get the appliance ID from the ticket's issue
            $stmt = $conn->prepare("SELECT ID FROM clientappliances WHERE Issue_ID = (SELECT ID FROM applianceissue WHERE Ticket_ID = ?)");
            $stmt->bind_param("i", $ticket_id);
            $stmt->execute();
            $appliance = $stmt->get_result()->fetch_assoc();
            $appliance_id = $appliance ? $appliance['ID'] : 0;
            
            insertRecord('repairhistory', [
                'Repairman_ID' => $repairman_id,
                'Schedule_ID' => $ticket['Schedule_ID'],
                'Ticket_ID' => $ticket_id,
                'ClientAppliances_ID' => $appliance_id,
                'Status' => 'Completed'
            ]);
        }
        
        commitTransaction();
        return true;
    } catch (Exception $e) {
        rollbackTransaction();
        throw $e;
    }
}

function getRepairmanSchedule($repairman_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT rs.ID as schedule_id, rs.Date_Time as schedule_date, rs.Status as schedule_status, 
               cp.Name as client_name, t.ID as ticket_id, t.Details as ticket_details,
               ca.Name as appliance_name, ca.Type as appliance_type, 
               ai.Issue as issue, ai.Details as issue_details,
               CONCAT_WS(', ', cladd.Address_Line1, cladd.City_Min, cladd.Province) as client_address
        FROM repairschedule rs 
        LEFT JOIN user_clientprofile cp ON rs.Client_ID = cp.ID 
        LEFT JOIN clientaddress cladd ON cp.ClientAdd_ID = cladd.ID
        LEFT JOIN repairticket t ON rs.ID = t.Schedule_ID 
        LEFT JOIN applianceissue ai ON t.ID = ai.Ticket_ID 
        LEFT JOIN clientappliances ca ON ai.ID = ca.Issue_ID
        WHERE rs.Repairman_ID = ? 
        ORDER BY rs.Date_Time DESC
    ");
    $stmt->bind_param("i", $repairman_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function updateRepairmanScheduleStatus($schedule_id, $repairman_id, $status) {
    global $conn;
    $stmt = $conn->prepare("UPDATE repairschedule SET Status = ? WHERE ID = ? AND Repairman_ID = ?");
    $stmt->bind_param("sii", $status, $schedule_id, $repairman_id);
    return $stmt->execute();
}

function getRepairmanDashboardStats($repairman_id) {
    global $conn;
    $today = date('Y-m-d');
    
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM repairschedule WHERE Repairman_ID = ? AND DATE(Date_Time) = ? AND Status != 'Declined'");
    $stmt->bind_param("is", $repairman_id, $today);
    $stmt->execute();
    $todays_jobs = $stmt->get_result()->fetch_assoc()['count'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM repairticket WHERE Repairman_ID = ? AND Status = 'Open'");
    $stmt->bind_param("i", $repairman_id);
    $stmt->execute();
    $pending_tickets = $stmt->get_result()->fetch_assoc()['count'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM repairticket WHERE Repairman_ID = ? AND Status = 'Completed'");
    $stmt->bind_param("i", $repairman_id);
    $stmt->execute();
    $completed_week = $stmt->get_result()->fetch_assoc()['count'];
    
    // Get rating
    $stmt = $conn->prepare("SELECT rb.Ratings FROM user_repairmanprofile rp JOIN repairmanagerbackground rb ON rp.RepairmanBG_ID = rb.ID WHERE rp.User_ID = ?");
    $stmt->bind_param("i", $repairman_id);
    $stmt->execute();
    $rating_row = $stmt->get_result()->fetch_assoc();
    $avg_rating = $rating_row ? $rating_row['Ratings'] : 0;
    
    // Today's schedule
    $stmt = $conn->prepare("
        SELECT rs.ID as schedule_id, rs.Date_Time as schedule_date, rs.Status as schedule_status, 
               cp.Name as client_name, ca.Name as appliance_name, ai.Issue as issue
        FROM repairschedule rs 
        LEFT JOIN user_clientprofile cp ON rs.Client_ID = cp.User_ID 
        LEFT JOIN repairticket t ON rs.ID = t.Schedule_ID 
        LEFT JOIN clientappliances ca ON t.Client_ID = ca.Client_ID 
        LEFT JOIN applianceissue ai ON t.ID = ai.Ticket_ID 
        WHERE rs.Repairman_ID = ? AND DATE(rs.Date_Time) = ? AND rs.Status != 'Declined' 
        ORDER BY rs.Date_Time ASC LIMIT 3
    ");
    $stmt->bind_param("is", $repairman_id, $today);
    $stmt->execute();
    $schedules = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    return compact('todays_jobs', 'pending_tickets', 'completed_week', 'avg_rating', 'schedules');
}

function getRepairmanHistory($repairman_id, $date_from = null, $date_to = null, $status = null) {
    global $conn;
    $where = "rh.Repairman_ID = ?";
    $types = "i";
    $params = [$repairman_id];
    
    if ($date_from) { $where .= " AND rh.Date >= ?"; $types .= "s"; $params[] = $date_from; }
    if ($date_to) { $where .= " AND rh.Date <= ?"; $types .= "s"; $params[] = $date_to; }
    if ($status) { $where .= " AND rh.Status = ?"; $types .= "s"; $params[] = $status; }
    
    $sql = "SELECT rh.ID as history_id, rh.Date as repair_date, rh.Status as repair_status, 
                   rs.Date_Time as schedule_date, t.ID as ticket_id, t.Details as ticket_details,
                   ca.Name as appliance_name, ca.Type as appliance_type, 
                   ai.Issue as issue, ai.Details as issue_details,
                   cp.Name as client_name, cp.Email as client_email
            FROM repairhistory rh 
            LEFT JOIN repairschedule rs ON rh.Schedule_ID = rs.ID 
            LEFT JOIN repairticket t ON rh.Ticket_ID = t.ID 
            LEFT JOIN clientappliances ca ON rh.ClientAppliances_ID = ca.ID 
            LEFT JOIN applianceissue ai ON t.ID = ai.Ticket_ID 
            LEFT JOIN user_clientprofile cp ON rs.Client_ID = cp.User_ID 
            WHERE $where ORDER BY rh.Date DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// ==========================================
// ADMIN MODEL FUNCTIONS
// ==========================================

function getAdminDashboardStats() {
    global $conn;
    $total_customers = countAllRecords('user_clientprofile', '1');
    $total_repairmen = countAllRecords('user_repairmanprofile', '1');
    $active_tickets = countAllRecords('repairticket', "Status IN ('Open', 'In Progress')");
    $completed_repairs = countAllRecords('repairhistory', '1');
    
    // Top repairmen
    $stmt = $conn->prepare("
        SELECT urp.*, rb.Ratings, 
            (SELECT COUNT(*) FROM repairhistory rh WHERE rh.Repairman_ID = urp.ID) AS CompletedRepairs 
        FROM user_repairmanprofile urp 
        JOIN repairmanagerbackground rb ON urp.RepairmanBG_ID = rb.ID 
        ORDER BY rb.Ratings DESC LIMIT 5
    ");
    $stmt->execute();
    $top_repairmen = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Recent tickets
    $stmt = $conn->prepare("
        SELECT repairticket.*, user_clientprofile.Name AS ClientName 
        FROM repairticket 
        LEFT JOIN user_clientprofile ON repairticket.Client_ID = user_clientprofile.ID 
        ORDER BY repairticket.ID DESC LIMIT 5
    ");
    $stmt->execute();
    $recent_tickets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Recent completions
    $stmt = $conn->prepare("
        SELECT repairhistory.*, user_repairmanprofile.Name AS RepairmanName 
        FROM repairhistory 
        LEFT JOIN user_repairmanprofile ON repairhistory.Repairman_ID = user_repairmanprofile.ID 
        ORDER BY repairhistory.Date DESC LIMIT 5
    ");
    $stmt->execute();
    $recent_completions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    return compact('total_customers', 'total_repairmen', 'active_tickets', 'completed_repairs', 'top_repairmen', 'recent_tickets', 'recent_completions');
}

function getAdminCustomers($search = null) {
    global $conn;
    if ($search) {
        $search = "%$search%";
        $stmt = $conn->prepare("
            SELECT users.*, user_clientprofile.ID AS ProfileID, user_clientprofile.Name AS ProfileName, 
                   user_clientprofile.Email AS ProfileEmail, user_clientprofile.Status AS ProfileStatus,
                   clientaddress.City_Min, clientaddress.Province
            FROM users 
            JOIN user_clientprofile ON users.ID = user_clientprofile.User_ID 
            LEFT JOIN clientaddress ON user_clientprofile.ClientAdd_ID = clientaddress.ID 
            WHERE users.Role = 'customer' AND (user_clientprofile.Name LIKE ? OR user_clientprofile.Email LIKE ?) 
            ORDER BY users.Created_At DESC
        ");
        $stmt->bind_param("ss", $search, $search);
    } else {
        $stmt = $conn->prepare("
            SELECT users.*, user_clientprofile.ID AS ProfileID, user_clientprofile.Name AS ProfileName, 
                   user_clientprofile.Email AS ProfileEmail, user_clientprofile.Status AS ProfileStatus,
                   clientaddress.City_Min, clientaddress.Province
            FROM users 
            JOIN user_clientprofile ON users.ID = user_clientprofile.User_ID 
            LEFT JOIN clientaddress ON user_clientprofile.ClientAdd_ID = clientaddress.ID 
            WHERE users.Role = 'customer' 
            ORDER BY users.Created_At DESC
        ");
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getAdminCustomerById($id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT users.*, user_clientprofile.ID AS ProfileID, user_clientprofile.Name AS ProfileName, 
               user_clientprofile.Email AS ProfileEmail, user_clientprofile.Status AS ProfileStatus,
               clientaddress.Address_Line1, clientaddress.Address_Line2, clientaddress.Brgy, 
               clientaddress.City_Min, clientaddress.Province, clientaddress.Region, clientaddress.Zip_Code
        FROM users 
        JOIN user_clientprofile ON users.ID = user_clientprofile.User_ID 
        LEFT JOIN clientaddress ON user_clientprofile.ClientAdd_ID = clientaddress.ID 
        WHERE user_clientprofile.ID = ? AND users.Role = 'customer'
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateAdminCustomer($id, $name, $email) {
    global $conn;
    $stmt = $conn->prepare("UPDATE user_clientprofile SET Name=?, Email=? WHERE ID=?");
    $stmt->bind_param("ssi", $name, $email, $id);
    return $stmt->execute();
}

function deactivateAdminCustomer($id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE user_clientprofile SET Status='inactive' WHERE ID=?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

function getAdminRepairmen($search = null) {
    global $conn;
    if ($search) {
        $search = "%$search%";
        $stmt = $conn->prepare("
            SELECT urp.*, rb.Skills, rb.Ratings 
            FROM user_repairmanprofile urp 
            JOIN repairmanagerbackground rb ON urp.RepairmanBG_ID = rb.ID 
            WHERE urp.Name LIKE ? OR urp.Email LIKE ? 
            ORDER BY urp.ID DESC
        ");
        $stmt->bind_param("ss", $search, $search);
    } else {
        $stmt = $conn->prepare("
            SELECT urp.*, rb.Skills, rb.Ratings 
            FROM user_repairmanprofile urp 
            JOIN repairmanagerbackground rb ON urp.RepairmanBG_ID = rb.ID 
            ORDER BY urp.ID DESC
        ");
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getAdminRepairmanById($id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT urp.*, rb.Skills, rb.Education, rb.Certifications, rb.Assessment, rb.Ratings 
        FROM user_repairmanprofile urp 
        JOIN repairmanagerbackground rb ON urp.RepairmanBG_ID = rb.ID 
        WHERE urp.ID = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function addAdminRepairman($name, $email, $password, $address, $mobile, $tel) {
    global $conn;
    startTransaction();
    try {
        $user_id = createUser($name, $email, $password, 'repairman');
        $bg_id = insertOrder('repairmanagerbackground', [
            'Skills' => null, 'Education' => null, 'Certifications' => null
        ]);
        $profile_id = insertOrder('user_repairmanprofile', [
            'Name' => $name, 'Email' => $email, 'Address' => $address,
            'MobileNo' => $mobile, 'TelNo' => $tel, 'RepairmanBG_ID' => $bg_id, 'User_ID' => $user_id
        ]);
        commitTransaction();
        return $profile_id;
    } catch (Exception $e) {
        rollbackTransaction();
        throw $e;
    }
}

function updateAdminRepairman($id, $name, $email, $address, $mobile, $tel, $availability) {
    global $conn;
    $stmt = $conn->prepare("UPDATE user_repairmanprofile SET Name=?, Email=?, Address=?, MobileNo=?, TelNo=?, Availability=? WHERE ID=?");
    $stmt->bind_param("ssssssi", $name, $email, $address, $mobile, $tel, $availability, $id);
    return $stmt->execute();
}

function deactivateAdminRepairman($id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE user_repairmanprofile SET Status='inactive' WHERE ID=?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

function deleteAdminRepairman($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT User_ID, RepairmanBG_ID FROM user_repairmanprofile WHERE ID=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $profile = $stmt->get_result()->fetch_assoc();
    
    if (!$profile) return false;
    
    startTransaction();
    try {
        deleteRecord('user_repairmanprofile', "ID = $id");
        deleteRecord('repairmanagerbackground', "ID = {$profile['RepairmanBG_ID']}");
        deleteRecord('users', "ID = {$profile['User_ID']}");
        commitTransaction();
        return true;
    } catch (Exception $e) {
        rollbackTransaction();
        throw $e;
    }
}

function getAdminTickets($status = null) {
    global $conn;
    if ($status) {
        $stmt = $conn->prepare("
            SELECT repairticket.*, user_clientprofile.Name AS ClientName, 
                   user_repairmanprofile.Name AS RepairmanName, repairschedule.Date_Time
            FROM repairticket 
            LEFT JOIN user_clientprofile ON repairticket.Client_ID = user_clientprofile.ID 
            LEFT JOIN user_repairmanprofile ON repairticket.Repairman_ID = user_repairmanprofile.ID 
            LEFT JOIN repairschedule ON repairticket.Schedule_ID = repairschedule.ID 
            WHERE repairticket.Status = ? 
            ORDER BY repairticket.ID DESC
        ");
        $stmt->bind_param("s", $status);
    } else {
        $stmt = $conn->prepare("
            SELECT repairticket.*, user_clientprofile.Name AS ClientName, 
                   user_repairmanprofile.Name AS RepairmanName, repairschedule.Date_Time
            FROM repairticket 
            LEFT JOIN user_clientprofile ON repairticket.Client_ID = user_clientprofile.ID 
            LEFT JOIN user_repairmanprofile ON repairticket.Repairman_ID = user_repairmanprofile.ID 
            LEFT JOIN repairschedule ON repairticket.Schedule_ID = repairschedule.ID 
            ORDER BY repairticket.ID DESC
        ");
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getAdminTicketById($id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT repairticket.*, user_clientprofile.Name AS ClientName, user_clientprofile.Email AS ClientEmail,
               user_repairmanprofile.Name AS RepairmanName, user_repairmanprofile.Email AS RepairmanEmail,
               repairschedule.Date_Time AS ScheduleDate, repairschedule.Status AS ScheduleStatus
        FROM repairticket 
        LEFT JOIN user_clientprofile ON repairticket.Client_ID = user_clientprofile.ID 
        LEFT JOIN user_repairmanprofile ON repairticket.Repairman_ID = user_repairmanprofile.ID 
        LEFT JOIN repairschedule ON repairticket.Schedule_ID = repairschedule.ID 
        WHERE repairticket.ID = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function reassignAdminTicket($ticket_id, $new_repairman_id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE repairticket SET Repairman_ID=? WHERE ID=?");
    $stmt->bind_param("ii", $new_repairman_id, $ticket_id);
    return $stmt->execute();
}

function closeAdminTicket($ticket_id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE repairticket SET Status='Completed' WHERE ID=?");
    $stmt->bind_param("i", $ticket_id);
    return $stmt->execute();
}

// ==========================================
// CHAT MODEL FUNCTIONS
// ==========================================

function getChatContacts($user_id, $user_role) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT 
            CASE WHEN Sender_ID = ? AND Sender_Role = ? THEN Receiver_ID ELSE Sender_ID END AS contact_id,
            CASE WHEN Sender_ID = ? AND Sender_Role = ? THEN Receiver_Role ELSE Sender_Role END AS contact_role,
            MAX(ID) AS last_msg_id,
            MAX(CASE WHEN Receiver_ID = ? AND Receiver_Role = ? AND Is_Read = 0 THEN 1 ELSE 0 END) AS has_unread
        FROM chat_messages 
        WHERE (Sender_ID = ? AND Sender_Role = ?) OR (Receiver_ID = ? AND Receiver_Role = ?) 
        GROUP BY contact_id, contact_role
    ");
    $stmt->bind_param("ssssssssss", $user_id, $user_role, $user_id, $user_role, $user_id, $user_role, $user_id, $user_role, $user_id, $user_role);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getChatContactName($contact_id, $contact_role) {
    global $conn;
    if ($contact_role === 'customer') {
        $stmt = $conn->prepare("SELECT Name FROM user_clientprofile WHERE User_ID = ?");
    } else {
        $stmt = $conn->prepare("SELECT Name FROM user_repairmanprofile WHERE User_ID = ?");
    }
    $stmt->bind_param("i", $contact_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result ? $result['Name'] : 'Unknown';
}

function getChatMessages($user_id, $user_role, $contact_id, $contact_role) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT * FROM chat_messages 
        WHERE (Sender_ID = ? AND Sender_Role = ? AND Receiver_ID = ? AND Receiver_Role = ?) 
           OR (Sender_ID = ? AND Sender_Role = ? AND Receiver_ID = ? AND Receiver_Role = ?) 
        ORDER BY Created_At ASC
    ");
    $stmt->bind_param("isssisss", $user_id, $user_role, $contact_id, $contact_role, $contact_id, $contact_role, $user_id, $user_role);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function markChatMessagesRead($user_id, $user_role, $contact_id, $contact_role) {
    global $conn;
    $stmt = $conn->prepare("UPDATE chat_messages SET Is_Read=1 WHERE Sender_ID=? AND Sender_Role=? AND Receiver_ID=? AND Receiver_Role=? AND Is_Read=0");
    $stmt->bind_param("isss", $contact_id, $contact_role, $user_id, $user_role);
    return $stmt->execute();
}

function sendChatMessage($sender_id, $sender_role, $receiver_id, $receiver_role, $message) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO chat_messages (Sender_ID, Sender_Role, Receiver_ID, Receiver_Role, Message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $sender_id, $sender_role, $receiver_id, $receiver_role, $message);
    $stmt->execute();
    return $conn->insert_id;
}

function getNewChatMessages($user_id, $user_role, $contact_id, $contact_role, $last_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM chat_messages WHERE ID > ? AND Sender_ID = ? AND Sender_Role = ? AND Receiver_ID = ? AND Receiver_Role = ? ORDER BY Created_At ASC");
    $stmt->bind_param("iisss", $last_id, $contact_id, $contact_role, $user_id, $user_role);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getChatMessageById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT Message, Created_At FROM chat_messages WHERE ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
