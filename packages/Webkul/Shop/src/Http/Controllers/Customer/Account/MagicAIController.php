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
            'context' => 'nullable|in:org,bank'
        ]);

        try {
            $context = $request->input('context', 'bank');
            $file = $request->file('file');
            $mimeType = $file->getMimeType();
            $base64 = base64_encode(file_get_contents($file->getPathname()));

            $model = env('MAGIC_AI_MODEL', 'qwen2.5');

            if ($context === 'org') {
                $prompt = "Extract Russian organization details and bank details from this document.
                I need the following information:
                - INN (ИНН, 10 or 12 digits)
                - KPP (КПП, 9 digits)
                - Organization Name (Название организации/ФИО ИП)
                - BIC (БИК, 9 digits)
                - Settlement Account (Расчетный счет, 20 digits)
                - Correspondent Account (Корр. счет, 20 digits)
                Return ONLY a valid JSON object with keys: 'inn', 'kpp', 'name', 'bic', 'account', 'corr_account'. 
                Do not include any other text.
                If not found, return empty strings for the values.
                Example: {\"inn\": \"7712345678\", \"kpp\": \"771201001\", \"name\": \"ООО Ромашка\", \"bic\": \"044525225\", \"account\": \"40702810400000000123\", \"corr_account\": \"30101810400000000225\"}";
            } else {
                $prompt = "Extract Russian bank details from this document.
                I need the BIC (9 digits) and the Settlement Account (Расчетный счет, 20 digits). 
                Return ONLY a valid JSON object with 'bic' and 'account' keys. 
                Do not include any other text.
                If not found, return empty strings for the values.
                Example: {\"bic\": \"044525225\", \"account\": \"40702810400000000123\"}";
            }

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

            if (json_last_error() !== JSON_ERROR_NONE || empty($data)) {
                // Try one more time with a simple regex if JSON fails or is empty
                if ($context === 'org') {
                    // Try to find labeled values first
                    preg_match('/(?:inn|инн).*?(\d{10,12})/i', $response, $innMatch);
                    preg_match('/(?:kpp|кпп).*?(\d{9})/i', $response, $kppMatch);

                    // If no labels, just grab the first matching digit sequences
                    if (empty($innMatch[1])) { // Check for actual value, not just match array existence
                        preg_match('/\b(\d{10}|\d{12})\b/', $response, $innMatch);
                    }
                    if (empty($kppMatch[1])) { // Check for actual value
                        preg_match('/\b(\d{9})\b/', $response, $kppMatch);
                    }

                    $data = [
                        'inn' => $innMatch[1] ?? '',
                        'kpp' => $kppMatch[1] ?? '',
                        'name' => '', // Hard to regex name reliably from small models, rely on JSON if it worked
                        'bic' => '',
                        'account' => '',
                        'corr_account' => '',
                    ];

                    // Also try to find bank details if they were in the org document
                    preg_match('/(?:bic|бик).*?(\d{9})/i', $response, $bicMatch);
                    preg_match('/(?:расчетный.*?счет|р\/с|р\sс|account).*?(\d{20})/ui', $response, $accMatch);
                    preg_match('/(?:корреспондентский.*?счет|к\/с|к\sс|corr).*?(\d{20})/ui', $response, $corrMatch);

                    if (!empty($bicMatch[1]))
                        $data['bic'] = $bicMatch[1];
                    if (!empty($accMatch[1]))
                        $data['account'] = $accMatch[1];
                    if (!empty($corrMatch[1]))
                        $data['corr_account'] = $corrMatch[1];

                } else {
                    // Banks
                    preg_match('/(?:bic|бик).*?(\d{9})/i', $response, $bicMatch);
                    preg_match('/(?:account|счет|счёт|р\/с).*?(\d{20})/ui', $response, $accMatch);

                    if (empty($bicMatch)) {
                        preg_match('/\b(\d{9})\b/', $response, $bicMatch);
                    }
                    if (empty($accMatch)) {
                        preg_match('/\b(\d{20})\b/', $response, $accMatch);
                    }

                    $data = [
                        'bic' => $bicMatch[1] ?? '',
                        'account' => $accMatch[1] ?? '',
                    ];
                }
            }

            // Fallback for empty strings if JSON parsed but values are missing
            if ($context === 'org') {
                if (empty($data['inn'])) {
                    preg_match('/\b(\d{10}|\d{12})\b/', $response, $fallbackInn);
                    if (!empty($fallbackInn[1]))
                        $data['inn'] = $fallbackInn[1];
                }

                // Aggressive fallback for 20-digit accounts if missing in org context
                if (empty($data['account'])) {
                    preg_match('/(?:расчетный.*?счет|р\/с|р\sс).*?(\d{20})/ui', $response, $accMatch);
                    if (!empty($accMatch[1]))
                        $data['account'] = $accMatch[1];
                }
                if (empty($data['corr_account'])) {
                    preg_match('/(?:корреспондентский.*?счет|к\/с|к\sс).*?(\d{20})/ui', $response, $corrMatch);
                    if (!empty($corrMatch[1]))
                        $data['corr_account'] = $corrMatch[1];
                }
                if (empty($data['bic'])) {
                    preg_match('/(?:bic|бик).*?(\d{9})/i', $response, $bicFallback);
                    if (!empty($bicFallback[1]))
                        $data['bic'] = $bicFallback[1];
                }
            }

            if ($context === 'bank' && empty($data['account'])) {
                preg_match('/\b(\d{20})\b/', $response, $fallback);
                if (!empty($fallback[1]))
                    $data['account'] = $fallback[1];
            }

            return new JsonResponse($data);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
                'response_dump' => $response ?? null // helpful for debugging
            ], 500);
        }
    }
}
