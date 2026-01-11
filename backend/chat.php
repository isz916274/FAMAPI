<?php
header('Content-Type: application/json');

// Leer entrada
$input = json_decode(file_get_contents("php://input"), true);
$userMessage = trim($input['message'] ?? '');

// Seguridad básica
if ($userMessage === '') {
  echo json_encode(["reply" => "¿En qué puedo ayudarte?"]);
  exit;
}

// Contexto FAMAPI
$context = file_get_contents('famapi_context.txt');

// API Key desde variable de entorno
$apiKey = "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";


$data = [
  "model" => "gpt-4.1-mini",
  "messages" => [
    ["role" => "system", "content" => $context],
    ["role" => "user", "content" => $userMessage]
  ],
  "temperature" => 0.3
];

$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_HTTPHEADER => [
    "Content-Type: application/json",
    "Authorization: Bearer $apiKey"
  ],
  CURLOPT_POSTFIELDS => json_encode($data)
]);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
$reply = $result['choices'][0]['message']['content']
  ?? "Por favor contáctanos para más información.";

echo json_encode(["reply" => $reply]);
