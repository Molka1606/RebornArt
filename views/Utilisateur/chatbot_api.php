<?php
header("Content-Type: application/json");

// ✅ TA VRAIE CLE API (celle qui commence par sk-)
$api_key = getenv('OPENAI_API_KEY');
// Lire le message depuis le JS
$data = json_decode(file_get_contents("php://input"), true);
$user_message = $data["message"] ?? "";

if (!$user_message) {
    echo json_encode(["reply" => "Message vide."]);
    exit;
}

// ✅ Nouvel endpoint OpenAI
$ch = curl_init("https://api.openai.com/v1/responses");

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer $api_key"
    ],
    CURLOPT_POSTFIELDS => json_encode([
        "model" => "gpt-4.1-mini",
        "input" => [
            [
                "role" => "system",
                "content" => "Tu es RebornBot, l’assistant officiel du site RebornArt. Tu aides avec le recyclage créatif, les idées, les projets, les métiers et l’utilisation du site."
            ],
            [
                "role" => "user",
                "content" => $user_message
            ]
        ]
    ])
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode([
        "reply" => "Erreur serveur : " . curl_error($ch)
    ]);
    curl_close($ch);
    exit;
}

curl_close($ch);

$responseData = json_decode($response, true);

// ✅ EXTRACTION CORRECTE DE LA RÉPONSE (version sûre 2025)
$reply = null;

// Cas 1 : output_text direct
if (isset($responseData["output_text"])) {
    $reply = $responseData["output_text"];
}

// Cas 2 : structure complexe
elseif (isset($responseData["output"][0]["content"][0]["text"])) {
    $reply = $responseData["output"][0]["content"][0]["text"];
}

if (!$reply) {
    echo json_encode([
        "reply" => "Erreur API OpenAI. Impossible de lire la réponse."
    ]);
    exit;
}

echo json_encode(["reply" => $reply]);



