<?php
header('Content-Type: application/json; charset=utf-8');

// Leer entrada
$input = json_decode(file_get_contents("php://input"), true);
$userMessage = trim($input['message'] ?? '');

if ($userMessage === '') {
  echo json_encode(["reply" => "¿En qué puedo ayudarte?"]);
  exit;
}

// Contexto
$context = file_get_contents(__DIR__ . '/famapi_context.txt');

// API KEY DESDE VARIABLE DE ENTORNO
$apiKey = getenv('OPENAI_API_KEY');

if (!$apiKey) {
  echo json_encode([
    "reply" => "El asistente no está configurado correctamente."
  ]);
  exit;
}

// Petición a OpenAI
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
    "Authorization: Bearer " . $apiKey
  ],
  CURLOPT_POSTFIELDS => json_encode($data)
]);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if (!isset($result['choices'][0]['message']['content'])) {
  echo json_encode([
    "reply" => "No fue posible generar una respuesta en este momento."
  ]);
  exit;
}

echo json_encode([
  "reply" => $result['choices'][0]['message']['content']
]);
