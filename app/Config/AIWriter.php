<?php

namespace Config;

/**
 * @immutable
 */
class AIWriter
{
    //API URL
    public static $apiUrl = 'https://api.openai.com/v1/chat/completions';

    //AI Models
    public static $models = [
        'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
        'gpt-4' => 'GPT-4',
        'gpt-4o' => 'GPT-4o',
        'gpt-4o-mini' => 'GPT-4o mini',
    ];

    //AI Form Defaults
    public static $formDefaults = [
        'model' => 'gpt-4o-mini',
        'temperature' => '0.7', // between 0 and 1
        'tone' => 'casual', //academic, casual, critical, formal, humorous, inspirational, persuasive, professional
        'length' => 'medium', //very_short, short, medium, long, very_long
    ];

    //AI Promts by Languages
    public static $prompts = [
        'en' => "Write a {length} article on the topic: {topic}. Tone: {tone}. Create a suitable title for the article.",
        'ar' => "اكتب مقالًا بــ {length} حول الموضوع: {topic}. النغمة: {tone}. أنشئ عنوانًا مناسبًا للمقال.",
        'az' => "{topic} mövzusunda {length} uzunluğunda məqalə yazın. Üslub: {tone}. Məqalə üçün uyğun bir başlıq yaradın.",
        'bn' => "{topic} বিষয়ে {length} দৈর্ঘ্যের একটি নিবন্ধ লিখুন। সুর: {tone}। নিবন্ধটির জন্য একটি উপযুক্ত শিরোনাম তৈরি করুন।",
        'bs' => "Napiši članak dužine {length} na temu: {topic}. Ton: {tone}. Kreiraj odgovarajući naslov za članak.",
        'bg' => "Напишете {length} статия на тема: {topic}. Тон: {tone}. Създайте подходящо заглавие за статията.",
        'ca' => "Escriu un article de {length} sobre el tema: {topic}. To: {tone}. Crea un títol adequat per a l'article.",
        'cs' => "Napište {length} článek na téma: {topic}. Ton: {tone}. Vytvořte vhodný název pro článek.",
        'da' => "Skriv en {length} artikel om emnet: {topic}. Tone: {tone}. Opret en passende titel til artiklen.",
        'de' => "Schreibe einen {length} Artikel zum Thema: {topic}. Ton: {tone}. Erstelle einen passenden Titel für den Artikel.",
        'el' => "Γράψτε ένα άρθρο {length} για το θέμα: {topic}. Τόνος: {tone}. Δημιουργήστε έναν κατάλληλο τίτλο για το άρθρο.",
        'es' => "Escribe un artículo de {length} sobre el tema: {topic}. Tono: {tone}. Crea un título adecuado para el artículo.",
        'et' => "Kirjutage {length} artikkel teemal: {topic}. Toon: {tone}. Looge sobiv pealkiri artiklile.",
        'eu' => "Idatzi {length} artikulu gaiari buruz: {topic}. Tonua: {tone}. Sortu artikulua egokia den izenburua.",
        'fi' => "Kirjoita {length} artikkeli aiheesta: {topic}. Sävy: {tone}. Luo sopiva otsikko artikkelille.",
        'fr' => "Écrivez un article {length} sur le sujet : {topic}. Ton : {tone}. Créez un titre approprié pour l'article.",
        'ga' => "Scríobh alt {length} ar an ábhar: {topic}. Ton: {tone}. Cruthaigh teideal cuí don alt.",
        'gl' => "Escribe un artigo de {length} sobre o tema: {topic}. Ton: {tone}. Crea un título axeitado para o artigo.",
        'he' => "כתוב מאמר באורך {length} בנושא: {topic}. טון: {tone}. צור כותרת מתאימה למאמר.",
        'hi' => "विषय: {topic} पर {length} लेख लिखें। टोन: {tone}। लेख के लिए उपयुक्त शीर्षक बनाएँ।",
        'hr' => "Napišite {length} članak na temu: {topic}. Ton: {tone}. Kreirajte odgovarajući naslov za članak.",
        'ht' => "Ekri yon atik {length} sou sijè a: {topic}. Ton: {tone}. Kreye yon tit ki apwopriye pou atik la.",
        'hu' => "Írjon {length} cikket a következő témáról: {topic}. Hang: {tone}. Hozzon létre egy megfelelő címet a cikkhez.",
        'hy' => "Գրեք {length} հոդված թեմայով: {topic}. Տոնը: {tone}. Ստեղծեք հոդվածի համապատասխան վերնագիր:",
        'id' => "Tulis artikel {length} tentang topik: {topic}. Nada: {tone}. Buat judul yang sesuai untuk artikel tersebut.",
        'is' => "Skrifaðu {length} grein um efnið: {topic}. Tónn: {tone}. Búðu til viðeigandi fyrirsagnir fyrir greinina.",
        'it' => "Scrivi un articolo di {length} sul tema: {topic}. Tono: {tone}. Crea un titolo adeguato per l'articolo.",
        'ja' => "トピック：{topic}について{length}の記事を書いてください。トーン：{tone}。記事に適切なタイトルを作成してください。",
        'jw' => "Tulis artikel {length} babagan topik: {topic}. Nada: {tone}. Gawe judhul sing cocog kanggo artikel kasebut.",
        'ka' => "წერო {length} სტატია თემაზე: {topic}. ტონი: {tone}. შექმენი სტატიის შესაფერისი სათაური.",
        'kk' => "Тақырып бойынша {length} мақала жазыңыз: {topic}. Тон: {tone}. Мақалаға сәйкес тақырып жасаңыз.",
        'km' => "សរសេរអត្ថបទ {length} ជុំវិញប្រធានបទ: {topic}. សំឡេង: {tone}. បង្កើតចំណងជើងសមរម្យសម្រាប់អត្ថបទ។",
        'kn' => "ವಿಷಯ: {topic} ಬಗ್ಗೆ {length} length ಇ article ಬರೆಯಿರಿ. ಶೈಲಿ: {tone}. ಲೇಖನಕ್ಕೆ ಸೂಕ್ತವಾದ ಶೀರ್ಷಿಕೆ ರಚಿಸಿ.",
        'ko' => "주제: {topic}에 대해 {length} 기사를 작성하십시오. 톤: {tone}. 기사에 적합한 제목을 만드세요.",
        'ky' => "Тема боюнча {length} макала жазыңыз: {topic}. Тон: {tone}. Макала үчүн ылайыктуу аталыш жасаңыз.",
        'la' => "Sribe articulum longitudinis {length} de re: {topic}. Tonus: {tone}. Crea titulum aptum pro articulo.",
        'lo' => "ຂຽນບົດຄວາມ {length} ກ່ອນຫົວຂໍ້: {topic}. ສຳລັບ: {tone}. ສ້າງຊື່ຫົວຂໍ້ທີ່ຫາກົວສຳລັບບົດຄວາມ.",
        'lt' => "Parašykite {length} straipsnį apie temą: {topic}. Tonas: {tone}. Sukurkite tinkamą straipsnio pavadinimą.",
        'lv' => "Uzrakstiet {length} rakstu par tēmu: {topic}. Tonis: {tone}. Izveidojiet atbilstošu virsrakstu rakstam.",
        'mk' => "Напишете {length} статия на тема: {topic}. Тон: {tone}. Создайте подходящо заглавие за статията.",
        'ml' => "{topic} വിഷയം സംബന്ധിച്ച {length} പ്രബന്ധം എഴുതുക. ശൈലി: {tone}. ലേഖനത്തിന് അനുയോജ്യമായ ശീർഷകം സൃഷ്‌ടിക്കുക.",
        'mr' => "विषय: {topic} वर {length} लेख लिहा. टोन: {tone}. लेखासाठी योग्य शीर्षक तयार करा.",
        'ms' => "Tulis artikel {length} mengenai topik: {topic}. Nada: {tone}. Buatkan tajuk yang sesuai untuk artikel tersebut.",
        'my' => "{topic} အကြောင်း {length} ဆောင်းပါးရေးပါ။ အသံ: {tone}. ဆောင်းပါးအတွက်သင့်တော်သောခေါင်းစဉ်တစ်ခုတည်ဆောက်ပါ။",
        'ne' => "विषय: {topic} बारे {length} लेख लेख्नुहोस्। स्वर: {tone}. लेखको लागि उपयुक्त शीर्षक सिर्जना गर्नुहोस्।",
        'nl' => "Schrijf een {length} artikel over het onderwerp: {topic}. Toon: {tone}. Maak een geschikte titel voor het artikel.",
        'no' => "Skriv en {length} artikkel om emnet: {topic}. Tone: {tone}. Lag en passende tittel for artikkelen.",
        'pa' => "ਵਿਸ਼ਾ: {topic} ਬਾਰੇ {length} ਲੰਬਾ ਲੇਖ ਲਿਖੋ। ਟੋਨ: {tone}. ਲੇਖ ਲਈ ਉਚਿਤ ਸ਼ੀਰਸ਼ਕ ਬਣਾਓ।",
        'pl' => "Napisz {length} artykuł na temat: {topic}. Ton: {tone}. Stwórz odpowiedni tytuł dla artykułu.",
        'pt' => "Escreva um artigo de {length} sobre o tema: {topic}. Tom: {tone}. Crie um título adequado para o artigo.",
        'ro' => "Scrieți un articol de {length} pe tema: {topic}. Ton: {tone}. Creați un titlu adecvat pentru articol.",
        'ru' => "Напишите {length} статью на тему: {topic}. Тон: {tone}. Создайте подходящее название для статьи.",
        'se' => "Skriv en artikel på {length} om ämnet: {topic}. Ton: {tone}. Skapa en lämplig titel för artikeln.",
        'sk' => "Napíšte článok s dĺžkou {length} na tému: {topic}. Tón: {tone}. Vytvorte vhodný názov pre článok.",
        'sl' => "Napišite članek dolžine {length} na temo: {topic}. Ton: {tone}. Ustvarite ustrezen naslov za članek.",
        'sq' => "Shkruani një artikull {length} mbi temën: {topic}. Ton: {tone}. Krijoni një titull të përshtatshëm për artikullin.",
        'sr' => "Napišite članak dužine {length} na temu: {topic}. Ton: {tone}. Kreirajte odgovarajući naslov za članak.",
        'su' => "Tulis artikel {length} tentang topik: {topic}. Nada: {tone}. Buat judul yang sesuai untuk artikel tersebut.",
        'sv' => "Skriv en {length} artikel om ämnet: {topic}. Ton: {tone}. Skapa en lämplig titel för artikeln.",
        'sw' => "Andika makala ya urefu wa {length} kuhusu mada: {topic}. Tone: {tone}. Tengeneza kichwa kinachofaa kwa makala hiyo.",
        'ta' => "விஷயம்: {topic} குறித்த {length} கட்டுரை எழுதுங்கள். சுருதி: {tone}. கட்டுரைக்கு உரிய தலைப்பை உருவாக்கவும்.",
        'te' => "{topic} విషయంపై {length} వ్యాసం రాయండి. శైలి: {tone}. వ్యాసానికి తగిన శీర్షికను రూపొందించండి.",
        'th' => "เขียนบทความ {length} เกี่ยวกับหัวข้อ: {topic}. โทนเสียง: {tone}. สร้างหัวข้อที่เหมาะสมสำหรับบทความ",
        'tl' => "Sumulat ng isang {length} artikulo tungkol sa paksa: {topic}. Tono: {tone}. Gumawa ng angkop na pamagat para sa artikulo.",
        'tr' => "{topic} hakkında {length} bir makale yaz. Ton: {tone}. Makale için uygun bir başlık oluştur.",
        'uk' => "Напишіть {length} статтю на тему: {topic}. Тон: {tone}. Створіть відповідну назву для статті.",
        'ur' => "موضوع: {topic} پر {length} مضمون لکھیں۔ لہجہ: {tone}. مضمون کے لیے مناسب عنوان بنائیں۔",
        'vi' => "Viết một bài {length} về chủ đề: {topic}. Giọng điệu: {tone}. Tạo một tiêu đề phù hợp cho bài viết."
    ];

