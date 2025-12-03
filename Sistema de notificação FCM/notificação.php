<?php

function getAccessToken() {
    $serviceAccount = json_decode(file_get_contents("furmigueironotificacao-89be461a804f.json"), true);

    $header = base64_encode(json_encode([
        "alg" => "RS256",
        "typ" => "JWT"
    ]));

    $iat = time();
    $exp = $iat + 3600;

    $claimSet = base64_encode(json_encode([
        "iss" => $serviceAccount["client_email"],
        "scope" => "https://www.googleapis.com/auth/firebase.messaging",
        "aud" => "https://oauth2.googleapis.com/token",
        "iat" => $iat,
        "exp" => $exp
    ]));

    $signature = "";
    openssl_sign("$header.$claimSet", $signature, $serviceAccount["private_key"], "sha256");
    $signature = base64_encode($signature);

    $jwt = "$header.$claimSet.$signature";

    // Troca JWT por Access Token
    $post = http_build_query([
        "grant_type" => "urn:ietf:params:oauth:grant-type:jwt-bearer",
        "assertion" => $jwt
    ]);

    $ch = curl_init("https://oauth2.googleapis.com/token");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = json_decode(curl_exec($ch), true);

    return $response["access_token"];
}



function sendNotification($targetToken, $title, $body) {
    $accessToken = getAccessToken();
    $projectId = "furmigueironotificacao"; // <<< coloque o ID do seu Firebase

    $url = "https://fcm.googleapis.com/v1/projects/$projectId/messages:send";

    $message = [
        "message" => [
            "token" => $targetToken,
            "notification" => [
                "title" => $title,
                "body" => $body
            ],

            "android" => [
                "priority" => "HIGH",
                "notification" => [
                    "icon" => "ic_launcher",
                    "color" => "#ff00d9ff",
                    "sound" => "default",
                    "image" => "https://i.guim.co.uk/img/static/sys-images/Books/Pix/pictures/2003/11/17/hitler1.jpg?width=465&dpr=1&s=none&crop=none",
                    "channel_id" => "default",
                    "notification_priority" => "PRIORITY_MAX"
                ]]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    echo $result;
}



// ------------- TESTE -----------------
sendNotification(
    "fF2tGTaESoO2wTRUCbtqe_:APA91bHffUd_RHic8xBjpualD90nxIvvJu2mlkhHke0KizU9urbrJQXVGYAVJJ2ezT5LMgJ6-3-pJm-FjKW0Ofr5328PH89LkEL-8TRSnq7elvvOz2_3MEo",
    "A FOLGA DO LINK ESTA PROXIMA",
    "Falta um dia para a folga do link!"
);

