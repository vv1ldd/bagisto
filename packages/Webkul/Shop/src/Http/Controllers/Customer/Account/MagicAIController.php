<?php

namespace Webkul\Shop\Http\Controllers\Customer\Account;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Webkul\MagicAI\Facades\MagicAI;
use Webkul\Shop\Http\Controllers\Controller;

class MagicAIController extends Controller
{
    /**
     * Parse bank details from uploaded document using AI.
     */
    public function parseBankDetails(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        try {
            $file = $request->file('file');
            $mimeType = $file->getMimeType();
            $base64 = base64_encode(file_get_contents($file->getPathname()));

            $model = core()->getConfigData('general.magic_ai.settings.model') ?: 'gemini-2.0-flash';

            $prompt = "Extract Russian bank details from this document.
            I need the BIC (9 digits) and the Settlement Account (Расчетный счет, 20 digits). 
            Return ONLY a valid JSON object with 'bic' and 'account' keys. 
            Do not include any other text.
            If not found, return empty strings for the values.
            Example: {\"bic\": \"044525225\", \"account\": \"40702810400000000123\"}";

            $response = MagicAI::setModel($model)
                ->setPrompt($prompt)
                ->setAttachment($base64, $mimeType)
                ->ask();

            // Clean response in case AI adds markdown block
            $response = trim($response);
            if (str_starts_with($response, '```json')) {
                $response = preg_replace('/^```json\s*|\s*```$/', '', $response);
            }
            $response = trim($response);

            $data = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                // Try one more time with a simple regex if JSON fails
                preg_match('/"bic":\s*"(\d{9})"/', $response, $bicMatch);
                preg_match('/"account":\s*"(\d{20})"/', $response, $accMatch);

                $data = [
                    'bic' => $bicMatch[1] ?? '',
                    'account' => $accMatch[1] ?? '',
                ];
            }

            return new JsonResponse($data);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
