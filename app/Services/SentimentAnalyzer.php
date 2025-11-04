<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class SentimentAnalyzer
{
    protected $apiKey;
    protected $model;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key') ?: env('OPENAI_API_KEY');
        $this->model = env('OPENAI_MODEL', 'gpt-4o-mini');
    }

    /**
     * Analyze text and return ['label' => 'Positive'|'Negative'|'Toxic', 'confidence' => float, 'reason' => string]
     */
    public function analyze(string $text): array
    {
        // minimal safety: empty text
        if (trim($text) === '') {
            return ['label' => 'Positive', 'confidence' => 0.0, 'reason' => 'Empty text'];
        }

        // Prompt: instruct model to return strict JSON only.
        $system = "You are a classifier. Categorize the user's comment into exactly one of: Positive, Negative, or Toxic.
- Positive: praising or supportive language.
- Negative: critical or unhappy language but not attacking protected classes or individuals.
- Toxic: abusive language, insults, direct attacks, hate speech, slurs, body-shaming, religion attacks or calls to violence.
Return ONLY a JSON object with keys: label, confidence, reason.
confidence must be a number between 0 and 1. Example:
{\"label\":\"Toxic\",\"confidence\":0.92,\"reason\":\"Contains personal attack and body-shaming\"}
Do not add any other text.";

        $userPrompt = "Classify the following comment:\n\n\"\"\"\n{$text}\n\"\"\"\n\nReturn JSON only.";

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(10)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $system],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                    'max_tokens' => 200,
                    'temperature' => 0.0,
                ]);

            if (! $response->ok()) {
                // fallback
                return ['label' => 'Negative', 'confidence' => 0.0, 'reason' => 'API error'];
            }

            $body = $response->json();

            // try to extract assistant content
            $assistant = $body['choices'][0]['message']['content'] ?? ($body['choices'][0]['text'] ?? null);

            if (! $assistant) {
                return ['label' => 'Negative', 'confidence' => 0.0, 'reason' => 'No response body'];
            }

            // The model should return JSON, but be defensive: try to find JSON substring
            if (is_string($assistant)) {
                // extract first { ... } substring
                if (preg_match('/\{.*\}/s', $assistant, $m)) {
                    $json = $m[0];
                } else {
                    $json = $assistant;
                }

                $parsed = json_decode($json, true);
                if (json_last_error() === JSON_ERROR_NONE && isset($parsed['label'])) {
                    $label = ucfirst(strtolower($parsed['label']));
                    $confidence = isset($parsed['confidence']) ? floatval($parsed['confidence']) : null;
                    $reason = $parsed['reason'] ?? '';
                    return [
                        'label' => in_array($label, ['Positive','Negative','Toxic']) ? $label : 'Negative',
                        'confidence' => $confidence ?? 0.0,
                        'reason' => $reason,
                    ];
                }
            }

            // fallback if parsing failed: return Negative
            return ['label' => 'Negative', 'confidence' => 0.0, 'reason' => 'Could not parse model output'];

        } catch (Exception $e) {
            return ['label' => 'Negative', 'confidence' => 0.0, 'reason' => 'Exception: ' . $e->getMessage()];
        }
    }
}
