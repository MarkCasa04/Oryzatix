<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatbotKnowledge;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ConsultationController extends Controller
{
    private $diseaseResponses = [
        'blast' => [
            'tagalog' => 'Para sa Leaf Blast, inirerekomenda ko ang mga sumusunod: 1) Mag-spray ng Tricyclazole fungicide (1-2 ml/L tubig). 2) Iwasan ang sobrang nitrogen fertilizer. 3) Gumamit ng resistant varieties tulad ng PSB Rc18. 4) Panatilihin ang tamang drainage ng field. May iba ka pa bang katanungan?',
            'english' => 'For Leaf Blast, I recommend: 1) Spray with Tricyclazole fungicide (1-2 ml/L water). 2) Avoid excessive nitrogen fertilizer. 3) Use resistant varieties like PSB Rc18. 4) Maintain proper field drainage. Do you have any other questions?',
        ],
        'blight' => [
            'tagalog' => 'Para sa Bacterial Leaf Blight: 1) Mag-apply ng Streptomycin Sulfate (100-200 ppm). 2) Gumamit ng resistant varieties na may Xa4/Xa7 genes. 3) Alisin at sunugin ang mga infected na dahon. 4) Iwasan ang sobrang pagbaha sa field.',
            'english' => 'For Bacterial Leaf Blight: 1) Apply Streptomycin Sulfate (100-200 ppm). 2) Use resistant varieties with Xa4/Xa7 genes. 3) Remove and burn infected leaves. 4) Avoid over-flooding the field.',
        ],
        'brown' => [
            'tagalog' => 'Para sa Brown Spot: 1) Mag-apply ng Propiconazole (1 ml/L). 2) Itama ang nutrient deficiency lalo na ang potassium. 3) Gumamit ng Bacillus subtilis (5-10 g/L). 4) Gumamit ng tolerant varieties tulad ng PSB Rc14.',
            'english' => 'For Brown Spot: 1) Apply Propiconazole (1 ml/L). 2) Correct nutrient deficiencies especially potassium. 3) Use Bacillus subtilis (5-10 g/L). 4) Plant tolerant varieties like PSB Rc14.',
        ],
        'tungro' => [
            'tagalog' => 'Para sa Rice Tungro: 1) Kontrolin ang green leafhopper vectors gamit ang Imidacloprid (0.5-1 ml/L). 2) Gumamit ng resistant varieties tulad ng PSB Rc10 o NSIC Rc160. 3) Magsagawa ng synchronous planting. 4) Alisin agad ang mga infected na halaman.',
            'english' => 'For Rice Tungro: 1) Control green leafhopper vectors with Imidacloprid (0.5-1 ml/L). 2) Use resistant varieties like PSB Rc10 o NSIC Rc160. 3) Practice synchronous planting. 4) Remove infected plants immediately.',
        ],
        'mildew' => [
            'tagalog' => 'Para sa Downy Mildew: 1) Gumamit ng Metalaxyl bilang seed treatment (2 g/kg) o foliar spray. 2) Siguraduhing maayos ang field drainage. 3) Gumamit ng mas malawak na planting spacing. 4) Iwasan ang waterlogged conditions.',
            'english' => 'For Downy Mildew: 1) Use Metalaxyl as seed treatment (2 g/kg) or foliar spray. 2) Ensure proper field drainage. 3) Use wider planting spacing. 4) Avoid waterlogged conditions.',
        ],
        'hispa' => [
            'tagalog' => 'Para sa Rice Hispa: 1) Mag-spray ng Chlorpyrifos (2 ml/L) o Lambda-Cyhalothrin (0.5 ml/L). 2) Gumamit ng Beauveria bassiana (3-5 g/L) bilang biological control. 3) Pumutol at sunugin ang mga heavily infested na dahon. 4) Iwasan ang sobrang nitrogen.',
            'english' => 'For Rice Hispa: 1) Spray Chlorpyrifos (2 ml/L) or Lambda-Cyhalothrin (0.5 ml/L). 2) Use Beauveria bassiana (3-5 g/L) as biological control. 3) Prune and burn heavily infested leaves. 4) Avoid excessive nitrogen.',
        ],
        'smut' => [
            'tagalog' => 'Para sa Leaf Smut: 1) Mag-apply ng Propiconazole + Difenoconazole (1 ml/L). 2) Sumunod sa balanced fertilization, iwasan ang sobrang nitrogen. 3) Gumamit ng clean seeds at resistant varieties. 4) Maaari ding gumamit ng compost tea + garlic extract spray.',
            'english' => 'For Leaf Smut: 1) Apply Propiconazole + Difenoconazole (1 ml/L). 2) Follow balanced fertilization, avoid excess nitrogen. 3) Use clean seeds and resistant varieties. 4) You can also use compost tea + garlic extract spray.',
        ],
        'treatment' => [
            'tagalog' => 'Mayroon akong kumpletong listahan ng mga treatment options batay sa pamantayan ng DA-PhilRice! Mayroon tayong Mild (≤25%), Moderate (26%-60%), at Severe (>60%) treatments para sa Leaf Blast, Bacterial Leaf Blight, Brown Spot, at Tungro. Aling sakit ang nais mong malaman ang gamot at dosage?',
            'english' => 'I have a complete list of treatment options based on DA-PhilRice standards! We provide Mild (≤25%), Moderate (26%-60%), and Severe (>60%) treatments for Leaf Blast, Bacterial Leaf Blight, Brown Spot, and Tungro. Which disease would you like dosage details for?',
        ],
        'overwatering' => [
            'tagalog' => 'Tungkol sa Sobrang Tubig / Baha (Overwatering): Ang nakatenggang tubig ay nagpapataas ng halumigmig (humidity) sa palayan at nagdudulot ng root rot at pagkalat ng Bacterial Leaf Blight (BLB). Rekomendasyon ng DA-PhilRice: 1) Isagawa ang Alternate Wetting and Drying (AWD). 2) Patuyuin ang bukid nang 2-3 araw para sumingaw ang nakalalasong hydrogen sulfide at magkaroon ng hangin ang mga ugat.',
            'english' => 'Regarding Overwatering / Waterlogging: Stagnant water increases canopy humidity, causes root asphyxiation, and accelerates the spread of Bacterial Leaf Blight (BLB). DA-PhilRice recommendations: 1) Practice Alternate Wetting and Drying (AWD). 2) Drain the field for 2–3 days to release toxic hydrogen sulfide and aerate root systems.',
        ],
        'nitrogen' => [
            'tagalog' => 'Tungkol sa Sobrang Pataba / Mataba sa Urea (Nitrogen Overload): Ang labis na Urea (46-0-0) ay nagpapadali sa paglambot at pagiging makatas (succulent) ng dahon kaya madaling kapitan ng Leaf Blast at Sheath Blight. Rekomendasyon ng DA-PhilRice: 1) Agarang itigil ang topdressing ng Urea. 2) Gamitin ang Leaf Color Chart (LCC) para sa tamang timpla. 3) Mag-abono ng Muriate of Potash (0-0-60 @ 30-40 kg/ha) upang patibayin ang cell walls ng dahon.',
            'english' => 'Regarding Excess Nitrogen / Fertilizer Overload: Excessive Urea (46-0-0) produces soft, succulent leaf tissue highly vulnerable to Leaf Blast and Sheath Blight. DA-PhilRice recommendations: 1) Immediately halt nitrogen topdressing. 2) Use the Leaf Color Chart (LCC) for calibrated application. 3) Apply Muriate of Potash (0-0-60 @ 30–40 kg/ha) to strengthen leaf cell walls.',
        ],
        'drought' => [
            'tagalog' => 'Tungkol sa Tuyo / Kulang sa Tubig (Water Stress / Drought): Ang tuyong lupa sa panahon ng pagsusuwi (tillering) ay nagpapahina sa natural na resistensya ng palay at nagpapabilis sa impeksyon ng Leaf Blast at Brown Spot. Rekomendasyon: 1) Panatilihin ang mababaw na patubig (3-5 cm) sa vegetative stage. 2) Huwag hayaang magbitak-bitak ang lupa. 3) Maglagay ng Carbonized Rice Hull (CRH) o dayami para mapanatili ang moisture.',
            'english' => 'Regarding Drought / Dry Soil Stress: Water deficit during the vegetative/tillering stage impairs nutrient absorption and predisposes rice to Leaf Blast and Brown Spot. Recommendations: 1) Maintain shallow continuous water (3–5 cm) during tillering. 2) Avoid severe soil cracking. 3) Incorporate Carbonized Rice Hull (CRH) or organic mulch to retain soil moisture.',
        ],
        'deficiency' => [
            'tagalog' => 'Tungkol sa Kakulangan sa Sustansya (Nutrient Deficiency - Potassium/Zinc/Silica): Ito ang pangunahing sanhi ng Brown Spot (Bipolaris oryzae) at paninilaw ng dahon. Rekomendasyon ng DA-PhilRice: 1) Maglagay ng Muriate of Potash (0-0-60 @ 30-40 kg/ha) na hinati sa basal at panicle initiation. 2) Mag-apply ng Zinc Sulfate (25 kg/ha). 3) Maglagay ng compost (2-3 t/ha) o pinunong dayami para sa Silica.',
            'english' => 'Regarding Nutrient Deficiency (Potassium, Zinc, Silica): Soil nutrient deficiency is the primary predisposing factor for Brown Spot (Bipolaris oryzae). DA-PhilRice recommendations: 1) Apply Muriate of Potash (0-0-60 @ 30–40 kg/ha) split into basal and panicle initiation. 2) Apply Zinc Sulfate (25 kg/ha). 3) Incorporate compost (2–3 t/ha) or rice hull ash for silica supplementation.',
        ],
    ];

    private $genericResponses = [
        'tagalog' => [
            'Salamat sa iyong tanong! Ang pag-aalaga ng palay ay nangangailangan ng tamang impormasyon. Anong partikular na aspeto ng rice farming ang gusto mong malaman?',
            'Magandang tanong iyan! Para sa pinakamahusay na resulta, siguraduhing sundin ang mga rekomendasyon ng inyong Municipal Agriculture Office. May iba ka pa bang gustong itanong tungkol sa pagtatanim?',
            'Naiintindihan ko ang iyong pag-aalala. Tandaan na ang maagang pagtukoy ng sakit ay mahalaga para sa epektibong paggamot. Gusto mo bang malaman ang mga senyales ng mga karaniwang sakit ng palay?',
        ],
        'english' => [
            'Thank you for your question! Rice farming requires proper information. What specific aspect of rice farming would you like to know about?',
            'That is a great question! For best results, be sure to follow your Municipal Agriculture Office recommendations. Do you have any other questions about farming?',
            'I understand your concern. Remember that early detection of disease is crucial for effective treatment. Would you like to know the symptoms of common rice diseases?',
        ],
    ];

    public function index(): JsonResponse
    {
        $messages = [];
        try {
            $userId = auth()->id();
            $query = ChatMessage::query();
            if ($userId) {
                $query->where('user_id', $userId);
            }

            $messages = $query->orderBy('created_at', 'asc')->get()->map(function ($msg) {
                return [
                    'role' => $msg->role,
                    'content' => $msg->content,
                    'language' => $msg->language,
                    'time' => $msg->created_at->format('g:i A'),
                ];
            })->all();
        } catch (Exception $e) {
        }

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string',
            'language' => 'required|in:tagalog,english',
        ]);

        $userMessage = trim($request->input('message'));
        $language = $request->input('language');
        $userId = auth()->id();

        try {
            ChatMessage::create([
                'user_id' => $userId,
                'role' => 'user',
                'content' => $userMessage,
                'language' => $language,
            ]);
        } catch (Exception $e) {
        }

        $aiResponse = $this->generateResponse($userMessage, $language, $userId);

        $time = now()->format('g:i A');
        try {
            $aiMsg = ChatMessage::create([
                'user_id' => $userId,
                'role' => 'ai',
                'content' => $aiResponse,
                'language' => $language,
            ]);
            $time = $aiMsg->created_at->format('g:i A');
        } catch (Exception $e) {
        }

        return response()->json([
            'success' => true,
            'ai_response' => $aiResponse,
            'language' => $language,
            'time' => $time,
        ]);
    }

    public function clear(): JsonResponse
    {
        try {
            $userId = auth()->id();
            if ($userId) {
                ChatMessage::where('user_id', $userId)->delete();
            } else {
                ChatMessage::query()->delete();
            }
        } catch (Exception $e) {
        }
        return response()->json(['success' => true]);
    }

    private function generateResponse(string $message, string $language, ?int $userId = null): string
    {
        // 1. Primary: Google Gemini API (Domain Grounded with strict guardrails)
        $geminiResponse = $this->callGeminiApi($message, $language, $userId);
        if (!empty($geminiResponse)) {
            return $geminiResponse;
        }

        // 2. Dynamic Chatbot Knowledge Base (Admin Managed)
        $msg = strtolower($message);
        try {
            $knowledgeItems = ChatbotKnowledge::where('status', 'active')
                ->where(function($q) use ($language) {
                    $q->where('language', $language)->orWhere('language', 'all');
                })
                ->get();

            foreach ($knowledgeItems as $item) {
                $itemQ = strtolower($item->question);
                $qWords = array_filter(explode(' ', preg_replace('/[^a-z0-9 ]/i', '', $itemQ)), fn($w) => strlen($w) > 3);
                $matches = 0;
                $totalWords = count($qWords);
                foreach ($qWords as $qw) {
                    if (str_contains($msg, $qw)) $matches++;
                }
                if (($totalWords > 0 && ($matches / $totalWords) >= 0.6) || str_contains($msg, $itemQ)) {
                    return $item->answer;
                }
            }
        } catch (Exception $e) {
        }

        // 3. Environmental & Cultural Causes (Overwatering, Nitrogen, Drought, Deficiency)
        if (str_contains($msg, 'overwater') || str_contains($msg, 'sobrang tubig') || str_contains($msg, 'baha') || str_contains($msg, 'nakatengga') || str_contains($msg, 'waterlog') || str_contains($msg, 'awd')) {
            if (isset($this->diseaseResponses['overwatering'][$language])) {
                return $this->diseaseResponses['overwatering'][$language];
            }
        }

        if (str_contains($msg, 'mataba') || str_contains($msg, 'urea') || str_contains($msg, 'nitrogen') || str_contains($msg, 'sobrang pataba') || str_contains($msg, 'sobrang abono') || str_contains($msg, 'lcc') || str_contains($msg, 'leaf color')) {
            if (isset($this->diseaseResponses['nitrogen'][$language])) {
                return $this->diseaseResponses['nitrogen'][$language];
            }
        }

        if (str_contains($msg, 'tuyo') || str_contains($msg, 'tagtuyot') || str_contains($msg, 'drought') || str_contains($msg, 'kulang sa tubig') || str_contains($msg, 'water stress') || str_contains($msg, 'dry')) {
            if (isset($this->diseaseResponses['drought'][$language])) {
                return $this->diseaseResponses['drought'][$language];
            }
        }

        if (str_contains($msg, 'sustansya') || str_contains($msg, 'potassium') || str_contains($msg, 'potash') || str_contains($msg, 'zinc') || str_contains($msg, 'silica') || str_contains($msg, 'deficiency') || str_contains($msg, 'kulang sa pataba')) {
            if (isset($this->diseaseResponses['deficiency'][$language])) {
                return $this->diseaseResponses['deficiency'][$language];
            }
        }

        // 4. Specific Rice Diseases
        if (str_contains($msg, 'blast') || str_contains($msg, 'amag') || str_contains($msg, 'magnaporthe')) {
            if (isset($this->diseaseResponses['blast'][$language])) {
                return $this->diseaseResponses['blast'][$language];
            }
        }

        if (str_contains($msg, 'blight') || str_contains($msg, 'blb') || str_contains($msg, 'xanthomonas') || str_contains($msg, 'bacterial')) {
            if (isset($this->diseaseResponses['blight'][$language])) {
                return $this->diseaseResponses['blight'][$language];
            }
        }

        if (str_contains($msg, 'brown') || str_contains($msg, 'bipolaris') || str_contains($msg, 'spot')) {
            if (isset($this->diseaseResponses['brown'][$language])) {
                return $this->diseaseResponses['brown'][$language];
            }
        }

        if (str_contains($msg, 'tungro') || str_contains($msg, 'leafhopper') || str_contains($msg, 'glh') || str_contains($msg, 'rtbv') || str_contains($msg, 'rtsv')) {
            if (isset($this->diseaseResponses['tungro'][$language])) {
                return $this->diseaseResponses['tungro'][$language];
            }
        }

        if (str_contains($msg, 'healthy') || str_contains($msg, 'malusog')) {
            return ($language === 'tagalog')
                ? 'Para mapanatiling malusog ang palay: 1) Panatilihin ang balanseng NPK fertilizer at regular na LCC monitoring. 2) Gamitin ang AWD patubig. 3) Panatilihing malinis ang mga pilapil laban sa peste at damo. 4) Mag-apply ng organikong compost (2-3 t/ha).'
                : 'To maintain healthy rice crops: 1) Practice balanced NPK fertilization with regular LCC monitoring. 2) Use AWD irrigation. 3) Keep bunds and levees clean from weed vectors. 4) Apply organic compost (2–3 t/ha).';
        }

        if (str_contains($msg, 'gamot') || str_contains($msg, 'treatment') || str_contains($msg, 'lunas') || str_contains($msg, 'dosage') || str_contains($msg, 'rekomendasyon')) {
            if (isset($this->diseaseResponses['treatment'][$language])) {
                return $this->diseaseResponses['treatment'][$language];
            }
        }

        // Off-topic or unrecognized general questions fallback
        if ($language === 'tagalog') {
            return "Paumanhin po, ako po ay si Oryzatix AI Agronomist na nakalaan lamang para sumagot sa mga katanungan tungkol sa mga sakit ng palay (Bacterial Leaf Blight, Rice Tungro, Brown Spot, Sheath Blight, Leaf Blast), pangangalaga sa malusog na palay, at ang mga kaukulang lunas at gamot ayon sa pamantayan ng DA-PhilRice. May maitutulong po ba ako tungkol sa inyong palayan?";
        }

        return "I apologize, but I am specifically designed as the Oryzatix AI Agronomist to only assist with rice leaf diseases (Bacterial Leaf Blight, Rice Tungro, Brown Spot, Sheath Blight, Leaf Blast), healthy rice crop care, and their respective treatment recommendations under DA-PhilRice standards. How can I assist you with your rice crop today?";
    }

    /**
     * Call Google Gemini REST API with domain-restricted grounding prompt.
     * Returns null if API key is not configured or if API call fails, allowing seamless fallback.
     */
    private function callGeminiApi(string $userMessage, string $language, ?int $userId = null): ?string
    {
        $apiKey = config('services.gemini.api_key') ?: env('GEMINI_API_KEY');
        if (empty($apiKey)) {
            return null;
        }

        $model = config('services.gemini.model') ?: env('GEMINI_MODEL', 'gemini-1.5-flash');

        $systemPrompt = $this->buildGeminiSystemPrompt($language);

        // Fetch recent conversation history (up to last 6 messages) for conversational continuity
        $contents = [];
        if ($userId) {
            try {
                $recentMessages = ChatMessage::where('user_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->limit(6)
                    ->get()
                    ->reverse();

                foreach ($recentMessages as $msg) {
                    $contents[] = [
                        'role' => $msg->role === 'ai' ? 'model' : 'user',
                        'parts' => [
                            ['text' => $msg->content]
                        ]
                    ];
                }
            } catch (Exception $e) {
            }
        }

        // Add current user message
        $contents[] = [
            'role' => 'user',
            'parts' => [
                ['text' => $userMessage]
            ]
        ];

        $payload = [
            'system_instruction' => [
                'parts' => [
                    ['text' => $systemPrompt]
                ]
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.2,
                'topP' => 0.8,
                'topK' => 40,
                'maxOutputTokens' => 800,
            ]
        ];

        $configuredModel = config('services.gemini.model') ?: env('GEMINI_MODEL', 'gemini-3.6-flash');
        $modelsToTry = array_unique([$configuredModel, 'gemini-3.6-flash', 'gemini-3.7-flash', 'gemini-3.5-flash', 'gemini-flash-latest']);

        foreach ($modelsToTry as $mName) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$mName}:generateContent?key={$apiKey}";

                $response = Http::timeout(12)->post($url, $payload);

                if ($response->successful()) {
                    $data = $response->json();
                    $candidates = $data['candidates'] ?? [];
                    if (!empty($candidates)) {
                        $parts = $candidates[0]['content']['parts'] ?? [];
                        if (!empty($parts)) {
                            $reply = trim($parts[0]['text'] ?? '');
                            if (!empty($reply)) {
                                return $reply;
                            }
                        }
                    }
                } else {
                    Log::warning("Gemini API ($mName) status: " . $response->status() . ' body: ' . $response->body());
                }
            } catch (Exception $e) {
                Log::error("Failed calling Google Gemini API ($mName): " . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Build strict domain-restricted system prompt for Oryzatix Rice Disease Consultation.
     */
    private function buildGeminiSystemPrompt(string $language): string
    {
        return <<<PROMPT
You are "Oryzatix AI Agronomist" (Oryzatix AI Konsultasyon), an expert agricultural AI assistant dedicated EXCLUSIVELY to rice farming and rice leaf diseases in the Philippines according to Department of Agriculture - Philippine Rice Research Institute (DA-PhilRice) standards.

=======================================================
STRICT SCOPE & DOMAIN BOUNDARY (CRITICAL RULE):
=======================================================
1. YOU MUST ONLY ANSWER QUESTIONS STRICTLY RELATED TO:
   a) Rice Leaf Diseases in the Oryzatix system:
      • Bacterial Leaf Blight (BLB) — Xanthomonas oryzae pv. oryzae
      • Rice Tungro Disease — Rice Tungro Bacilliform Virus (RTBV) + Rice Tungro Spherical Virus (RTSV), transmitted by Green Leafhopper (Nephotettix virescens)
      • Rice Brown Spot — Bipolaris oryzae / Cochliobolus miyabeanus
      • Sheath Blight — Rhizoctonia solani
      • Rice Leaf Blast — Magnaporthe oryzae
   b) Healthy Rice Leaf Care & Maintenance (AWD irrigation, balanced N-P-K, Leaf Color Chart LCC, MOP 0-0-60 potash, Zinc Sulfate, organic compost).
   c) Severity Levels (Mild ≤ 25%, Moderate 26%–60%, Severe > 60%) and their corresponding Chemical (exact active ingredients, fungicides, bactericides, insecticides, dosages) and Organic/Cultural treatments.
   d) Causes, prevention, crop sanitation, water management, and agronomic practices for rice crops.

