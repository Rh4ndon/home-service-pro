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

function createUser($name, $email, $password, $role, $status = 'active') {
    global $conn;
    $hashed = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO users (Name, Email, Password, Role, Status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $hashed, $role, $status);
    $stmt->execute();
    return $conn->insert_id;
}

function setRepairmanActivation($profile_id, $active) {
    global $conn;
    $status = $active ? 'active' : 'inactive';
    $stmt = $conn->prepare("
        UPDATE user_repairmanprofile urp 
        JOIN users u ON u.ID = urp.User_ID 
        SET urp.Status = ?, u.Status = ? 
        WHERE urp.ID = ?
    ");
    $stmt->bind_param("ssi", $status, $status, $profile_id);
    return $stmt->execute();
}

function createRepairmanWithCerts($name, $email, $password, $mobile, $certs_json, $active = false) {
    global $conn;
    startTransaction();
    try {
        $user_status = $active ? 'active' : 'inactive';
        $user_id = createUser($name, $email, $password, 'repairman', $user_status);
        $profile_id = insertOrder('user_repairmanprofile', [
            'Name' => $name, 'Email' => $email, 'MobileNo' => $mobile,
            'User_ID' => $user_id, 'Status' => $user_status,
            'Certifications' => $certs_json ? $certs_json : null
        ]);
        commitTransaction();
        return $profile_id;
    } catch (Exception $e) {
        rollbackTransaction();
        throw $e;
    }
}

function certUploadDir() {
    $dir = __DIR__ . '/../uploads/certificates';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    return $dir;
}

function saveUploadedCertFile($tmp_name, $orig_name) {
    $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
    $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($ext, $allowed)) return null;
    $safe = uniqid('cert_', true) . '.' . $ext;
    $dest = certUploadDir() . '/' . $safe;
    if (move_uploaded_file($tmp_name, $dest)) return 'uploads/certificates/' . $safe;
    return null;
}

function collectCertEntriesFromUploads() {
    $entries = [];
    if (empty($_FILES['cert_files']) || !is_array($_FILES['cert_files']['name'])) return $entries;
    foreach ($_FILES['cert_files']['name'] as $i => $orig_name) {
        $cert_name = trim($_POST['cert_names'][$i] ?? '');
        $err = $_FILES['cert_files']['error'][$i] ?? UPLOAD_ERR_NO_FILE;
        if ($err === UPLOAD_ERR_NO_FILE) {
            if (!$cert_name && !empty($orig_name)) $cert_name = preg_replace('/\.[^.]+$/', '', $orig_name);
            if (!$cert_name) continue;
            $entries[] = ['name' => $cert_name, 'file' => null];
            continue;
        }
        if ($err !== UPLOAD_ERR_OK) continue;
        if (!$cert_name && !empty($orig_name)) $cert_name = preg_replace('/\.[^.]+$/', '', $orig_name);
        $path = saveUploadedCertFile($_FILES['cert_files']['tmp_name'][$i], $orig_name);
        $entries[] = ['name' => $cert_name ?: 'Certificate', 'file' => $path];
    }
    return $entries;
}

function normalizePhilippineMobile($mobile) {
    $digits = preg_replace('/[^0-9]/', '', $mobile);
    if (preg_match('/^639\d{9}$/', $digits)) $digits = '0' . substr($digits, 2);
    return $digits;
}