    //generate AI promt
    public static function generateAIPrompt($langCode, $length, $topic, $tone)
    {
        $prompt = '';
        $prompts = self::$prompts;
        if (isset($prompts[$langCode])) {
            $prompt = $prompts[$langCode];
        } else {
            $prompt = $prompts['en'] ?? '';
        }
        if (!empty($prompt)) {
            $prompt = str_replace('{length}', $length, $prompt);
            $prompt = str_replace('{topic}', $topic, $prompt);
            $prompt = str_replace('{tone}', $tone, $prompt);
        }
        return $prompt;
    }

    //generate text
    public static function generateText($model, $temperature, $tone, $length, $topic, $langCode)
    {
        // Validate and set model
        $model = (!empty(self::$models) && array_key_exists($model, self::$models)) ? $model : 'gpt-4o-mini';

        // Validate temperature
        $temperature = floatval($temperature);
        if (empty($temperature) || $temperature > 1 || $temperature < 0) {
            $temperature = 0.7;
        }

        // Build the AI prompt
        $prompt = self::generateAIPrompt($langCode, $length, $topic, $tone);
        $data = [
            'model' => $model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => $temperature,
        ];

        try {
            // Initialize AI Writer
            $aiWriter = aiWriter();
            if (empty($aiWriter->apiKey)) {
                return json_encode([
                    'status' => 'error',
                    'message' => 'API key is missing. Add your API key from the Preferences section.'
                ]);
            }

            // Initialize cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::$apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $aiWriter->apiKey,
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            // Execute cURL request
            $response = curl_exec($ch);

            // Check for cURL errors
            if (curl_errno($ch)) {
                $errorMessage = curl_error($ch);
                curl_close($ch);
                return json_encode([
                    'status' => 'error',
                    'message' => 'cURL error: ' . $errorMessage
                ]);
            }

            // Get HTTP status code
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // Validate HTTP response code
            if ($httpCode !== 200) {
                return json_encode([
                    'status' => 'error',
                    'message' => 'Unexpected response code: ' . $httpCode
                ]);
            }

            // Decode response
            $responseData = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return json_encode([
                    'status' => 'error',
                    'message' => 'Invalid JSON response: ' . json_last_error_msg()
                ]);
            }

            // Check for API errors in response
            if (isset($responseData['error'])) {
                return json_encode([
                    'status' => 'error',
                    'message' => $responseData['error']['message'] ?? 'Unknown error'
                ]);
            }

            // Return success response with content
            if (isset($responseData['choices'][0]['message']['content'])) {
                $content = $responseData['choices'][0]['message']['content'];
                if (!empty($content)) {
                    $content = nl2br($content);
                }
                return json_encode([
                    'status' => 'success',
                    'content' => $content
                ]);
            }

            return json_encode([
                'status' => 'error',
                'message' => 'No valid response content found.'
            ]);

        } catch (\Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }
}