2. IMMEDIATE POLITE REFUSAL FOR OFF-TOPIC QUESTIONS:
   If the user asks about ANY topic NOT related to rice crops, rice leaf diseases, rice soil nutrients, or agricultural management (for example: coding, math, politics, celebrity news, movies, cooking non-rice recipes, assignments, or diseases of non-rice plants like tomato, banana, mango, corn):
   You MUST POLITELY REFUSE to answer, stating that you are strictly dedicated to rice disease consultation and treatment in Oryzatix.

   • If language is 'tagalog', reply with:
     "Paumanhin po, ako po ay si Oryzatix AI Agronomist na nakalaan lamang para sumagot sa mga katanungan tungkol sa mga sakit ng palay (Bacterial Leaf Blight, Rice Tungro, Brown Spot, Sheath Blight, Leaf Blast), pangangalaga sa malusog na palay, at ang mga kaukulang lunas at gamot ayon sa pamantayan ng DA-PhilRice. May maitutulong po ba ako tungkol sa inyong palayan?"

   • If language is 'english', reply with:
     "I apologize, but I am specifically designed as the Oryzatix AI Agronomist to only assist with rice leaf diseases (Bacterial Leaf Blight, Rice Tungro, Brown Spot, Sheath Blight, Leaf Blast), healthy rice crop care, and their respective treatment recommendations under DA-PhilRice standards. How can I assist you with your rice crop today?"

