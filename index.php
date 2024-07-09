<?php
$auth_token = "4b60b8594d67d1f8-9429c3f90875b1ed-6a40ac53d68ec5a5";

function put_log_in($data)
{
    global $is_log;
    if ($is_log) {
        file_put_contents("tmp_in.txt", $data . "\n", FILE_APPEND);
    }
}

function put_log_out($data)
{
    global $is_log;
    if ($is_log) {
        file_put_contents("tmp_out.txt", $data . "\n", FILE_APPEND);
    }
}

function sendReq($data)
{
    $request_data = json_encode($data);
    put_log_out($request_data);

    // Використання cURL для відправки даних користувачеві
    $ch = curl_init("https://chatapi.viber.com/pa/send_message");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $request_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    if ($err) {
        return $err;
    } else {
        return $response;
    }
}

function sendMsg($sender_id, $text, $type, $tracking_data = null, $arr_asoc = null)
{
    global $auth_token, $send_name;

    $data['auth_token'] = $auth_token;
    $data['receiver'] = $sender_id;
    if ($text != null) {
        $data['text'] = $text;
    }
    $data['type'] = $type;
    $data['sender']['name'] = $send_name;
    if ($tracking_data != null) {
        $data['tracking_data'] = $tracking_data;
    }
    if ($arr_asoc != null) {
        foreach ($arr_asoc as $key => $val) {
            $data[$key] = $val;
        }
    }

    return sendReq($data);
}

function sendMsgText($sender_id, $text, $tracking_data = null)
{
    return sendMsg($sender_id, $text, "text", $tracking_data);
}

// Отримання вхідних даних від Viber
$request = file_get_contents("php://input");
$input = json_decode($request, true);
put_log_in($request);

// Відповідь "Привіт" на будь-яке отримане повідомлення
if (isset($input['sender']['id'])) {
    sendMsgText($input['sender']['id'], "Привіт!");
}

?>