function isPhilippineMobile($mobile) {
    $digits = preg_replace('/[^0-9]/', '', $mobile);
    return (bool) preg_match('/^(09\d{9}|639\d{9})$/', $digits);
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


function updateCustomerProfile($profile_id, $name, $email) {
    global $conn;
    $stmt = $conn->prepare("UPDATE user_clientprofile SET Name = ?, Email = ? WHERE ID = ?");
    $stmt->bind_param("ssi", $name, $email, $profile_id);
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
    $stmt->bind_param("sssssii", $type, $name, $make, $year, $details, $id, $client_id);
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
        SELECT repairticket.ID as ticket_id, repairticket.Status as ticket_status, repairticket.Details as ticket_details,
               repairticket.Repairman_ID as repairman_id,
               repairticket.Client_Lat, repairticket.Client_Lng, repairticket.Route_Distance_Km, repairticket.Route_Duration_Min,
               cp.Name as client_name, cp.Latitude as client_lat, cp.Longitude as client_lng,
               cp.Formatted_Address as client_formatted_address,
               cp.Address_Line1, cp.Address_Line2, cp.Brgy, cp.City_Min, cp.Province,
               ca.Name as appliance_name, ca.Type as appliance_type, ca.Make as appliance_make, ca.Year as appliance_year,
               ai.Issue as issue, ai.Details as issue_details, ai.Detailed_Report as detailed_report,
               rs.Date_Time as schedule_date, rs.Status as schedule_status,
               urp.Name as repairman_name,
               urp.User_ID as repairman_user_id
        FROM repairticket
        LEFT JOIN user_clientprofile cp ON repairticket.Client_ID = cp.ID
        LEFT JOIN repairschedule rs ON repairticket.Schedule_ID = rs.ID
        LEFT JOIN user_repairmanprofile urp ON repairticket.Repairman_ID = urp.ID
        LEFT JOIN applianceissue ai ON repairticket.ID = ai.Ticket_ID
        LEFT JOIN clientappliances ca ON ca.ID = COALESCE(repairticket.Appliance_ID, (SELECT ca2.ID FROM clientappliances ca2 WHERE ca2.Issue_ID = ai.ID LIMIT 1))
        WHERE repairticket.Client_ID = ?
        ORDER BY repairticket.ID DESC
    ");
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function saveBookingMedia($ticket_id, $media_type, $file_path, $file_name) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO booking_media (Ticket_ID, Media_Type, File_Path, File_Name) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $ticket_id, $media_type, $file_path, $file_name);
    return $stmt->execute();
}