=======================================================
KNOWLEDGE BASE & TREATMENT DOSAGE GUIDELINES:
=======================================================
• Bacterial Leaf Blight (BLB):
  - Mild (≤25%): Copper Hydroxide 77% WP (2.0–2.5 g/L), Kasugamycin 2% SL (2.0 ml/L). Suspend top-dress nitrogen.
  - Moderate (26%-60%): Streptomycin Sulfate + Oxytetracycline (Plantomycin / Agrimycin @ 150–200 ppm / 1.5–2.0 g/L), Zinc Thiazole 20% SC (1.5–2.0 ml/L). Field drainage for 2-3 days.
  - Severe (>60%): Therapeutic Streptomycin (2.0–2.5 g/L), Zinc Thiazole + Copper Hydroxide tank mix. Plant resistant varieties next season (PSB Rc82, NSIC Rc152).

• Rice Tungro Disease:
  - Mild (≤25%): Imidacloprid 17.8% SL (0.5–0.75 ml/L), Thiamethoxam 25% WG (0.2–0.3 g/L). Synchronous planting within 2 weeks, yellow sticky vector traps (20-25/ha).
  - Moderate (26%-60%): Dinotefuran 20% SG (0.5–1.0 g/L), Clothianidin + Pymetrozine (1.0 g/L), Buprofezin 25% SC (1.5–2.0 ml/L). Selective rogueing, Neem Seed Kernel Extract (NSKE 5%).
  - Severe (>60%): Etofenprox 10% EC (1.5–2.0 ml/L), Fipronil 5% SC. Systemic rogueing, switch to resistant varieties (NSIC Rc160, PSB Rc10), 30-day post-harvest fallow & deep plowing.

