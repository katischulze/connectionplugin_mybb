<?php
// Define plugin constants
define("IN_MYBB", 1);
define("THIS_SCRIPT", "connectionplugin.php");
require_once MYBB_ROOT . "global.php";
require_once MYBB_ROOT . "inc/class_parser.php";

// Define an array of user groups that can add connections
$allowed_usergroups = ["Mortals", "Vampires", "Werewolves", "Imbued", "Admin"];

// Check if the user has the required permissions
$has_wob = $mybb->user['uid'] && in_array($mybb->user['usergroup'], $allowed_usergroups);

// Handle POST requests for adding, editing, or deleting connections
if ($has_wob && $mybb->request_method == 'post') {
    if (isset($mybb->input['add_connection'])) {
        // Add connection logic
        add_connection($mybb->input['connection_name']);
    } elseif (isset($mybb->input['edit_connection'])) {
        // Edit connection logic
        edit_connection($mybb->input['connection_id'], $mybb->input['connection_name']);
    } elseif (isset($mybb->input['delete_connection'])) {
        // Delete connection logic
        delete_connection($mybb->input['connection_id']);
    }
}

// Fetch user's connections from the database
$user_connections = get_user_connections($mybb->user['uid']);

// Display connections
$page->output_header("Connections");

// Output the connections
if (!empty($user_connections)) {
    foreach ($user_connections as $connection) {
        echo "Connection: " . htmlspecialchars($connection['name']) . " 
            <form method='post'>
                <input type='hidden' name='connection_id' value='" . intval($connection['id']) . "' />
                <input type='submit' name='edit_connection' value='Edit' />
                <input type='submit' name='delete_connection' value='Delete' />
            </form><br />";
    }
} else {
    echo "You have no connections.";
}

// If the user has permission, show the form to add a new connection
if ($has_wob) {
?>
    <form method='post'>
        <input type='text' name='connection_name' placeholder='Add a new connection' required />
        <input type='submit' name='add_connection' value='Add Connection' />
    </form>
<?php
}

$page->output_footer();

// Function definitions
function add_connection($name) {
    global $db, $mybb;
    $db->insert_query("your_connection_table", [
        "uid" => $mybb->user['uid'],
        "name" => $name
    ]);
}

function edit_connection($id, $name) {
    global $db;
    $db->update_query("your_connection_table", ["name" => $name], "id=" . intval($id));
}

function delete_connection($id) {
    global $db;
    $db->delete_query("your_connection_table", "id=" . intval($id));
}

function get_user_connections($uid) {
    global $db;
    $query = $db->simple_select("your_connection_table", "*", "uid=" . intval($uid));
    return $db->fetch_array($query);
}
