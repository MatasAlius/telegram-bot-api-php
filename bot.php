<?php
$path = "https://api.telegram.org/bot<authentication_token>";
error_reporting(0);
ini_set('display_errors', 0);

$update = json_decode(file_get_contents("php://input"), TRUE);

// %0A - new line in text
// strpos - find the position of the first occurrence of a substring in a string.
// strcmp - string comparison, if returns 0 two strings are equal.
// More info about requests: https://core.telegram.org/bots/api#making-requests

if ($update != null) {
    $chatId = $update["message"]["chat"]["id"];
    $message = $update["message"]["text"];
    $firstName = $update["message"]["chat"]["first_name"];
    $userId = $update["message"]["chat"]["id"];
    
    include_once "database.php";
    $language = selectedLanguage($userId, $conn);
    switch (true){
        case strcmp($message,'/start') == 0:
            $rez = startUsing($userId, $firstName, $conn);
            file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=Message text%0ANew line.");
            file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=Select language%0AWrite /seten or /setlt");
           break;
        case strcmp($message,'/setlt') == 0:
            $rez = whichLanguage($userId, 0, $conn);
            file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=Sveiki ".$firstName.".");
            $language = 0;
           break;
        case strcmp($message,'/seten') == 0:
            $rez = whichLanguage($userId, 1, $conn);
            file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=Hello ".$firstName.".");
            $language = 1;
           break;
        case strpos($message, "/help") === 0: 
            $argument = substr($message, 6);    // 6 -> start at the offset'th position
            if (strpos(strtolower($argument), "text") !== false) {  // argument -> text...
                if ($language == 0) {
                    file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=❓Pagalba pagal ".$argument." argumenta.");
                }
                else {
                    file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=❓Help by ".$argument." argument.");
                }
            }
            else {
                file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=❓Help by other ".$argument.".");
            }
            break;
        $conn->close();
    }
}
else {
    header("Location: http://yourwebsite");
    die();
}

function startUsing($userId, $firstName, $conn) {
    $result = $conn->query("REPLACE INTO Users (userid, name) values('$userId', '$firstName')"); 
    $conn->close();
    return $result;
}
function whichLanguage($userId, $whichLanguage, $conn) {
    $result = $conn->query("UPDATE Users SET language='$whichLanguage' WHERE userid='$userId'"); 
    $conn->close();
    return $result;
}
function selectedLanguage($userId, $conn) {
    $result = $conn->query("SELECT language FROM Users WHERE userid='$userId'"); 
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            return $row["language"];
        }
    }
    else {
        return 0;
    }
}
?>