function getBookingMedia($ticket_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT ID, Media_Type, File_Path, File_Name, Created_At FROM booking_media WHERE Ticket_ID = ? ORDER BY Media_Type, ID");
    $stmt->bind_param("i", $ticket_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function createCustomerTicket($client_id, $appliance_type, $appliance_name, $make_brand, $year, $issue_desc, $date_time, $appliance_id = null, $repairman_id = null, $client_lat = null, $client_lng = null, $route_distance_km = null, $route_duration_min = null, $detailed_report = null) {
    global $conn;
    startTransaction();
    try {
        // Confirm the chosen repairman is still valid before assigning
        if ($repairman_id && !isRepairmanAssignable($repairman_id)) {
            throw new Exception('The selected repairman is no longer available.');
        }

        // 1. Insert ticket first (no schedule yet)
        $ticket_id = insertOrder('repairticket', [
            'Client_ID' => $client_id,
            'Status' => 'Open',
            'Details' => $issue_desc,
            'Repairman_ID' => $repairman_id,
            'Client_Lat' => $client_lat,
            'Client_Lng' => $client_lng,
            'Route_Distance_Km' => $route_distance_km,
            'Route_Duration_Min' => $route_duration_min
        ]);
        
        // 2. Insert issue linked to ticket
        $issue_id = insertOrder('applianceissue', [
            'Issue' => $issue_desc, 'Details' => $issue_desc, 'Ticket_ID' => $ticket_id,
            'Detailed_Report' => $detailed_report
        ]);
        
        // 3. Link to an existing appliance, or create a new one
        $linked_appliance_id = null;
        if ($appliance_id) {
            $linked = linkApplianceToIssue($appliance_id, $client_id, $issue_id, $issue_desc);
            if (!$linked) throw new Exception('Selected appliance not found.');
            $linked_appliance_id = $appliance_id;
        } else {
            $linked_appliance_id = insertOrder('clientappliances', [
                'Client_ID' => $client_id, 'Type' => $appliance_type, 'Name' => $appliance_name,
                'Make' => $make_brand, 'Year' => $year, 'Details' => $issue_desc, 'Issue_ID' => $issue_id
            ]);
        }

        // Ensure the ticket records exactly which appliance it is about
        editRecord('repairticket', ['Appliance_ID' => $linked_appliance_id], "ID = $ticket_id");
        
        // 4. Insert schedule
        $schedule_id = insertOrder('repairschedule', [
            'Client_ID' => $client_id, 'Date_Time' => $date_time, 'Status' => 'Scheduled',
            'Repairman_ID' => $repairman_id
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

function linkApplianceToIssue($appliance_id, $client_id, $issue_id, $issue_desc) {
    global $conn;
    $stmt = $conn->prepare("UPDATE clientappliances SET Issue_ID = ?, Details = ? WHERE ID = ? AND Client_ID = ?");
    $stmt->bind_param("isii", $issue_id, $issue_desc, $appliance_id, $client_id);
    $ok = $stmt->execute();
    return $ok && $stmt->affected_rows > 0;
}

function cancelCustomerTicket($ticket_id, $client_id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE repairticket SET Status='Cancelled' WHERE ID=? AND Client_ID=? AND Status NOT IN ('Completed','Cancelled')");
    $stmt->bind_param("ii", $ticket_id, $client_id);
    return $stmt->execute();
}

function completeCustomerTicket($ticket_id, $client_id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE repairticket SET Status='Completed' WHERE ID=? AND Client_ID=? AND Status NOT IN ('Completed','Cancelled')");
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
        SELECT rp.*, u.Status AS UserStatus
        FROM user_repairmanprofile rp 
        LEFT JOIN users u ON u.ID = rp.User_ID
        WHERE rp.User_ID = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateRepairmanProfile($user_id, $name, $email, $address, $mobile, $facebook_page) {
    global $conn;
    $stmt = $conn->prepare("UPDATE user_repairmanprofile SET Name=?, Email=?, Address=?, MobileNo=?, FacebookPage=? WHERE User_ID=?");
    $stmt->bind_param("sssssi", $name, $email, $address, $mobile, $facebook_page, $user_id);
    return $stmt->execute();
}

function updateRepairmanAvailability($user_id, $availability) {
    global $conn;
    $stmt = $conn->prepare("UPDATE user_repairmanprofile SET Availability=? WHERE User_ID=?");
    $stmt->bind_param("si", $availability, $user_id);
    return $stmt->execute();
}

function updateRepairmanSkills($profile_id, $skills) {
    global $conn;
    $stmt = $conn->prepare("UPDATE user_repairmanprofile SET Skills=? WHERE ID=?");
    $stmt->bind_param("si", $skills, $profile_id);
    return $stmt->execute();
}

function updateRepairmanEducation($profile_id, $education) {
    global $conn;
    $stmt = $conn->prepare("UPDATE user_repairmanprofile SET Education=? WHERE ID=?");
    $stmt->bind_param("si", $education, $profile_id);
    return $stmt->execute();
}

function updateRepairmanCerts($profile_id, $certs) {
    global $conn;
    $stmt = $conn->prepare("UPDATE user_repairmanprofile SET Certifications=? WHERE ID=?");
    $stmt->bind_param("si", $certs, $profile_id);
    return $stmt->execute();
}

function getRepairmanTickets($repairman_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT t.ID as ticket_id, t.Status as ticket_status, t.Details as ticket_details, 
               t.Client_ID as client_id,
               t.Client_Lat, t.Client_Lng, t.Route_Distance_Km, t.Route_Duration_Min,
                cp.Name as client_name, cp.Latitude as client_lat, cp.Longitude as client_lng,
                cp.Formatted_Address as client_formatted_address,
                cp.Address_Line1, cp.Address_Line2, cp.Brgy, cp.City_Min, cp.Province,
                ca.Name as appliance_name, ca.Type as appliance_type, ca.Make as appliance_make, ca.Year as appliance_year,
                ai.Issue as issue, ai.Details as issue_details, ai.Detailed_Report as detailed_report,
                 rs.Date_Time as schedule_date, rs.Status as schedule_status
        FROM repairticket t 
        LEFT JOIN user_clientprofile cp ON t.Client_ID = cp.ID 
        LEFT JOIN applianceissue ai ON t.ID = ai.Ticket_ID 
        LEFT JOIN clientappliances ca ON ca.ID = COALESCE(t.Appliance_ID, (SELECT ca2.ID FROM clientappliances ca2 WHERE ca2.Issue_ID = ai.ID LIMIT 1))
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
        $stmt = $conn->prepare("SELECT Client_ID, Schedule_ID, Appliance_ID FROM repairticket WHERE ID = ?");
        $stmt->bind_param("i", $ticket_id);
        $stmt->execute();
        $ticket = $stmt->get_result()->fetch_assoc();
        
        if ($ticket) {
            // Get the appliance ID for this ticket (recorded on the ticket itself)
            $appliance_id = $ticket['Appliance_ID'] ? $ticket['Appliance_ID'] : 0;
            if (!$appliance_id) {
                $stmt = $conn->prepare("SELECT ID FROM clientappliances WHERE Issue_ID = (SELECT ID FROM applianceissue WHERE Ticket_ID = ?)");
                $stmt->bind_param("i", $ticket_id);
                $stmt->execute();
                $appliance = $stmt->get_result()->fetch_assoc();
                $appliance_id = $appliance ? $appliance['ID'] : 0;
            }
            
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
               rs.Client_ID as client_id,
               cu.ID as client_user_id,
               cp.Name as client_name, t.ID as ticket_id, t.Details as ticket_details,
                ca.Name as appliance_name, ca.Type as appliance_type, 
                ai.Issue as issue, ai.Details as issue_details,
                CONCAT_WS(', ', cp.Address_Line1, cp.City_Min, cp.Province) as client_address
         FROM repairschedule rs 
         LEFT JOIN user_clientprofile cp ON rs.Client_ID = cp.ID 
         LEFT JOIN users cu ON cu.ID = cp.User_ID
         LEFT JOIN repairticket t ON rs.ID = t.Schedule_ID
        LEFT JOIN applianceissue ai ON t.ID = ai.Ticket_ID 
        LEFT JOIN clientappliances ca ON ca.ID = COALESCE(t.Appliance_ID, (SELECT ca2.ID FROM clientappliances ca2 WHERE ca2.Issue_ID = ai.ID LIMIT 1))
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
    $stmt = $conn->prepare("SELECT Ratings FROM user_repairmanprofile WHERE User_ID = ?");
    $stmt->bind_param("i", $repairman_id);
    $stmt->execute();
    $rating_row = $stmt->get_result()->fetch_assoc();
    $avg_rating = $rating_row ? $rating_row['Ratings'] : 0;
    
    // Today's schedule
    $stmt = $conn->prepare("
        SELECT rs.ID as schedule_id, rs.Date_Time as schedule_date, rs.Status as schedule_status, 
               cp.Name as client_name, ca.Name as appliance_name, ai.Issue as issue
        FROM repairschedule rs 
        LEFT JOIN user_clientprofile cp ON rs.Client_ID = cp.ID 
        LEFT JOIN repairticket t ON rs.ID = t.Schedule_ID 
        LEFT JOIN applianceissue ai ON t.ID = ai.Ticket_ID 
        LEFT JOIN clientappliances ca ON ca.ID = COALESCE(t.Appliance_ID, (SELECT ca2.ID FROM clientappliances ca2 WHERE ca2.Issue_ID = ai.ID LIMIT 1))
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
               ai.Issue as issue, ai.Details as issue_details, ai.Detailed_Report as detailed_report,
                   cp.Name as client_name, cp.Email as client_email
            FROM repairhistory rh 
            LEFT JOIN repairschedule rs ON rh.Schedule_ID = rs.ID 
            LEFT JOIN repairticket t ON rh.Ticket_ID = t.ID 
            LEFT JOIN clientappliances ca ON rh.ClientAppliances_ID = ca.ID 
            LEFT JOIN applianceissue ai ON t.ID = ai.Ticket_ID 
            LEFT JOIN user_clientprofile cp ON rs.Client_ID = cp.ID 
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

    // Pending approvals (inactive repairmen awaiting activation)
    $pending_approvals = countAllRecords('user_repairmanprofile', "Status = 'inactive'");

    // Flagged issues: open tickets with no repairman assigned
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM repairticket WHERE Status IN ('Open', 'In Progress') AND Repairman_ID IS NULL");
    $stmt->execute();
    $flagged_issues = $stmt->get_result()->fetch_assoc()['total'];

    // Schedules today
    $today = date('Y-m-d');
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM repairschedule WHERE DATE(Date_Time) = ? AND Status != 'Declined'");
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $schedules_today = $stmt->get_result()->fetch_assoc()['total'];

    // Reports this week (completed repairs this week)
    $week_start = date('Y-m-d', strtotime('monday this week'));
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM repairhistory WHERE DATE(Date) >= ?");
    $stmt->bind_param("s", $week_start);
    $stmt->execute();
    $reports_this_week = $stmt->get_result()->fetch_assoc()['total'];
    
    // Top repairmen
    $stmt = $conn->prepare("
        SELECT urp.*, 
            (SELECT COUNT(*) FROM repairhistory rh WHERE rh.Repairman_ID = urp.ID) AS CompletedRepairs 
        FROM user_repairmanprofile urp 
        ORDER BY urp.Ratings DESC LIMIT 5
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
    
    return compact('total_customers', 'total_repairmen', 'active_tickets', 'completed_repairs', 'pending_approvals', 'flagged_issues', 'schedules_today', 'reports_this_week', 'top_repairmen', 'recent_tickets', 'recent_completions');
}

function getAdminCustomers($search = null) {
    global $conn;
    if ($search) {
        $search = "%$search%";
        $stmt = $conn->prepare("
            SELECT users.*, user_clientprofile.ID AS ProfileID, user_clientprofile.Name AS ProfileName, 
                   user_clientprofile.Email AS ProfileEmail, user_clientprofile.Status AS ProfileStatus,
                   user_clientprofile.City_Min, user_clientprofile.Province
            FROM users 
            JOIN user_clientprofile ON users.ID = user_clientprofile.User_ID 
            WHERE users.Role = 'customer' AND (user_clientprofile.Name LIKE ? OR user_clientprofile.Email LIKE ?) 
            ORDER BY users.Created_At DESC
        ");
        $stmt->bind_param("ss", $search, $search);
    } else {
        $stmt = $conn->prepare("
            SELECT users.*, user_clientprofile.ID AS ProfileID, user_clientprofile.Name AS ProfileName, 
                   user_clientprofile.Email AS ProfileEmail, user_clientprofile.Status AS ProfileStatus,
                   user_clientprofile.City_Min, user_clientprofile.Province
            FROM users 
            JOIN user_clientprofile ON users.ID = user_clientprofile.User_ID 
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
               user_clientprofile.Address_Line1, user_clientprofile.Address_Line2, user_clientprofile.Brgy, 
               user_clientprofile.City_Min, user_clientprofile.Province, user_clientprofile.Region, user_clientprofile.Zip_Code
        FROM users 
        JOIN user_clientprofile ON users.ID = user_clientprofile.User_ID 
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
    $stmt->execute();

    $stmt2 = $conn->prepare("UPDATE users u JOIN user_clientprofile up ON u.ID = up.User_ID SET u.Status='inactive' WHERE up.ID=?");
    $stmt2->bind_param("i", $id);
    return $stmt2->execute();
}

function activateAdminCustomer($id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE user_clientprofile SET Status='active' WHERE ID=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $stmt2 = $conn->prepare("UPDATE users u JOIN user_clientprofile up ON u.ID = up.User_ID SET u.Status='active' WHERE up.ID=?");
    $stmt2->bind_param("i", $id);
    return $stmt2->execute();
}

function getAdminRepairmen($search = null) {
    global $conn;
    if ($search) {
        $search = "%$search%";
        $stmt = $conn->prepare("
            SELECT urp.* 
            FROM user_repairmanprofile urp 
            WHERE urp.Name LIKE ? OR urp.Email LIKE ? 
            ORDER BY urp.ID DESC
        ");
        $stmt->bind_param("ss", $search, $search);
    } else {
        $stmt = $conn->prepare("
            SELECT urp.* 
            FROM user_repairmanprofile urp 
            ORDER BY urp.ID DESC
        ");
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getAdminRepairmanById($id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT urp.* 
        FROM user_repairmanprofile urp 
        WHERE urp.ID = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function addAdminRepairman($name, $email, $password, $address, $mobile, $certs_json = null, $skills = null, $education = null, $facebook_page = null) {
    global $conn;
    startTransaction();
    try {
        $user_id = createUser($name, $email, $password, 'repairman');
        $profile_id = insertOrder('user_repairmanprofile', [
            'Name' => $name, 'Email' => $email, 'Address' => $address,
            'MobileNo' => $mobile, 'User_ID' => $user_id,
            'Skills' => $skills, 'Education' => $education,
            'Certifications' => $certs_json ? $certs_json : null,
            'FacebookPage' => $facebook_page
        ]);
        commitTransaction();
        return $profile_id;
    } catch (Exception $e) {
        rollbackTransaction();
        throw $e;
    }
}

function activateAdminRepairman($id) {
    return setRepairmanActivation($id, true);
}

function updateAdminRepairman($id, $name, $email, $address, $mobile, $availability, $education = null, $facebook_page = null) {
    global $conn;
    $stmt = $conn->prepare("UPDATE user_repairmanprofile SET Name=?, Email=?, Address=?, MobileNo=?, Availability=?, Education=?, FacebookPage=? WHERE ID=?");
    $stmt->bind_param("sssssssi", $name, $email, $address, $mobile, $availability, $education, $facebook_page, $id);
    return $stmt->execute();
}

function deactivateAdminRepairman($id) {
    return setRepairmanActivation($id, false);
}

function deleteAdminRepairman($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT User_ID FROM user_repairmanprofile WHERE ID=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $profile = $stmt->get_result()->fetch_assoc();
    
    if (!$profile) return false;
    
    startTransaction();
    try {
        deleteRecord('user_repairmanprofile', "ID = $id");
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
    $with = "repairticket.*, user_clientprofile.Name AS ClientName, 
                   user_repairmanprofile.Name AS RepairmanName, repairschedule.Date_Time,
                   clientappliances.Name AS ApplianceName, clientappliances.Type AS ApplianceType";
    $from = " FROM repairticket 
            LEFT JOIN user_clientprofile ON repairticket.Client_ID = user_clientprofile.ID 
            LEFT JOIN user_repairmanprofile ON repairticket.Repairman_ID = user_repairmanprofile.ID 
            LEFT JOIN repairschedule ON repairticket.Schedule_ID = repairschedule.ID 
            LEFT JOIN applianceissue ON applianceissue.Ticket_ID = repairticket.ID
            LEFT JOIN clientappliances ON clientappliances.ID = COALESCE(repairticket.Appliance_ID, (SELECT ca2.ID FROM clientappliances ca2 WHERE ca2.Issue_ID = applianceissue.ID LIMIT 1))";
    if ($status) {
        $stmt = $conn->prepare("SELECT $with $from WHERE repairticket.Status = ? ORDER BY repairticket.ID DESC");
        $stmt->bind_param("s", $status);
    } else {
        $stmt = $conn->prepare("SELECT $with $from ORDER BY repairticket.ID DESC");
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

// ==========================================
// LOCATION & MATCHING MODEL FUNCTIONS
// ==========================================

function getGoogleMapsApiKey() {
    $env_file = __DIR__ . '/../controllers/.env';
    $key = '';
    if (file_exists($env_file)) {
        $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (!$line || strpos($line, '#') === 0) continue;
            if (strpos($line, 'GOOGLE_MAP_API_KEY=') === 0) {
                $key = trim(substr($line, strlen('GOOGLE_MAP_API_KEY=')));
                break;
            }
        }
    }
    return $key ?: getenv('GOOGLE_MAP_API_KEY');
}

function haversineDistanceKm($lat1, $lng1, $lat2, $lng2) {
    if ($lat1 === null || $lng1 === null || $lat2 === null || $lng2 === null) return null;
    $earth_radius_km = 6371.0;
    $dLat = deg2rad((float) $lat2 - (float) $lat1);
    $dLng = deg2rad((float) $lng2 - (float) $lng1);
    $a = sin($dLat / 2) * sin($dLat / 2) +
         cos(deg2rad((float) $lat1)) * cos(deg2rad((float) $lat2)) *
         sin($dLng / 2) * sin($dLng / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $earth_radius_km * $c;
}

function getAvailableRepairmenWithLocation($skill = null, $limit = 50) {
    global $conn;
    $sql = "
        SELECT urp.ID, urp.User_ID, urp.Name, urp.Email, urp.Latitude, urp.Longitude,
               urp.Formatted_Address, urp.MobileNo, urp.Availability, urp.Skills,
               urp.Education, urp.Certifications, urp.Ratings, urp.Reviews, urp.Details,
               u.Status AS AccountStatus,
               (SELECT COUNT(*) FROM repairhistory rh WHERE rh.Repairman_ID = urp.ID) AS CompletedRepairs
        FROM user_repairmanprofile urp
        JOIN users u ON u.ID = urp.User_ID
        WHERE u.Status = 'active'
          AND urp.Status = 'active'
          AND urp.Latitude IS NOT NULL
          AND urp.Longitude IS NOT NULL
          AND (urp.Availability IS NULL OR urp.Availability = '' OR LOWER(urp.Availability) IN ('1','available','yes'))
    ";
    $types = '';
    $params = [];
    if ($skill) {
        $sql .= " AND FIND_IN_SET(?, urp.Skills)";
        $types .= 's';
        $params[] = $skill;
    }
    $sql .= " ORDER BY urp.Ratings DESC LIMIT ?";
    $types .= 'i';
    $params[] = $limit;

    $stmt = $conn->prepare($sql);
    if ($types) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function isRepairmanAssignable($repairman_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT urp.ID FROM user_repairmanprofile urp
        JOIN users u ON u.ID = urp.User_ID
        WHERE urp.ID = ? AND u.Status = 'active' AND urp.Status = 'active'
          AND urp.Latitude IS NOT NULL AND urp.Longitude IS NOT NULL
          AND (urp.Availability IS NULL OR urp.Availability = '' OR LOWER(urp.Availability) IN ('1','available','yes'))
    ");
    $stmt->bind_param("i", $repairman_id);
    $stmt->execute();
    return (bool) $stmt->get_result()->fetch_assoc();
}

function routeDistancesFromDistanceMatrix($origin_lat, $origin_lng, array $destinations, $key) {
    // Origins = repairmen, destination(s) = client (one destination).
    // Returns keyed by repairman ID: ['km' => float, 'min' => int].
    if (!$key || !$destinations || $origin_lat === null || $origin_lng === null) return [];

    $origins = [];
    foreach ($destinations as $r) {
        if ($r['Latitude'] !== null && $r['Longitude'] !== null) {
            $origins[$r['ID']] = number_format((float) $r['Latitude'], 6, '.', '') . ',' . number_format((float) $r['Longitude'], 6, '.', '');
        }
    }
    if (!$origins) return [];

    $origins_str = implode('|', array_values($origins));
    $dest_str = number_format((float) $origin_lat, 6, '.', '') . ',' . number_format((float) $origin_lng, 6, '.', '');

    $url = 'https://maps.googleapis.com/maps/api/distancematrix/json'
        . '?origins=' . urlencode($origins_str)
        . '&destinations=' . urlencode($dest_str)
        . '&mode=driving'
        . '&units=metric'
        . '&key=' . urlencode($key);

    $ctx = stream_context_create(['http' => ['timeout' => 15]]);
    $resp = @file_get_contents($url, false, $ctx);
    if ($resp === false) return [];
    $data = json_decode($resp, true);
    if (empty($data['status']) || $data['status'] !== 'OK') return [];

    $result = [];
    $id_list = array_keys($origins);
    foreach (($data['rows'] ?? []) as $i => $row) {
        if (!isset($id_list[$i])) continue;
        $id = $id_list[$i];
        $elem = $row['elements'][0] ?? null;
        if (!$elem || ($elem['status'] ?? '') !== 'OK') {
            $result[$id] = null; // flag as no route found
            continue;
        }
        $km = isset($elem['distance']['value']) ? round($elem['distance']['value'] / 1000.0, 2) : null;
        $min = isset($elem['duration']['value']) ? (int) round($elem['duration']['value'] / 60.0) : null;
        $result[$id] = ['km' => $km, 'min' => $min];
    }
    return $result;
}

function findNearbyRepairmen($client_lat, $client_lng, $radius_km = 10, $skill = null) {
    if ($client_lat === null || $client_lng === null) {
        return ['repairmen' => [], 'method' => 'none', 'error' => 'Client location is required.'];
    }
    $repairmen = getAvailableRepairmenWithLocation($skill);
    if (!$repairmen) return ['repairmen' => [], 'method' => 'none'];

    $key = getGoogleMapsApiKey();
    $routes = routeDistancesFromDistanceMatrix($client_lat, $client_lng, $repairmen, $key);
    $method = $routes ? 'route' : 'haversine';

    $result = [];
    foreach ($repairmen as $r) {
        $rid = $r['ID'];
        $route = $routes[$rid] ?? null;
        if ($route === null && $routes) {
            // Distance Matrix returned no drivable route for this repairman -> straight-line estimate
            $distance = haversineDistanceKm($client_lat, $client_lng, $r['Latitude'], $r['Longitude']);
            $r['distance_km'] = $distance !== null ? round($distance, 2) : null;
            $r['duration_min'] = $distance !== null ? (int) round(($distance / 25.0) * 60.0) : null;
            $r['route_used'] = false;
        } elseif ($route) {
            $r['distance_km'] = $route['km'];
            $r['duration_min'] = $route['min'];
            $r['route_used'] = true;
        } else {
            // No Google response at all -> Haversine fallback
            $distance = haversineDistanceKm($client_lat, $client_lng, $r['Latitude'], $r['Longitude']);
            $r['distance_km'] = $distance !== null ? round($distance, 2) : null;
            $r['duration_min'] = $distance !== null ? (int) round(($distance / 40.0) * 60.0) : null;
            $r['route_used'] = false;
        }

        if ($r['distance_km'] !== null && $r['distance_km'] <= (float) $radius_km) {
            $result[] = $r;
        }
    }

    usort($result, function ($a, $b) {
        return ($a['distance_km'] ?? PHP_FLOAT_MAX) <=> ($b['distance_km'] ?? PHP_FLOAT_MAX);
    });

    return ['repairmen' => $result, 'method' => $method, 'radius_km' => (float) $radius_km];
}

function updateRepairmanLocation($user_id, $lat, $lng, $address) {
    global $conn;
    $lat = ($lat === '' || $lat === null) ? null : $lat;
    $lng = ($lng === '' || $lng === null) ? null : $lng;
    $address = ($address === '' || $address === null) ? null : $address;
    $stmt = $conn->prepare("UPDATE user_repairmanprofile SET Latitude=?, Longitude=?, Formatted_Address=? WHERE User_ID=?");
    $stmt->bind_param("ddss", $lat, $lng, $address, $user_id);
    return $stmt->execute();
}

function updateCustomerLocation($profile_id, $lat, $lng, $address) {
    global $conn;
    $lat = ($lat === '' || $lat === null) ? null : $lat;
    $lng = ($lng === '' || $lng === null) ? null : $lng;
    $address = ($address === '' || $address === null) ? null : $address;
    $stmt = $conn->prepare("UPDATE user_clientprofile SET Latitude=?, Longitude=?, Formatted_Address=? WHERE ID=?");
    $stmt->bind_param("ddsi", $lat, $lng, $address, $profile_id);
    return $stmt->execute();
}
