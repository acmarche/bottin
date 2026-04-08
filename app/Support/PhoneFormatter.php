<?php

declare(strict_types=1);

namespace App\Support;

use OpenAI\Laravel\Facades\OpenAI;

final class PhoneFormatter
{
    final public function formatPhone(string $phone): string
    {
        if ($phone !== '' && !preg_match('/^\+\d{1,3}(\s\d{2,4}){2,4}$/', $phone)) {
            $phone = $this->formatPhonesWithAi($phone);
            if ($phone === null) {
                return $phone;
            }
        }

        return $phone;
    }

    /**
     * @param string $phone
     * @return string|null
     */
    private function formatPhonesWithAi(string $phone): ?string
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
- If the input is clearly not a phone number or is unrecoverable, return null for that field.
- If a number already has a non-Belgian country code (e.g., +33, +49), keep that country code and format with spaces.
- Return ONLY a JSON object with the same keys as the input and the formatted values.
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

        if (!is_array($decoded)) {
            return null;
        }

        return $decoded[0];
    }
}
