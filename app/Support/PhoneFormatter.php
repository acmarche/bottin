<?php

declare(strict_types=1);

namespace App\Support;

use OpenAI\Laravel\Facades\OpenAI;

final class PhoneFormatter
{
    private const FORMAT_REGEX = '/^\+\d{1,3}(\s\d{2,4}){2,4}$/';

    public function formatPhone(string $phone): string
    {
        if ($phone === '' || preg_match(self::FORMAT_REGEX, $phone) === 1) {
            return $phone;
        }

        return $this->formatPhoneWithAi($phone) ?? $phone;
    }

    private function formatPhoneWithAi(string $phone): ?string
    {
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'temperature' => 0,
            'response_format' => ['type' => 'json_object'],
            'messages' => [
                [
                    'role' => 'system',
                    'content' => <<<'PROMPT'
You are a phone number formatter. Convert Belgian phone numbers to the international format: +32 XX XX XX XX.

Rules:
- Country code is Belgium (+32) unless another country code is explicitly present.
- Remove the leading 0 from the area/mobile code when adding +32 (e.g., 084 → +32 84).
- Separate digits in groups of 2-3 with spaces: +32 84 22 44 33 or +32 475 12 34 56.
- If a number already has a non-Belgian country code (e.g., +33, +49), keep that country code and format with spaces.
- If the input is clearly not a phone number or is unrecoverable, set "phone" to null.
- Return ONLY a JSON object of the form {"phone": "<formatted value or null>"}.
PROMPT,
                ],
                [
                    'role' => 'user',
                    'content' => $phone,
                ],
            ],
        ]);

        $content = $response->choices[0]->message->content;

        if ($content === null) {
            return null;
        }

        $decoded = json_decode($content, true);

        if (! is_array($decoded)) {
            return null;
        }

        $formatted = $decoded['phone'] ?? null;

        return is_string($formatted) ? $formatted : null;
    }
}
