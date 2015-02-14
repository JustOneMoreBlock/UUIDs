<?php

$user = $_GET["user"];

function api($api)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    $req = json_decode(curl_exec($ch));
    curl_close($ch);
    Return $req;
}

/*
Richard Castera
http://www.richardcastera.com/blog/php-convert-array-to-object-with-stdclass
*/
function arrayToObject($array)
{
    if (!is_array($array)) {
        return $array;
    }
    
    $object = new stdClass();
    if (is_array($array) && count($array) > 0) {
        foreach ($array as $name => $value) {
            $name = strtolower(trim($name));
            if (!empty($name)) {
                $object->$name = arrayToObject($value);
            }
        }
        return $object;
    } else {
        return FALSE;
    }
}

$UUID    = api("https://api.mojang.com/users/profiles/minecraft/" . $user . "");
/*
$UUID->id;
$UUID->name;
*/
$session = api("https://sessionserver.mojang.com/session/minecraft/profile/" . $UUID->id . "");
$p       = arrayToObject($session);
/*
$p->id; //Same as $UUID->id;
$p->name; //Same as $UUID->name;
$p->properties;
*/
foreach ($p->properties as $properties) {
    $value   = base64_decode($properties->value);
    $profile = json_decode($value);
    /*
    $profile->timestamp; //The timestamp of request.
    $profile->profileId; //Same as $UUID->id;
    $profile->profileName; //Same as $UUID->name;
    $profile->textures->SKIN->url;
    $profile->textures->CAPE->url;
    */
}

$names = api("https://api.mojang.com/user/profiles/" . $UUID->id . "/names");
/*
$history->name;
$history->changedToAt;
*/
foreach ($names as $history) {
    $timestamp   = $history->changedToAt / 1000;
    $days_format = "F j, Y, g:i:s A";
    $days_30     = strtotime('+30 days', $timestamp);
    $days_37     = strtotime('+37 days', $timestamp);
    $date        = date($days_format, $timestamp);
    $change      = date($days_format, $days_30);
    $reclaim     = date($days_format, $days_37);
}

?>