• Brown Spot:
  - Mild (≤25%): Mancozeb 80% WP (2.0–2.5 g/L), Propiconazole 25% EC (0.75–1.0 ml/L). Apply Muriate of Potash (30–40 kg K₂O/ha) & Zinc Sulfate (25 kg/ha).
  - Moderate (26%-60%): Tebuconazole 250 EC (0.75–1.0 ml/L), Azoxystrobin + Difenoconazole (1.0 ml/L), Hexaconazole 5% SC (1.5–2.0 ml/L). Split potassium topdress, AWD water aeration.
  - Severe (>60%): Propiconazole + Difenoconazole tank mix (1.5–2.0 g/L) to prevent pecky rice grain rot. Hot water seed treatment (52-54°C for 15 mins), agricultural lime (200-300 kg/ha).

• Rice Leaf Blast:
  - Tricyclazole 75% WP (0.6–0.8 g/L), Isoprothiolane 40% EC (1.5–2.0 ml/L), Azoxystrobin (1.0 ml/L). Immediate nitrogen suspension, silicon fertilization.

• Sheath Blight:
  - Validamycin 3% L (2.0–2.5 ml/L), Hexaconazole 5% SC (1.5–2.0 ml/L), Azoxystrobin + Difenoconazole (1.0 ml/L). Wide spacing (20x20 cm), Trichoderma harzianum bio-spray.

=======================================================
LANGUAGE & FORMATTING:
=======================================================
• Requested Language: {$language}
• If 'tagalog', speak in natural, helpful Tagalog/Filipino easily understood by Filipino rice farmers.
• If 'english', provide clear, structured, and actionable agronomic explanations.
• FORMATTING RULES:
  - Always organize your response into distinct, well-spaced paragraphs with empty line breaks between topics.
  - Use bullet points (• or -) or numbered steps (1., 2.) for actions, dosages, and recommendations so it is clean and easy to read.
  - Never clump all sentences together into one long, solid wall of text.
  - Use bold formatting (**active ingredient**, **dosage**) for critical medicine names and measurements.
PROMPT;
    }
}
