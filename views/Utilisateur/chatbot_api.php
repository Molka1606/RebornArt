<?php
require_once __DIR__ . '/../../model/config.php';

header("Content-Type: application/json");

// 🔑 Clé API depuis .env
$api_key = getenv('OPENAI_API_KEY');
if (!$api_key) {
    echo json_encode(["reply" => "Clé API OpenAI non chargée."]);
    exit;
}

// 📩 Lire le message utilisateur
$data = json_decode(file_get_contents("php://input"), true);
$user_message = $data["message"] ?? "";

if (!$user_message) {
    echo json_encode(["reply" => "Message vide."]);
    exit;
}

// 🌐 Appel API OpenAI (Responses API)
$ch = curl_init("https://api.openai.com/v1/responses");

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer $api_key"
    ],
    CURLOPT_POSTFIELDS => json_encode([
        "model" => "gpt-4o-mini",
        "input" => [
            [
                "role" => "system",
                "content" => [
                    [
                        "type" => "input_text",
                        "text" =>
                        "Tu es RebornBot, l’assistant officiel du site RebornArt.

                        TU DOIS RÉPONDRE UNIQUEMENT aux questions liées à :
                        - RebornArt (le site)
                        - le recyclage créatif
                        - les métiers proposés sur RebornArt
                        - les fonctionnalités du site
                        - les projets, artisans, créations et utilisateurs

                        RÈGLE STRICTE :
                        Si la question n’est PAS liée à RebornArt ou au recyclage créatif,
                        tu dois REFUSER poliment de répondre et dire EXACTEMENT :
                        « Je réponds uniquement aux questions concernant RebornArt et le recyclage créatif. »"
                    ]
                ]
            ],
            [
                "role" => "user",
                "content" => [
                    [
                        "type" => "input_text",
                        "text" => $user_message
                    ]
                ]
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

// ❌ Erreur OpenAI explicite
if (isset($responseData["error"])) {
    echo json_encode([
        "reply" => "Erreur OpenAI : " . $responseData["error"]["message"]
    ]);
    exit;
}

// ✅ Extraction correcte de la réponse
$reply = $responseData["output"][0]["content"][0]["text"] ?? null;

if (!$reply) {
    echo json_encode([
        "reply" => "Erreur API OpenAI : réponse vide ou format inattendu."
    ]);
    exit;
}

// 📤 Réponse finale
echo json_encode(["reply" => $reply]);
