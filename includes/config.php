
<?php




$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "simple_ecommerce";
$conn = "";

// Start Session


try {
    $conn = mysqli_connect(
        $db_server,
        $db_user,
        $db_pass,
        $db_name
    );
} catch (mysqli_sql_exception) {
    echo "no";
}




// Database Connection
try {
    $pdo = new PDO(
        "mysql:host={$db_server};dbname={$db_name};charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Helper Functions
function sanitize_output($data)
{
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// function is_logged_in()
// {
//     return isset($_SESSION['user_id']);
// }
function get_cart()
{
    // return isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
}

function save_cart($cart)
{
    //$_SESSION['cart'] = $cart;
}
function redirect($url)
{
    header("Location: $url");
    exit();
}


?>