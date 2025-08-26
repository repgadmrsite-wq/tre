<?php
function sendEmail(string $to, string $subject, string $body): bool {
    $headers = 'From: '.(getenv('SMTP_FROM') ?: 'noreply@example.com');
    $success = mail($to, $subject, $body, $headers);
    if (!$success) {
        error_log('Email delivery failed to '.$to);
    }
    return $success;
}

function sendSMS(string $to, string $message): bool {
    $sid = getenv('TWILIO_SID');
    $token = getenv('TWILIO_TOKEN');
    $from = getenv('TWILIO_FROM');
    if (!$sid || !$token || !$from) {
        error_log('SMS config missing');
        return false;
    }
    $data = http_build_query(['From'=>$from,'To'=>$to,'Body'=>$message]);
    $ch = curl_init("https://api.twilio.com/2010-04-01/Accounts/$sid/Messages.json");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_USERPWD, "$sid:$token");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    if(curl_errno($ch)) {
        error_log('SMS error: '.curl_error($ch));
        curl_close($ch);
        return false;
    }
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $status >= 200 && $status < 300;
}
?>
