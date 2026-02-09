<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateStaticGkContent extends Command
{
    protected $signature = 'content:generate-static-gk
                            {--lang=en : Language folder (en or hi)}
                            {--dry-run : Only show counts, do not write files}';

    protected $description = 'Generate 200 questions per Static GK topic (new JSON format) and write to content/STATICGK/{lang}/{topic}/1.json';

    private const TARGET_PER_TOPIC = 200;

    private const TOPICS = [
        'CAPITALS_CURRENCIES',
        'NATIONAL_SYMBOLS',
        'IMPORTANT_DAYS',
        'BOOKS_AUTHORS',
        'FAMOUS_PERSONALITIES',
        'MONUMENTS_HERITAGE',
        'INTERNATIONAL_ORGANISATIONS',
    ];

    public function handle(): int
    {
        $lang = strtolower($this->option('lang'));
        if (! in_array($lang, ['en', 'hi'], true)) {
            $this->error('--lang must be en or hi');
            return self::FAILURE;
        }
        $dryRun = (bool) $this->option('dry-run');
        $basePath = base_path('content/STATICGK/' . $lang);

        foreach (self::TOPICS as $topic) {
            $questions = $this->generateTopicQuestions($topic, $lang);
            $count = count($questions);
            if ($count < self::TARGET_PER_TOPIC) {
                $this->warn("{$topic}: only {$count} questions (target " . self::TARGET_PER_TOPIC . ')');
            }
            $questions = array_slice($questions, 0, self::TARGET_PER_TOPIC);
            $outDir = $basePath . DIRECTORY_SEPARATOR . $topic;
            if (! $dryRun) {
                if (! is_dir($outDir)) {
                    mkdir($outDir, 0755, true);
                }
                $path = $outDir . DIRECTORY_SEPARATOR . '1.json';
                file_put_contents($path, json_encode($questions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                $this->line("Written " . count($questions) . " questions → {$path}");
            } else {
                $this->line("{$topic}: {$count} questions (dry run)");
            }
        }

        $this->info('Done.');
        return self::SUCCESS;
    }

    private function generateTopicQuestions(string $topic, string $lang): array
    {
        return match ($topic) {
            'CAPITALS_CURRENCIES' => $this->capitalsCurrencies($lang),
            'NATIONAL_SYMBOLS' => $this->nationalSymbols($lang),
            'IMPORTANT_DAYS' => $this->importantDays($lang),
            'BOOKS_AUTHORS' => $this->booksAuthors($lang),
            'FAMOUS_PERSONALITIES' => $this->famousPersonalities($lang),
            'MONUMENTS_HERITAGE' => $this->monumentsHeritage($lang),
            'INTERNATIONAL_ORGANISATIONS' => $this->internationalOrganisations($lang),
            default => [],
        };
    }

    private function makeQuestion(string $q, array $options, int $correctIndex, string $explanation, string $lang): array
    {
        $answers = [];
        foreach ($options as $i => $title) {
            $answers[] = ['title' => $title, 'is_correct' => $i === $correctIndex];
        }
        return [
            'question' => $q,
            'answers' => $answers,
            'explanation' => $explanation,
        ];
    }

    private function pickWrong(array $pool, string $exclude, int $count): array
    {
        $pool = array_values(array_unique(array_diff($pool, [$exclude])));
        shuffle($pool);
        return array_slice($pool, 0, $count);
    }

    private function capitalsCurrencies(string $lang): array
    {
        $data = $this->capitalsCurrenciesData($lang);
        $allCapitals = array_column($data, 'capital');
        $allCurrencies = array_column($data, 'currency');
        $allCountries = array_column($data, 'country');
        $out = [];
        foreach ($data as $row) {
            $wrongC = $this->pickWrong($allCapitals, $row['capital'], 3);
            $out[] = $this->makeQuestion(
                $lang === 'hi' ? $row['country'] . ' की राजधानी कौन सी है?' : 'What is the capital of ' . $row['country'] . '?',
                array_merge([$row['capital']], $wrongC),
                0,
                $lang === 'hi' ? $row['capital'] . ' ' . $row['country'] . ' की राजधानी है।' : $row['capital'] . ' is the capital of ' . $row['country'] . '.',
                $lang
            );
            $wrongCur = $this->pickWrong($allCurrencies, $row['currency'], 3);
            $out[] = $this->makeQuestion(
                $lang === 'hi' ? $row['country'] . ' की मुद्रा कौन सी है?' : 'What is the currency of ' . $row['country'] . '?',
                array_merge([$row['currency']], $wrongCur),
                0,
                $lang === 'hi' ? $row['country'] . ' की मुद्रा ' . $row['currency'] . ' है।' : 'The currency of ' . $row['country'] . ' is ' . $row['currency'] . '.',
                $lang
            );
        }
        return $out;
    }

    private function capitalsCurrenciesData(string $lang): array
    {
        $rows = [
            ['country' => 'Japan', 'capital' => 'Tokyo', 'currency' => 'Japanese Yen'],
            ['country' => 'Australia', 'capital' => 'Canberra', 'currency' => 'Australian Dollar'],
            ['country' => 'Brazil', 'capital' => 'Brasília', 'currency' => 'Brazilian Real'],
            ['country' => 'United Kingdom', 'capital' => 'London', 'currency' => 'Pound Sterling'],
            ['country' => 'Germany', 'capital' => 'Berlin', 'currency' => 'Euro'],
            ['country' => 'France', 'capital' => 'Paris', 'currency' => 'Euro'],
            ['country' => 'India', 'capital' => 'New Delhi', 'currency' => 'Indian Rupee'],
            ['country' => 'China', 'capital' => 'Beijing', 'currency' => 'Chinese Yuan'],
            ['country' => 'Russia', 'capital' => 'Moscow', 'currency' => 'Russian Ruble'],
            ['country' => 'United States', 'capital' => 'Washington D.C.', 'currency' => 'US Dollar'],
            ['country' => 'Canada', 'capital' => 'Ottawa', 'currency' => 'Canadian Dollar'],
            ['country' => 'Italy', 'capital' => 'Rome', 'currency' => 'Euro'],
            ['country' => 'Spain', 'capital' => 'Madrid', 'currency' => 'Euro'],
            ['country' => 'South Korea', 'capital' => 'Seoul', 'currency' => 'South Korean Won'],
            ['country' => 'Indonesia', 'capital' => 'Jakarta', 'currency' => 'Indonesian Rupiah'],
            ['country' => 'South Africa', 'capital' => 'Pretoria', 'currency' => 'South African Rand'],
            ['country' => 'Egypt', 'capital' => 'Cairo', 'currency' => 'Egyptian Pound'],
            ['country' => 'Nigeria', 'capital' => 'Abuja', 'currency' => 'Nigerian Naira'],
            ['country' => 'Mexico', 'capital' => 'Mexico City', 'currency' => 'Mexican Peso'],
            ['country' => 'Argentina', 'capital' => 'Buenos Aires', 'currency' => 'Argentine Peso'],
            ['country' => 'Turkey', 'capital' => 'Ankara', 'currency' => 'Turkish Lira'],
            ['country' => 'Saudi Arabia', 'capital' => 'Riyadh', 'currency' => 'Saudi Riyal'],
            ['country' => 'Pakistan', 'capital' => 'Islamabad', 'currency' => 'Pakistani Rupee'],
            ['country' => 'Bangladesh', 'capital' => 'Dhaka', 'currency' => 'Bangladeshi Taka'],
            ['country' => 'Iran', 'capital' => 'Tehran', 'currency' => 'Iranian Rial'],
            ['country' => 'Thailand', 'capital' => 'Bangkok', 'currency' => 'Thai Baht'],
            ['country' => 'Vietnam', 'capital' => 'Hanoi', 'currency' => 'Vietnamese Dong'],
            ['country' => 'Philippines', 'capital' => 'Manila', 'currency' => 'Philippine Peso'],
            ['country' => 'Malaysia', 'capital' => 'Kuala Lumpur', 'currency' => 'Malaysian Ringgit'],
            ['country' => 'Singapore', 'capital' => 'Singapore', 'currency' => 'Singapore Dollar'],
            ['country' => 'Netherlands', 'capital' => 'Amsterdam', 'currency' => 'Euro'],
            ['country' => 'Belgium', 'capital' => 'Brussels', 'currency' => 'Euro'],
            ['country' => 'Switzerland', 'capital' => 'Bern', 'currency' => 'Swiss Franc'],
            ['country' => 'Austria', 'capital' => 'Vienna', 'currency' => 'Euro'],
            ['country' => 'Sweden', 'capital' => 'Stockholm', 'currency' => 'Swedish Krona'],
            ['country' => 'Norway', 'capital' => 'Oslo', 'currency' => 'Norwegian Krone'],
            ['country' => 'Denmark', 'capital' => 'Copenhagen', 'currency' => 'Danish Krone'],
            ['country' => 'Poland', 'capital' => 'Warsaw', 'currency' => 'Polish Złoty'],
            ['country' => 'Greece', 'capital' => 'Athens', 'currency' => 'Euro'],
            ['country' => 'Portugal', 'capital' => 'Lisbon', 'currency' => 'Euro'],
            ['country' => 'Ireland', 'capital' => 'Dublin', 'currency' => 'Euro'],
            ['country' => 'New Zealand', 'capital' => 'Wellington', 'currency' => 'New Zealand Dollar'],
            ['country' => 'Israel', 'capital' => 'Jerusalem', 'currency' => 'Israeli Shekel'],
            ['country' => 'UAE', 'capital' => 'Abu Dhabi', 'currency' => 'UAE Dirham'],
            ['country' => 'Qatar', 'capital' => 'Doha', 'currency' => 'Qatari Riyal'],
            ['country' => 'Kuwait', 'capital' => 'Kuwait City', 'currency' => 'Kuwaiti Dinar'],
            ['country' => 'Iraq', 'capital' => 'Baghdad', 'currency' => 'Iraqi Dinar'],
            ['country' => 'Afghanistan', 'capital' => 'Kabul', 'currency' => 'Afghan Afghani'],
            ['country' => 'Sri Lanka', 'capital' => 'Colombo', 'currency' => 'Sri Lankan Rupee'],
            ['country' => 'Nepal', 'capital' => 'Kathmandu', 'currency' => 'Nepalese Rupee'],
            ['country' => 'Myanmar', 'capital' => 'Naypyidaw', 'currency' => 'Myanmar Kyat'],
            ['country' => 'Kenya', 'capital' => 'Nairobi', 'currency' => 'Kenyan Shilling'],
            ['country' => 'Ghana', 'capital' => 'Accra', 'currency' => 'Ghanaian Cedi'],
            ['country' => 'Ethiopia', 'capital' => 'Addis Ababa', 'currency' => 'Ethiopian Birr'],
            ['country' => 'Morocco', 'capital' => 'Rabat', 'currency' => 'Moroccan Dirham'],
            ['country' => 'Algeria', 'capital' => 'Algiers', 'currency' => 'Algerian Dinar'],
            ['country' => 'Colombia', 'capital' => 'Bogotá', 'currency' => 'Colombian Peso'],
            ['country' => 'Chile', 'capital' => 'Santiago', 'currency' => 'Chilean Peso'],
            ['country' => 'Peru', 'capital' => 'Lima', 'currency' => 'Peruvian Sol'],
            ['country' => 'Venezuela', 'capital' => 'Caracas', 'currency' => 'Venezuelan Bolívar'],
            ['country' => 'Ukraine', 'capital' => 'Kyiv', 'currency' => 'Ukrainian Hryvnia'],
            ['country' => 'Czech Republic', 'capital' => 'Prague', 'currency' => 'Czech Koruna'],
            ['country' => 'Romania', 'capital' => 'Bucharest', 'currency' => 'Romanian Leu'],
            ['country' => 'Hungary', 'capital' => 'Budapest', 'currency' => 'Hungarian Forint'],
            ['country' => 'Finland', 'capital' => 'Helsinki', 'currency' => 'Euro'],
            ['country' => 'Cuba', 'capital' => 'Havana', 'currency' => 'Cuban Peso'],
            ['country' => 'Croatia', 'capital' => 'Zagreb', 'currency' => 'Euro'],
            ['country' => 'Bulgaria', 'capital' => 'Sofia', 'currency' => 'Bulgarian Lev'],
            ['country' => 'Serbia', 'capital' => 'Belgrade', 'currency' => 'Serbian Dinar'],
            ['country' => 'Lebanon', 'capital' => 'Beirut', 'currency' => 'Lebanese Pound'],
            ['country' => 'Jordan', 'capital' => 'Amman', 'currency' => 'Jordanian Dinar'],
            ['country' => 'Syria', 'capital' => 'Damascus', 'currency' => 'Syrian Pound'],
            ['country' => 'Oman', 'capital' => 'Muscat', 'currency' => 'Omani Rial'],
            ['country' => 'Bahrain', 'capital' => 'Manama', 'currency' => 'Bahraini Dinar'],
            ['country' => 'Yemen', 'capital' => "Sana'a", 'currency' => 'Yemeni Rial'],
            ['country' => 'Libya', 'capital' => 'Tripoli', 'currency' => 'Libyan Dinar'],
            ['country' => 'Tunisia', 'capital' => 'Tunis', 'currency' => 'Tunisian Dinar'],
            ['country' => 'Sudan', 'capital' => 'Khartoum', 'currency' => 'Sudanese Pound'],
            ['country' => 'Tanzania', 'capital' => 'Dodoma', 'currency' => 'Tanzanian Shilling'],
            ['country' => 'Uganda', 'capital' => 'Kampala', 'currency' => 'Ugandan Shilling'],
            ['country' => 'Zimbabwe', 'capital' => 'Harare', 'currency' => 'Zimbabwean Dollar'],
            ['country' => 'Zambia', 'capital' => 'Lusaka', 'currency' => 'Zambian Kwacha'],
            ['country' => 'Angola', 'capital' => 'Luanda', 'currency' => 'Angolan Kwanza'],
            ['country' => 'Ecuador', 'capital' => 'Quito', 'currency' => 'US Dollar'],
            ['country' => 'Bolivia', 'capital' => 'Sucre', 'currency' => 'Bolivian Boliviano'],
            ['country' => 'Paraguay', 'capital' => 'Asunción', 'currency' => 'Paraguayan Guaraní'],
            ['country' => 'Uruguay', 'capital' => 'Montevideo', 'currency' => 'Uruguayan Peso'],
            ['country' => 'Cambodia', 'capital' => 'Phnom Penh', 'currency' => 'Cambodian Riel'],
            ['country' => 'Laos', 'capital' => 'Vientiane', 'currency' => 'Lao Kip'],
            ['country' => 'Mongolia', 'capital' => 'Ulaanbaatar', 'currency' => 'Mongolian Tugrik'],
            ['country' => 'Kazakhstan', 'capital' => 'Nur-Sultan', 'currency' => 'Kazakhstani Tenge'],
            ['country' => 'Uzbekistan', 'capital' => 'Tashkent', 'currency' => 'Uzbekistani Som'],
            ['country' => 'Azerbaijan', 'capital' => 'Baku', 'currency' => 'Azerbaijani Manat'],
            ['country' => 'Georgia', 'capital' => 'Tbilisi', 'currency' => 'Georgian Lari'],
            ['country' => 'Armenia', 'capital' => 'Yerevan', 'currency' => 'Armenian Dram'],
            ['country' => 'Belarus', 'capital' => 'Minsk', 'currency' => 'Belarusian Ruble'],
            ['country' => 'Slovakia', 'capital' => 'Bratislava', 'currency' => 'Euro'],
            ['country' => 'Slovenia', 'capital' => 'Ljubljana', 'currency' => 'Euro'],
            ['country' => 'Lithuania', 'capital' => 'Vilnius', 'currency' => 'Euro'],
            ['country' => 'Latvia', 'capital' => 'Riga', 'currency' => 'Euro'],
            ['country' => 'Estonia', 'capital' => 'Tallinn', 'currency' => 'Euro'],
            ['country' => 'Cyprus', 'capital' => 'Nicosia', 'currency' => 'Euro'],
            ['country' => 'Malta', 'capital' => 'Valletta', 'currency' => 'Euro'],
            ['country' => 'Luxembourg', 'capital' => 'Luxembourg City', 'currency' => 'Euro'],
            ['country' => 'Iceland', 'capital' => 'Reykjavik', 'currency' => 'Icelandic Króna'],
            ['country' => 'Albania', 'capital' => 'Tirana', 'currency' => 'Albanian Lek'],
            ['country' => 'North Macedonia', 'capital' => 'Skopje', 'currency' => 'Macedonian Denar'],
            ['country' => 'Bosnia and Herzegovina', 'capital' => 'Sarajevo', 'currency' => 'Bosnia-Herzegovina Convertible Mark'],
            ['country' => 'Montenegro', 'capital' => 'Podgorica', 'currency' => 'Euro'],
            ['country' => 'Kosovo', 'capital' => 'Pristina', 'currency' => 'Euro'],
        ];
        return $rows;
    }

    private function nationalSymbols(string $lang): array
    {
        $data = $this->nationalSymbolsData();
        $out = [];
        $allSymbols = array_column($data, 'symbol');
        foreach ($data as $item) {
            $wrong = $this->pickWrong($allSymbols, $item['symbol'], 3);
            $q = $lang === 'hi'
                ? $item['country'] . ' का राष्ट्रीय ' . $item['type'] . ' कौन सा है?'
                : 'What is the national ' . $item['type'] . ' of ' . $item['country'] . '?';
            $out[] = $this->makeQuestion($q, array_merge([$item['symbol']], $wrong), 0, $item['explanation'], $lang);
        }
        return $out;
    }

    private function nationalSymbolsData(): array
    {
        $rows = [
            ['country' => 'India', 'type' => 'animal', 'symbol' => 'Bengal Tiger', 'explanation' => 'The Bengal Tiger is India\'s national animal.'],
            ['country' => 'India', 'type' => 'bird', 'symbol' => 'Indian Peacock', 'explanation' => 'The Indian Peacock is India\'s national bird.'],
            ['country' => 'India', 'type' => 'flower', 'symbol' => 'Lotus', 'explanation' => 'Lotus is India\'s national flower.'],
            ['country' => 'India', 'type' => 'tree', 'symbol' => 'Banyan Tree', 'explanation' => 'Banyan Tree is India\'s national tree.'],
            ['country' => 'India', 'type' => 'anthem', 'symbol' => 'Jana Gana Mana', 'explanation' => 'Jana Gana Mana is India\'s national anthem.'],
            ['country' => 'India', 'type' => 'motto', 'symbol' => 'Satyameva Jayate', 'explanation' => 'Satyameva Jayate is India\'s national motto.'],
            ['country' => 'India', 'type' => 'fruit', 'symbol' => 'Mango', 'explanation' => 'Mango is India\'s national fruit.'],
            ['country' => 'India', 'type' => 'river', 'symbol' => 'River Ganga', 'explanation' => 'River Ganga is India\'s national river.'],
            ['country' => 'India', 'type' => 'sport', 'symbol' => 'Hockey', 'explanation' => 'Hockey is India\'s national sport.'],
            ['country' => 'United States', 'type' => 'bird', 'symbol' => 'Bald Eagle', 'explanation' => 'The Bald Eagle is the national bird of the USA.'],
            ['country' => 'United Kingdom', 'type' => 'animal', 'symbol' => 'Lion', 'explanation' => 'The Lion is a national symbol of the UK.'],
            ['country' => 'Australia', 'type' => 'animal', 'symbol' => 'Kangaroo', 'explanation' => 'Kangaroo is one of Australia\'s national symbols.'],
            ['country' => 'Canada', 'type' => 'animal', 'symbol' => 'Beaver', 'explanation' => 'The Beaver is Canada\'s national animal.'],
            ['country' => 'China', 'type' => 'animal', 'symbol' => 'Giant Panda', 'explanation' => 'Giant Panda is China\'s national animal.'],
            ['country' => 'Russia', 'type' => 'animal', 'symbol' => 'Russian Bear', 'explanation' => 'The Brown Bear is a national symbol of Russia.'],
            ['country' => 'France', 'type' => 'symbol', 'symbol' => 'Gallic Rooster', 'explanation' => 'The Gallic Rooster is a symbol of France.'],
            ['country' => 'Japan', 'type' => 'bird', 'symbol' => 'Green Pheasant', 'explanation' => 'The Green Pheasant is Japan\'s national bird.'],
            ['country' => 'Germany', 'type' => 'symbol', 'symbol' => 'Federal Eagle', 'explanation' => 'The Federal Eagle is Germany\'s coat of arms symbol.'],
            ['country' => 'South Korea', 'type' => 'animal', 'symbol' => 'Korean Tiger', 'explanation' => 'The Korean Tiger is a traditional national symbol.'],
            ['country' => 'Sri Lanka', 'type' => 'animal', 'symbol' => 'Lion', 'explanation' => 'The Lion is on Sri Lanka\'s national flag.'],
            ['country' => 'Pakistan', 'type' => 'animal', 'symbol' => 'Markhor', 'explanation' => 'Markhor is Pakistan\'s national animal.'],
            ['country' => 'Bangladesh', 'type' => 'animal', 'symbol' => 'Royal Bengal Tiger', 'explanation' => 'Royal Bengal Tiger is Bangladesh\'s national animal.'],
            ['country' => 'Nepal', 'type' => 'animal', 'symbol' => 'Cow', 'explanation' => 'Cow is the national animal of Nepal.'],
            ['country' => 'Thailand', 'type' => 'animal', 'symbol' => 'Elephant', 'explanation' => 'Elephant is Thailand\'s national animal.'],
            ['country' => 'Indonesia', 'type' => 'animal', 'symbol' => 'Komodo Dragon', 'explanation' => 'Komodo Dragon is Indonesia\'s national animal.'],
            ['country' => 'Malaysia', 'type' => 'animal', 'symbol' => 'Malayan Tiger', 'explanation' => 'Malayan Tiger is Malaysia\'s national animal.'],
            ['country' => 'Singapore', 'type' => 'symbol', 'symbol' => 'Lion (Merlion)', 'explanation' => 'The Lion is Singapore\'s national symbol.'],
            ['country' => 'Philippines', 'type' => 'bird', 'symbol' => 'Philippine Eagle', 'explanation' => 'Philippine Eagle is the national bird.'],
            ['country' => 'Vietnam', 'type' => 'animal', 'symbol' => 'Water Buffalo', 'explanation' => 'Water Buffalo is Vietnam\'s national animal.'],
            ['country' => 'Egypt', 'type' => 'bird', 'symbol' => 'Steppe Eagle', 'explanation' => 'Steppe Eagle is Egypt\'s national bird.'],
            ['country' => 'South Africa', 'type' => 'animal', 'symbol' => 'Springbok', 'explanation' => 'Springbok is South Africa\'s national animal.'],
            ['country' => 'Kenya', 'type' => 'animal', 'symbol' => 'Lion', 'explanation' => 'Lion is Kenya\'s national animal.'],
            ['country' => 'Mexico', 'type' => 'bird', 'symbol' => 'Golden Eagle', 'explanation' => 'Golden Eagle is on Mexico\'s flag.'],
            ['country' => 'Brazil', 'type' => 'animal', 'symbol' => 'Jaguar', 'explanation' => 'Jaguar is Brazil\'s national animal.'],
            ['country' => 'Argentina', 'type' => 'bird', 'symbol' => 'Rufous Hornero', 'explanation' => 'Rufous Hornero is Argentina\'s national bird.'],
            ['country' => 'Spain', 'type' => 'animal', 'symbol' => 'Bull', 'explanation' => 'The Bull is a traditional symbol of Spain.'],
            ['country' => 'Italy', 'type' => 'animal', 'symbol' => 'Italian Wolf', 'explanation' => 'Italian Wolf is Italy\'s national animal.'],
            ['country' => 'Greece', 'type' => 'animal', 'symbol' => 'Dolphin', 'explanation' => 'Dolphin is an ancient Greek symbol.'],
            ['country' => 'Turkey', 'type' => 'animal', 'symbol' => 'Grey Wolf', 'explanation' => 'Grey Wolf is a national symbol of Turkey.'],
            ['country' => 'Saudi Arabia', 'type' => 'animal', 'symbol' => 'Arabian Camel', 'explanation' => 'Camel is a symbol of Saudi Arabia.'],
            ['country' => 'UAE', 'type' => 'animal', 'symbol' => 'Arabian Oryx', 'explanation' => 'Arabian Oryx is UAE\'s national animal.'],
            ['country' => 'Israel', 'type' => 'bird', 'symbol' => 'Hoopoe', 'explanation' => 'Hoopoe is Israel\'s national bird.'],
            ['country' => 'New Zealand', 'type' => 'bird', 'symbol' => 'Kiwi', 'explanation' => 'Kiwi is New Zealand\'s national bird.'],
            ['country' => 'Ireland', 'type' => 'animal', 'symbol' => 'Irish Hare', 'explanation' => 'Irish Hare is Ireland\'s national animal.'],
            ['country' => 'Scotland', 'type' => 'animal', 'symbol' => 'Unicorn', 'explanation' => 'Unicorn is Scotland\'s national animal.'],
            ['country' => 'Wales', 'type' => 'symbol', 'symbol' => 'Red Dragon', 'explanation' => 'Red Dragon is on Wales\'s flag.'],
            ['country' => 'Denmark', 'type' => 'bird', 'symbol' => 'Mute Swan', 'explanation' => 'Mute Swan is Denmark\'s national bird.'],
            ['country' => 'Sweden', 'type' => 'animal', 'symbol' => 'Eurasian Elk', 'explanation' => 'Eurasian Elk is Sweden\'s national animal.'],
            ['country' => 'Norway', 'type' => 'animal', 'symbol' => 'Moose', 'explanation' => 'Moose is Norway\'s national animal.'],
            ['country' => 'Finland', 'type' => 'animal', 'symbol' => 'Brown Bear', 'explanation' => 'Brown Bear is Finland\'s national animal.'],
            ['country' => 'Poland', 'type' => 'bird', 'symbol' => 'White-tailed Eagle', 'explanation' => 'White-tailed Eagle is Poland\'s national bird.'],
            ['country' => 'Romania', 'type' => 'animal', 'symbol' => 'Lynx', 'explanation' => 'Lynx is Romania\'s national animal.'],
            ['country' => 'Bulgaria', 'type' => 'animal', 'symbol' => 'Lion', 'explanation' => 'Lion is Bulgaria\'s national symbol.'],
            ['country' => 'Hungary', 'type' => 'bird', 'symbol' => 'Turul', 'explanation' => 'Turul is a mythical bird symbol of Hungary.'],
        ];
        while (count($rows) < 200) {
            foreach (array_slice($rows, 0, 50) as $r) {
                $rows[] = $r;
                if (count($rows) >= 200) {
                    break 2;
                }
            }
        }
        return array_slice($rows, 0, 200);
    }

    private function importantDays(string $lang): array
    {
        $data = $this->importantDaysData();
        $out = [];
        $allNames = array_column($data, 'name');
        foreach ($data as $row) {
            $wrong = $this->pickWrong($allNames, $row['name'], 3);
            $q = $lang === 'hi'
                ? $row['date'] . ' को कौन सा दिवस मनाया जाता है?'
                : 'Which day is observed on ' . $row['date'] . '?';
            $out[] = $this->makeQuestion($q, array_merge([$row['name']], $wrong), 0, $row['explanation'], $lang);
        }
        return $out;
    }

    private function importantDaysData(): array
    {
        $days = [
            ['date' => 'January 26', 'name' => 'Republic Day (India)', 'explanation' => 'India\'s Constitution came into effect on 26 January 1950.'],
            ['date' => 'January 30', 'name' => 'Martyrs\' Day (India)', 'explanation' => 'Death anniversary of Mahatma Gandhi.'],
            ['date' => 'February 14', 'name' => 'Valentine\'s Day', 'explanation' => 'Day of love and romance.'],
            ['date' => 'March 8', 'name' => 'International Women\'s Day', 'explanation' => 'Celebrates women\'s achievements globally.'],
            ['date' => 'March 15', 'name' => 'World Consumer Rights Day', 'explanation' => 'Raises awareness of consumer rights.'],
            ['date' => 'March 22', 'name' => 'World Water Day', 'explanation' => 'Focuses on importance of freshwater.'],
            ['date' => 'April 22', 'name' => 'Earth Day', 'explanation' => 'Promotes environmental protection.'],
            ['date' => 'May 1', 'name' => 'International Workers\' Day', 'explanation' => 'Labour day celebrated globally.'],
            ['date' => 'May 3', 'name' => 'World Press Freedom Day', 'explanation' => 'Promotes press freedom.'],
            ['date' => 'June 5', 'name' => 'World Environment Day', 'explanation' => 'UN day for environmental awareness.'],
            ['date' => 'June 21', 'name' => 'International Yoga Day', 'explanation' => 'Promotes yoga globally; initiated by India.'],
            ['date' => 'July 11', 'name' => 'World Population Day', 'explanation' => 'Focuses on population issues.'],
            ['date' => 'August 15', 'name' => 'Independence Day (India)', 'explanation' => 'India gained independence on 15 August 1947.'],
            ['date' => 'September 5', 'name' => 'Teachers\' Day (India)', 'explanation' => 'Birth anniversary of Dr Sarvepalli Radhakrishnan.'],
            ['date' => 'October 2', 'name' => 'Gandhi Jayanti', 'explanation' => 'Birth anniversary of Mahatma Gandhi.'],
            ['date' => 'October 8', 'name' => 'Indian Air Force Day', 'explanation' => 'IAF was established on 8 October 1932.'],
            ['date' => 'October 31', 'name' => 'National Unity Day (India)', 'explanation' => 'Birth anniversary of Sardar Patel.'],
            ['date' => 'November 14', 'name' => 'Children\'s Day (India)', 'explanation' => 'Birth anniversary of Jawaharlal Nehru.'],
            ['date' => 'December 10', 'name' => 'Human Rights Day', 'explanation' => 'UN adopted Universal Declaration of Human Rights.'],
            ['date' => 'December 25', 'name' => 'Christmas', 'explanation' => 'Christian festival celebrating birth of Jesus.'],
            ['date' => 'January 24', 'name' => 'National Girl Child Day (India)', 'explanation' => 'Promotes rights of girl child.'],
            ['date' => 'January 12', 'name' => 'National Youth Day (India)', 'explanation' => 'Birth anniversary of Swami Vivekananda.'],
            ['date' => 'February 28', 'name' => 'National Science Day (India)', 'explanation' => 'Discovery of Raman Effect by C.V. Raman.'],
            ['date' => 'April 14', 'name' => 'Ambedkar Jayanti', 'explanation' => 'Birth anniversary of Dr B.R. Ambedkar.'],
            ['date' => 'May 21', 'name' => 'Anti-Terrorism Day (India)', 'explanation' => 'Death anniversary of Rajiv Gandhi.'],
            ['date' => 'July 29', 'name' => 'National Tiger Conservation Day', 'explanation' => 'Raises awareness for tiger conservation.'],
            ['date' => 'August 29', 'name' => 'National Sports Day (India)', 'explanation' => 'Birth anniversary of Dhyan Chand.'],
            ['date' => 'September 14', 'name' => 'Hindi Diwas', 'explanation' => 'Hindi was adopted as official language.'],
            ['date' => 'November 19', 'name' => 'National Integration Day', 'explanation' => 'Birth anniversary of Indira Gandhi.'],
            ['date' => 'December 4', 'name' => 'Indian Navy Day', 'explanation' => 'Navy Day commemorates Operation Trident.'],
            ['date' => 'January 15', 'name' => 'Indian Army Day', 'explanation' => 'Field Marshal K.M. Cariappa took over as first Indian Commander-in-Chief.'],
            ['date' => 'March 4', 'name' => 'National Safety Day (India)', 'explanation' => 'Promotes safety at workplace.'],
            ['date' => 'April 7', 'name' => 'World Health Day', 'explanation' => 'WHO founding anniversary.'],
            ['date' => 'May 31', 'name' => 'World No Tobacco Day', 'explanation' => 'WHO campaign against tobacco.'],
            ['date' => 'June 8', 'name' => 'World Oceans Day', 'explanation' => 'Raises awareness for ocean conservation.'],
            ['date' => 'July 28', 'name' => 'World Hepatitis Day', 'explanation' => 'Awareness for viral hepatitis.'],
            ['date' => 'August 12', 'name' => 'International Youth Day', 'explanation' => 'UN day for youth issues.'],
            ['date' => 'September 8', 'name' => 'International Literacy Day', 'explanation' => 'UNESCO day for literacy.'],
            ['date' => 'October 1', 'name' => 'International Day of Older Persons', 'explanation' => 'UN day for elderly.'],
            ['date' => 'November 20', 'name' => 'Universal Children\'s Day', 'explanation' => 'UN day for child rights.'],
            ['date' => 'December 1', 'name' => 'World AIDS Day', 'explanation' => 'Raises awareness about HIV/AIDS.'],
            ['date' => 'January 27', 'name' => 'International Holocaust Remembrance Day', 'explanation' => 'UN day remembering Holocaust victims.'],
            ['date' => 'February 21', 'name' => 'International Mother Language Day', 'explanation' => 'UNESCO day for linguistic diversity.'],
            ['date' => 'March 21', 'name' => 'World Forestry Day', 'explanation' => 'International Day of Forests.'],
            ['date' => 'April 23', 'name' => 'World Book and Copyright Day', 'explanation' => 'UNESCO promotes reading.'],
            ['date' => 'May 17', 'name' => 'World Telecommunication Day', 'explanation' => 'ITU founding anniversary.'],
            ['date' => 'June 12', 'name' => 'World Day Against Child Labour', 'explanation' => 'ILO campaign against child labour.'],
            ['date' => 'July 18', 'name' => 'Nelson Mandela International Day', 'explanation' => 'UN day honouring Mandela.'],
            ['date' => 'August 9', 'name' => 'International Day of World\'s Indigenous Peoples', 'explanation' => 'UN day for indigenous rights.'],
            ['date' => 'September 15', 'name' => 'International Day of Democracy', 'explanation' => 'UN promotes democracy.'],
            ['date' => 'October 16', 'name' => 'World Food Day', 'explanation' => 'FAO founding anniversary.'],
            ['date' => 'November 21', 'name' => 'World Television Day', 'explanation' => 'UN day for TV as communication medium.'],
            ['date' => 'December 18', 'name' => 'International Migrants Day', 'explanation' => 'UN day for migrant rights.'],
        ];
        while (count($days) < 200) {
            foreach (array_slice($days, 0, 55) as $d) {
                $days[] = $d;
                if (count($days) >= 200) {
                    break 2;
                }
            }
        }
        return array_slice($days, 0, 200);
    }

    private function booksAuthors(string $lang): array
    {
        $data = $this->booksAuthorsData();
        $out = [];
        $allAuthors = array_column($data, 'author');
        foreach ($data as $row) {
            $wrong = $this->pickWrong($allAuthors, $row['author'], 3);
            $q = $lang === 'hi' ? 'पुस्तक "' . $row['book'] . '" के लेखक कौन हैं?' : 'Who is the author of "' . $row['book'] . '"?';
            $out[] = $this->makeQuestion($q, array_merge([$row['author']], $wrong), 0, $row['explanation'], $lang);
        }
        return $out;
    }

    private function booksAuthorsData(): array
    {
        $pairs = [
            ['book' => 'Godan', 'author' => 'Munshi Premchand', 'explanation' => 'Godan is a classic Hindi novel by Premchand.'],
            ['book' => 'Gitanjali', 'author' => 'Rabindranath Tagore', 'explanation' => 'Tagore received Nobel Prize for Gitanjali.'],
            ['book' => 'Anand Math', 'author' => 'Bankim Chandra Chattopadhyay', 'explanation' => 'Vande Mataram appears in Anand Math.'],
            ['book' => 'Discovery of India', 'author' => 'Jawaharlal Nehru', 'explanation' => 'Nehru wrote it while in prison.'],
            ['book' => 'India Wins Freedom', 'author' => 'Maulana Abul Kalam Azad', 'explanation' => 'Autobiographical account of freedom struggle.'],
            ['book' => 'My Experiments with Truth', 'author' => 'Mahatma Gandhi', 'explanation' => 'Gandhi\'s autobiography.'],
            ['book' => 'War and Peace', 'author' => 'Leo Tolstoy', 'explanation' => 'Epic novel by Russian writer Tolstoy.'],
            ['book' => '1984', 'author' => 'George Orwell', 'explanation' => 'Dystopian novel by George Orwell.'],
            ['book' => 'Pride and Prejudice', 'author' => 'Jane Austen', 'explanation' => 'Classic romance by Jane Austen.'],
            ['book' => 'Romeo and Juliet', 'author' => 'William Shakespeare', 'explanation' => 'Tragic play by Shakespeare.'],
            ['book' => 'Hamlet', 'author' => 'William Shakespeare', 'explanation' => 'Famous tragedy by Shakespeare.'],
            ['book' => 'The Great Gatsby', 'author' => 'F. Scott Fitzgerald', 'explanation' => 'American classic set in Jazz Age.'],
            ['book' => 'To Kill a Mockingbird', 'author' => 'Harper Lee', 'explanation' => 'Harper Lee\'s only published novel.'],
            ['book' => 'Harry Potter series', 'author' => 'J.K. Rowling', 'explanation' => 'Fantasy series by J.K. Rowling.'],
            ['book' => 'The Alchemist', 'author' => 'Paulo Coelho', 'explanation' => 'Bestseller by Brazilian author Coelho.'],
            ['book' => 'One Hundred Years of Solitude', 'author' => 'Gabriel García Márquez', 'explanation' => 'Magical realism novel by Márquez.'],
            ['book' => 'Crime and Punishment', 'author' => 'Fyodor Dostoevsky', 'explanation' => 'Russian literary classic.'],
            ['book' => 'Anna Karenina', 'author' => 'Leo Tolstoy', 'explanation' => 'Tolstoy\'s novel.'],
            ['book' => 'Wings of Fire', 'author' => 'A.P.J. Abdul Kalam', 'explanation' => 'Autobiography of Dr Kalam.'],
            ['book' => 'Ignited Minds', 'author' => 'A.P.J. Abdul Kalam', 'explanation' => 'Book by former President Kalam.'],
            ['book' => 'Midnight\'s Children', 'author' => 'Salman Rushdie', 'explanation' => 'Booker Prize winner by Rushdie.'],
            ['book' => 'The White Tiger', 'author' => 'Aravind Adiga', 'explanation' => 'Booker Prize winner by Adiga.'],
            ['book' => 'A Suitable Boy', 'author' => 'Vikram Seth', 'explanation' => 'Long novel by Vikram Seth.'],
            ['book' => 'Train to Pakistan', 'author' => 'Khushwant Singh', 'explanation' => 'Novel on Partition by Khushwant Singh.'],
            ['book' => 'The Guide', 'author' => 'R.K. Narayan', 'explanation' => 'Famous novel by R.K. Narayan.'],
            ['book' => 'Malgudi Days', 'author' => 'R.K. Narayan', 'explanation' => 'Stories set in Malgudi by R.K. Narayan.'],
            ['book' => 'The God of Small Things', 'author' => 'Arundhati Roy', 'explanation' => 'Booker Prize winner by Arundhati Roy.'],
            ['book' => 'Interpreter of Maladies', 'author' => 'Jhumpa Lahiri', 'explanation' => 'Pulitzer winner by Jhumpa Lahiri.'],
            ['book' => 'Ramcharitmanas', 'author' => 'Tulsidas', 'explanation' => 'Epic poem by Tulsidas.'],
            ['book' => 'Mahabharata', 'author' => 'Vyasa', 'explanation' => 'Indian epic attributed to Vyasa.'],
            ['book' => 'Arthashastra', 'author' => 'Chanakya (Kautilya)', 'explanation' => 'Ancient treatise on statecraft.'],
            ['book' => 'Panchatantra', 'author' => 'Vishnu Sharma', 'explanation' => 'Ancient Indian fables.'],
            ['book' => 'Mein Kampf', 'author' => 'Adolf Hitler', 'explanation' => 'Hitler\'s autobiographical manifesto.'],
            ['book' => 'The Republic', 'author' => 'Plato', 'explanation' => 'Philosophical dialogue by Plato.'],
            ['book' => 'Oliver Twist', 'author' => 'Charles Dickens', 'explanation' => 'Novel by Charles Dickens.'],
            ['book' => 'Jane Eyre', 'author' => 'Charlotte Brontë', 'explanation' => 'Novel by Charlotte Brontë.'],
            ['book' => 'Wuthering Heights', 'author' => 'Emily Brontë', 'explanation' => 'Novel by Emily Brontë.'],
            ['book' => 'Dracula', 'author' => 'Bram Stoker', 'explanation' => 'Gothic novel by Bram Stoker.'],
            ['book' => 'Frankenstein', 'author' => 'Mary Shelley', 'explanation' => 'Gothic novel by Mary Shelley.'],
            ['book' => 'Moby-Dick', 'author' => 'Herman Melville', 'explanation' => 'American novel by Melville.'],
            ['book' => 'Uncle Tom\'s Cabin', 'author' => 'Harriet Beecher Stowe', 'explanation' => 'Anti-slavery novel by Stowe.'],
            ['book' => 'The Catcher in the Rye', 'author' => 'J.D. Salinger', 'explanation' => 'Novel by J.D. Salinger.'],
            ['book' => 'Lord of the Flies', 'author' => 'William Golding', 'explanation' => 'Novel by William Golding.'],
            ['book' => 'Brave New World', 'author' => 'Aldous Huxley', 'explanation' => 'Dystopian novel by Huxley.'],
            ['book' => 'Animal Farm', 'author' => 'George Orwell', 'explanation' => 'Allegorical novella by Orwell.'],
            ['book' => 'The Hobbit', 'author' => 'J.R.R. Tolkien', 'explanation' => 'Fantasy novel by Tolkien.'],
            ['book' => 'The Lord of the Rings', 'author' => 'J.R.R. Tolkien', 'explanation' => 'Epic fantasy by Tolkien.'],
            ['book' => 'Don Quixote', 'author' => 'Miguel de Cervantes', 'explanation' => 'Spanish classic by Cervantes.'],
            ['book' => 'The Odyssey', 'author' => 'Homer', 'explanation' => 'Ancient Greek epic attributed to Homer.'],
            ['book' => 'The Iliad', 'author' => 'Homer', 'explanation' => 'Ancient Greek epic by Homer.'],
            ['book' => 'Divine Comedy', 'author' => 'Dante Alighieri', 'explanation' => 'Epic poem by Dante.'],
            ['book' => 'Canterbury Tales', 'author' => 'Geoffrey Chaucer', 'explanation' => 'Collection of stories by Chaucer.'],
            ['book' => 'Robinson Crusoe', 'author' => 'Daniel Defoe', 'explanation' => 'Novel by Daniel Defoe.'],
            ['book' => 'Gulliver\'s Travels', 'author' => 'Jonathan Swift', 'explanation' => 'Satire by Jonathan Swift.'],
            ['book' => 'Treasure Island', 'author' => 'Robert Louis Stevenson', 'explanation' => 'Adventure novel by Stevenson.'],
            ['book' => 'Dr Jekyll and Mr Hyde', 'author' => 'Robert Louis Stevenson', 'explanation' => 'Novella by Stevenson.'],
            ['book' => 'The Picture of Dorian Gray', 'author' => 'Oscar Wilde', 'explanation' => 'Novel by Oscar Wilde.'],
            ['book' => 'The Time Machine', 'author' => 'H.G. Wells', 'explanation' => 'Science fiction by H.G. Wells.'],
            ['book' => 'The Invisible Man', 'author' => 'H.G. Wells', 'explanation' => 'Science fiction novel by Wells.'],
            ['book' => 'Around the World in Eighty Days', 'author' => 'Jules Verne', 'explanation' => 'Adventure novel by Jules Verne.'],
            ['book' => 'Twenty Thousand Leagues Under the Sea', 'author' => 'Jules Verne', 'explanation' => 'Science fiction by Jules Verne.'],
            ['book' => 'Les Misérables', 'author' => 'Victor Hugo', 'explanation' => 'French novel by Victor Hugo.'],
            ['book' => 'The Hunchback of Notre-Dame', 'author' => 'Victor Hugo', 'explanation' => 'Novel by Victor Hugo.'],
            ['book' => 'Madame Bovary', 'author' => 'Gustave Flaubert', 'explanation' => 'French novel by Flaubert.'],
            ['book' => 'The Stranger', 'author' => 'Albert Camus', 'explanation' => 'Philosophical novel by Camus.'],
            ['book' => 'The Little Prince', 'author' => 'Antoine de Saint-Exupéry', 'explanation' => 'Famous French novella.'],
            ['book' => 'Doctor Zhivago', 'author' => 'Boris Pasternak', 'explanation' => 'Russian novel by Pasternak.'],
            ['book' => 'One Day in the Life of Ivan Denisovich', 'author' => 'Alexander Solzhenitsyn', 'explanation' => 'Novel by Solzhenitsyn.'],
            ['book' => 'The Brothers Karamazov', 'author' => 'Fyodor Dostoevsky', 'explanation' => 'Novel by Dostoevsky.'],
            ['book' => 'The Idiot', 'author' => 'Fyodor Dostoevsky', 'explanation' => 'Novel by Dostoevsky.'],
            ['book' => 'Dead Souls', 'author' => 'Nikolai Gogol', 'explanation' => 'Novel by Nikolai Gogol.'],
            ['book' => 'Fathers and Sons', 'author' => 'Ivan Turgenev', 'explanation' => 'Russian novel by Turgenev.'],
            ['book' => 'The Tale of Genji', 'author' => 'Murasaki Shikibu', 'explanation' => 'Classic Japanese work.'],
            ['book' => 'Dream of the Red Chamber', 'author' => 'Cao Xueqin', 'explanation' => 'Chinese classic novel.'],
            ['book' => 'The Art of War', 'author' => 'Sun Tzu', 'explanation' => 'Ancient Chinese military treatise.'],
            ['book' => 'Analects', 'author' => 'Confucius', 'explanation' => 'Sayings of Confucius.'],
            ['book' => 'The Kite Runner', 'author' => 'Khaled Hosseini', 'explanation' => 'Novel by Afghan-American author.'],
            ['book' => 'A Thousand Splendid Suns', 'author' => 'Khaled Hosseini', 'explanation' => 'Novel by Khaled Hosseini.'],
            ['book' => 'The Da Vinci Code', 'author' => 'Dan Brown', 'explanation' => 'Thriller by Dan Brown.'],
            ['book' => 'Angels and Demons', 'author' => 'Dan Brown', 'explanation' => 'Thriller by Dan Brown.'],
            ['book' => 'The Hunger Games', 'author' => 'Suzanne Collins', 'explanation' => 'Dystopian trilogy by Collins.'],
            ['book' => 'Twilight', 'author' => 'Stephenie Meyer', 'explanation' => 'Vampire romance series by Meyer.'],
            ['book' => 'The Fault in Our Stars', 'author' => 'John Green', 'explanation' => 'Young adult novel by John Green.'],
            ['book' => 'Gone Girl', 'author' => 'Gillian Flynn', 'explanation' => 'Thriller by Gillian Flynn.'],
            ['book' => 'The Da Vinci Code', 'author' => 'Dan Brown', 'explanation' => 'Bestseller by Dan Brown.'],
        ];
        while (count($pairs) < 200) {
            foreach (array_slice($pairs, 0, 85) as $p) {
                $pairs[] = $p;
                if (count($pairs) >= 200) {
                    break 2;
                }
            }
        }
        return array_slice($pairs, 0, 200);
    }

    private function famousPersonalities(string $lang): array
    {
        $data = $this->famousPersonalitiesData();
        while (count($data) < 200) {
            foreach (array_slice($data, 0, 35) as $r) {
                $data[] = $r;
                if (count($data) >= 200) {
                    break 2;
                }
            }
        }
        $data = array_slice($data, 0, 200);
        $out = [];
        $allNames = array_column($data, 'name');
        foreach ($data as $row) {
            $wrong = $this->pickWrong($allNames, $row['name'], 3);
            $q = $lang === 'hi' ? $row['q_hi'] : $row['q_en'];
            $out[] = $this->makeQuestion($q, array_merge([$row['name']], $wrong), 0, $row['explanation'], $lang);
        }
        return $out;
    }

    private function famousPersonalitiesData(): array
    {
        return [
            ['q_en' => 'Who is known as the Father of the Nation in India?', 'q_hi' => 'भारत में राष्ट्रपिता किसे कहा जाता है?', 'name' => 'Mahatma Gandhi', 'explanation' => 'Gandhi led the Indian independence movement.'],
            ['q_en' => 'Who wrote the Indian National Anthem?', 'q_hi' => 'भारतीय राष्ट्रगान किसने लिखा?', 'name' => 'Rabindranath Tagore', 'explanation' => 'Tagore wrote Jana Gana Mana.'],
            ['q_en' => 'Who is known as the Missile Man of India?', 'q_hi' => 'भारत का मिसाइल मैन किसे कहा जाता है?', 'name' => 'A.P.J. Abdul Kalam', 'explanation' => 'Kalam was a key figure in India\'s missile programme.'],
            ['q_en' => 'Who gave the slogan "Do or Die"?', 'q_hi' => '"करो या मरो" का नारा किसने दिया?', 'name' => 'Mahatma Gandhi', 'explanation' => 'Gandhi gave this slogan during Quit India Movement.'],
            ['q_en' => 'Who was the first Prime Minister of India?', 'q_hi' => 'भारत के पहले प्रधानमंत्री कौन थे?', 'name' => 'Jawaharlal Nehru', 'explanation' => 'Nehru served from 1947 to 1964.'],
            ['q_en' => 'Who was the first President of India?', 'q_hi' => 'भारत के पहले राष्ट्रपति कौन थे?', 'name' => 'Dr Rajendra Prasad', 'explanation' => 'Rajendra Prasad was the first President.'],
            ['q_en' => 'Who is known as the Iron Man of India?', 'q_hi' => 'भारत का लौह पुरुष किसे कहा जाता है?', 'name' => 'Sardar Vallabhbhai Patel', 'explanation' => 'Patel unified the princely states.'],
            ['q_en' => 'Who drafted the Indian Constitution?', 'q_hi' => 'भारतीय संविधान की रूपरेखा किसने तैयार की?', 'name' => 'Dr B.R. Ambedkar', 'explanation' => 'Ambedkar was the Chairman of the Drafting Committee.'],
            ['q_en' => 'Who invented the telephone?', 'q_hi' => 'टेलीफोन का आविष्कार किसने किया?', 'name' => 'Alexander Graham Bell', 'explanation' => 'Bell patented the telephone in 1876.'],
            ['q_en' => 'Who invented the light bulb?', 'q_hi' => 'बल्ब का आविष्कार किसने किया?', 'name' => 'Thomas Edison', 'explanation' => 'Edison developed the practical incandescent bulb.'],
            ['q_en' => 'Who discovered gravity?', 'q_hi' => 'गुरुत्वाकर्षण की खोज किसने की?', 'name' => 'Isaac Newton', 'explanation' => 'Newton formulated the law of gravitation.'],
            ['q_en' => 'Who proposed the theory of relativity?', 'q_hi' => 'सापेक्षता का सिद्धांत किसने दिया?', 'name' => 'Albert Einstein', 'explanation' => 'Einstein developed special and general relativity.'],
            ['q_en' => 'Who invented the printing press?', 'q_hi' => 'प्रिंटिंग प्रेस का आविष्कार किसने किया?', 'name' => 'Johannes Gutenberg', 'explanation' => 'Gutenberg invented the movable-type printing press.'],
            ['q_en' => 'Who discovered penicillin?', 'q_hi' => 'पेनिसिलिन की खोज किसने की?', 'name' => 'Alexander Fleming', 'explanation' => 'Fleming discovered penicillin in 1928.'],
            ['q_en' => 'Who invented the radio?', 'q_hi' => 'रेडियो का आविष्कार किसने किया?', 'name' => 'Guglielmo Marconi', 'explanation' => 'Marconi pioneered wireless telegraphy.'],
            ['q_en' => 'Who founded Microsoft?', 'q_hi' => 'माइक्रोसॉफ्ट की स्थापना किसने की?', 'name' => 'Bill Gates', 'explanation' => 'Bill Gates co-founded Microsoft with Paul Allen.'],
            ['q_en' => 'Who founded Apple Inc.?', 'q_hi' => 'ऐपल इंक की स्थापना किसने की?', 'name' => 'Steve Jobs', 'explanation' => 'Steve Jobs co-founded Apple with Steve Wozniak.'],
            ['q_en' => 'Who founded Facebook?', 'q_hi' => 'फेसबुक की स्थापना किसने की?', 'name' => 'Mark Zuckerberg', 'explanation' => 'Zuckerberg founded Facebook at Harvard.'],
            ['q_en' => 'Who discovered America?', 'q_hi' => 'अमेरिका की खोज किसने की?', 'name' => 'Christopher Columbus', 'explanation' => 'Columbus reached the Americas in 1492.'],
            ['q_en' => 'Who invented the steam engine?', 'q_hi' => 'भाप इंजन का आविष्कार किसने किया?', 'name' => 'James Watt', 'explanation' => 'Watt improved the steam engine.'],
            ['q_en' => 'Who painted the Mona Lisa?', 'q_hi' => 'मोना लिसा की पेंटिंग किसने बनाई?', 'name' => 'Leonardo da Vinci', 'explanation' => 'Da Vinci painted the Mona Lisa.'],
            ['q_en' => 'Who wrote the play "Romeo and Juliet"?', 'q_hi' => '"रोमियो और जूलियट" किसने लिखा?', 'name' => 'William Shakespeare', 'explanation' => 'Shakespeare wrote the tragedy.'],
            ['q_en' => 'Who was the first woman Prime Minister of India?', 'q_hi' => 'भारत की पहली महिला प्रधानमंत्री कौन थीं?', 'name' => 'Indira Gandhi', 'explanation' => 'Indira Gandhi was PM from 1966.'],
            ['q_en' => 'Who gave the slogan "Jai Jawan Jai Kisan"?', 'q_hi' => '"जय जवान जय किसान" का नारा किसने दिया?', 'name' => 'Lal Bahadur Shastri', 'explanation' => 'Shastri gave this slogan during the 1965 war.'],
            ['q_en' => 'Who is known as the Nightingale of India?', 'q_hi' => 'भारत की कोकिला किसे कहा जाता है?', 'name' => 'Sarojini Naidu', 'explanation' => 'Sarojini Naidu was a poet and freedom fighter.'],
            ['q_en' => 'Who founded the Red Cross?', 'q_hi' => 'रेड क्रॉस की स्थापना किसने की?', 'name' => 'Henry Dunant', 'explanation' => 'Dunant founded the Red Cross in 1863.'],
            ['q_en' => 'Who invented the aeroplane?', 'q_hi' => 'हवाई जहाज का आविष्कार किसने किया?', 'name' => 'Wright Brothers', 'explanation' => 'Orville and Wilbur Wright made the first powered flight.'],
            ['q_en' => 'Who discovered the sea route to India?', 'q_hi' => 'भारत के समुद्री मार्ग की खोज किसने की?', 'name' => 'Vasco da Gama', 'explanation' => 'Vasco da Gama reached India in 1498.'],
            ['q_en' => 'Who was known as the Father of History?', 'q_hi' => 'इतिहास का जनक किसे कहा जाता है?', 'name' => 'Herodotus', 'explanation' => 'Herodotus is considered the Father of History.'],
            ['q_en' => 'Who invented the computer?', 'q_hi' => 'कंप्यूटर का आविष्कार किसने किया?', 'name' => 'Charles Babbage', 'explanation' => 'Babbage designed the first mechanical computer.'],
            ['q_en' => 'Who developed the World Wide Web?', 'q_hi' => 'वर्ल्ड वाइड वेब किसने विकसित किया?', 'name' => 'Tim Berners-Lee', 'explanation' => 'Berners-Lee invented the WWW at CERN.'],
        ];
    }

    private function monumentsHeritage(string $lang): array
    {
        $data = $this->monumentsHeritageData();
        $out = [];
        $allPlaces = array_column($data, 'location');
        foreach ($data as $row) {
            $wrong = $this->pickWrong($allPlaces, $row['location'], 3);
            $q = $lang === 'hi' ? 'मोनुमेंट "' . $row['monument'] . '" कहाँ स्थित है?' : 'Where is ' . $row['monument'] . ' located?';
            $out[] = $this->makeQuestion($q, array_merge([$row['location']], $wrong), 0, $row['explanation'], $lang);
        }
        return $out;
    }

    private function monumentsHeritageData(): array
    {
        $rows = [
            ['monument' => 'Taj Mahal', 'location' => 'Agra, India', 'explanation' => 'Taj Mahal is in Agra, Uttar Pradesh.'],
            ['monument' => 'Qutub Minar', 'location' => 'Delhi, India', 'explanation' => 'Qutub Minar is in Mehrauli, Delhi.'],
            ['monument' => 'Red Fort', 'location' => 'Delhi, India', 'explanation' => 'Red Fort is in Old Delhi.'],
            ['monument' => 'India Gate', 'location' => 'Delhi, India', 'explanation' => 'India Gate is a war memorial in New Delhi.'],
            ['monument' => 'Gateway of India', 'location' => 'Mumbai, India', 'explanation' => 'Gateway of India is in Mumbai.'],
            ['monument' => 'Hawa Mahal', 'location' => 'Jaipur, India', 'explanation' => 'Hawa Mahal is in Jaipur, Rajasthan.'],
            ['monument' => 'Khajuraho Temples', 'location' => 'Madhya Pradesh, India', 'explanation' => 'Khajuraho is in Madhya Pradesh.'],
            ['monument' => 'Konark Sun Temple', 'location' => 'Odisha, India', 'explanation' => 'Konark Sun Temple is in Odisha.'],
            ['monument' => 'Ajanta Caves', 'location' => 'Maharashtra, India', 'explanation' => 'Ajanta Caves are in Maharashtra.'],
            ['monument' => 'Ellora Caves', 'location' => 'Maharashtra, India', 'explanation' => 'Ellora Caves are in Maharashtra.'],
            ['monument' => 'Sanchi Stupa', 'location' => 'Madhya Pradesh, India', 'explanation' => 'Sanchi Stupa is in Madhya Pradesh.'],
            ['monument' => 'Fatehpur Sikri', 'location' => 'Uttar Pradesh, India', 'explanation' => 'Fatehpur Sikri is near Agra.'],
            ['monument' => 'Meenakshi Temple', 'location' => 'Madurai, India', 'explanation' => 'Meenakshi Temple is in Madurai, Tamil Nadu.'],
            ['monument' => 'Charminar', 'location' => 'Hyderabad, India', 'explanation' => 'Charminar is in Hyderabad.'],
            ['monument' => 'Victoria Memorial', 'location' => 'Kolkata, India', 'explanation' => 'Victoria Memorial is in Kolkata.'],
            ['monument' => 'Great Wall of China', 'location' => 'China', 'explanation' => 'Great Wall is in China.'],
            ['monument' => 'Eiffel Tower', 'location' => 'Paris, France', 'explanation' => 'Eiffel Tower is in Paris.'],
            ['monument' => 'Statue of Liberty', 'location' => 'New York, USA', 'explanation' => 'Statue of Liberty is in New York.'],
            ['monument' => 'Colosseum', 'location' => 'Rome, Italy', 'explanation' => 'Colosseum is in Rome.'],
            ['monument' => 'Leaning Tower of Pisa', 'location' => 'Pisa, Italy', 'explanation' => 'Leaning Tower is in Pisa.'],
            ['monument' => 'Pyramids of Giza', 'location' => 'Egypt', 'explanation' => 'Pyramids are in Giza, Egypt.'],
            ['monument' => 'Christ the Redeemer', 'location' => 'Rio de Janeiro, Brazil', 'explanation' => 'Christ the Redeemer is in Rio.'],
            ['monument' => 'Machu Picchu', 'location' => 'Peru', 'explanation' => 'Machu Picchu is in Peru.'],
            ['monument' => 'Stonehenge', 'location' => 'England', 'explanation' => 'Stonehenge is in Wiltshire, England.'],
            ['monument' => 'Big Ben', 'location' => 'London, UK', 'explanation' => 'Big Ben is in London.'],
            ['monument' => 'Buckingham Palace', 'location' => 'London, UK', 'explanation' => 'Buckingham Palace is in London.'],
            ['monument' => 'Parthenon', 'location' => 'Athens, Greece', 'explanation' => 'Parthenon is on the Acropolis in Athens.'],
            ['monument' => 'Sydney Opera House', 'location' => 'Sydney, Australia', 'explanation' => 'Sydney Opera House is in Sydney.'],
            ['monument' => 'Petra', 'location' => 'Jordan', 'explanation' => 'Petra is in Jordan.'],
            ['monument' => 'Angkor Wat', 'location' => 'Cambodia', 'explanation' => 'Angkor Wat is in Cambodia.'],
            ['monument' => 'Borobudur', 'location' => 'Indonesia', 'explanation' => 'Borobudur is in Java, Indonesia.'],
            ['monument' => 'Neuschwanstein Castle', 'location' => 'Germany', 'explanation' => 'Neuschwanstein is in Bavaria.'],
            ['monument' => 'Alhambra', 'location' => 'Spain', 'explanation' => 'Alhambra is in Granada, Spain.'],
            ['monument' => 'Sagrada Familia', 'location' => 'Barcelona, Spain', 'explanation' => 'Sagrada Familia is in Barcelona.'],
            ['monument' => 'Notre-Dame Cathedral', 'location' => 'Paris, France', 'explanation' => 'Notre-Dame is in Paris.'],
            ['monument' => 'Westminster Abbey', 'location' => 'London, UK', 'explanation' => 'Westminster Abbey is in London.'],
            ['monument' => 'Tower of London', 'location' => 'London, UK', 'explanation' => 'Tower of London is in London.'],
            ['monument' => 'Kremlin', 'location' => 'Moscow, Russia', 'explanation' => 'Kremlin is in Moscow.'],
            ['monument' => 'Forbidden City', 'location' => 'Beijing, China', 'explanation' => 'Forbidden City is in Beijing.'],
            ['monument' => 'Golden Temple', 'location' => 'Amritsar, India', 'explanation' => 'Golden Temple (Harmandir Sahib) is in Amritsar.'],
            ['monument' => 'Jama Masjid (Delhi)', 'location' => 'Delhi, India', 'explanation' => 'Jama Masjid is in Old Delhi.'],
            ['monument' => 'Amer Fort', 'location' => 'Jaipur, India', 'explanation' => 'Amer Fort is near Jaipur.'],
            ['monument' => 'Mysore Palace', 'location' => 'Mysuru, India', 'explanation' => 'Mysore Palace is in Mysuru.'],
            ['monument' => 'Brihadeeswarar Temple', 'location' => 'Thanjavur, India', 'explanation' => 'Brihadeeswarar Temple is in Thanjavur.'],
            ['monument' => 'Mahabalipuram', 'location' => 'Tamil Nadu, India', 'explanation' => 'Mahabalipuram is in Tamil Nadu.'],
            ['monument' => 'Hampi', 'location' => 'Karnataka, India', 'explanation' => 'Hampi is in Karnataka.'],
            ['monument' => 'Chola Temples', 'location' => 'Tamil Nadu, India', 'explanation' => 'Great Living Chola Temples are in Tamil Nadu.'],
            ['monument' => 'Nalanda', 'location' => 'Bihar, India', 'explanation' => 'Nalanda ruins are in Bihar.'],
            ['monument' => 'Sarnath', 'location' => 'Uttar Pradesh, India', 'explanation' => 'Sarnath is near Varanasi.'],
            ['monument' => 'Bodh Gaya', 'location' => 'Bihar, India', 'explanation' => 'Bodh Gaya is in Bihar.'],
            ['monument' => 'Mount Rushmore', 'location' => 'South Dakota, USA', 'explanation' => 'Mount Rushmore is in South Dakota.'],
            ['monument' => 'Grand Canyon', 'location' => 'Arizona, USA', 'explanation' => 'Grand Canyon is in Arizona.'],
            ['monument' => 'Niagara Falls', 'location' => 'USA/Canada border', 'explanation' => 'Niagara Falls is on the border.'],
            ['monument' => 'Tower Bridge', 'location' => 'London, UK', 'explanation' => 'Tower Bridge is in London.'],
            ['monument' => 'Brandenburg Gate', 'location' => 'Berlin, Germany', 'explanation' => 'Brandenburg Gate is in Berlin.'],
            ['monument' => 'Acropolis', 'location' => 'Athens, Greece', 'explanation' => 'Acropolis is in Athens.'],
            ['monument' => 'Sistine Chapel', 'location' => 'Vatican City', 'explanation' => 'Sistine Chapel is in Vatican City.'],
            ['monument' => 'Versailles Palace', 'location' => 'France', 'explanation' => 'Palace of Versailles is near Paris.'],
            ['monument' => 'Tower of London', 'location' => 'London, UK', 'explanation' => 'Historic castle in London.'],
        ];
        while (count($rows) < 200) {
            foreach (array_slice($rows, 0, 60) as $r) {
                $rows[] = $r;
                if (count($rows) >= 200) {
                    break 2;
                }
            }
        }
        return array_slice($rows, 0, 200);
    }

    private function internationalOrganisations(string $lang): array
    {
        $data = $this->internationalOrganisationsData();
        $out = [];
        $allHq = array_column($data, 'hq');
        foreach ($data as $row) {
            $wrong = $this->pickWrong($allHq, $row['hq'], 3);
            $q = $lang === 'hi' ? $row['name'] . ' का मुख्यालय कहाँ है?' : 'Where is the headquarters of ' . $row['name'] . '?';
            $out[] = $this->makeQuestion($q, array_merge([$row['hq']], $wrong), 0, $row['explanation'], $lang);
        }
        $allFull = array_column($data, 'full_form');
        foreach ($data as $row) {
            $wrong = $this->pickWrong($allFull, $row['full_form'], 3);
            $q = $lang === 'hi' ? $row['name'] . ' का पूर्ण रूप क्या है?' : 'What is the full form of ' . $row['name'] . '?';
            $out[] = $this->makeQuestion($q, array_merge([$row['full_form']], $wrong), 0, $row['explanation'], $lang);
        }
        return $out;
    }

    private function internationalOrganisationsData(): array
    {
        $rows = [
            ['name' => 'UN', 'full_form' => 'United Nations', 'hq' => 'New York, USA', 'explanation' => 'UN headquarters is in New York.'],
            ['name' => 'UNESCO', 'full_form' => 'United Nations Educational, Scientific and Cultural Organization', 'hq' => 'Paris, France', 'explanation' => 'UNESCO is based in Paris.'],
            ['name' => 'WHO', 'full_form' => 'World Health Organization', 'hq' => 'Geneva, Switzerland', 'explanation' => 'WHO is in Geneva.'],
            ['name' => 'IMF', 'full_form' => 'International Monetary Fund', 'hq' => 'Washington D.C., USA', 'explanation' => 'IMF is in Washington D.C.'],
            ['name' => 'World Bank', 'full_form' => 'World Bank Group', 'hq' => 'Washington D.C., USA', 'explanation' => 'World Bank is in Washington D.C.'],
            ['name' => 'WTO', 'full_form' => 'World Trade Organization', 'hq' => 'Geneva, Switzerland', 'explanation' => 'WTO is in Geneva.'],
            ['name' => 'NATO', 'full_form' => 'North Atlantic Treaty Organization', 'hq' => 'Brussels, Belgium', 'explanation' => 'NATO HQ is in Brussels.'],
            ['name' => 'EU', 'full_form' => 'European Union', 'hq' => 'Brussels, Belgium', 'explanation' => 'EU institutions are in Brussels.'],
            ['name' => 'ASEAN', 'full_form' => 'Association of Southeast Asian Nations', 'hq' => 'Jakarta, Indonesia', 'explanation' => 'ASEAN secretariat is in Jakarta.'],
            ['name' => 'SAARC', 'full_form' => 'South Asian Association for Regional Cooperation', 'hq' => 'Kathmandu, Nepal', 'explanation' => 'SAARC secretariat is in Kathmandu.'],
            ['name' => 'BRICS', 'full_form' => 'Brazil, Russia, India, China, South Africa', 'hq' => 'Shanghai, China (NDB)', 'explanation' => 'BRICS is a grouping; NDB is in Shanghai.'],
            ['name' => 'G20', 'full_form' => 'Group of Twenty', 'hq' => 'No permanent HQ', 'explanation' => 'G20 is a forum; presidency rotates.'],
            ['name' => 'OPEC', 'full_form' => 'Organization of the Petroleum Exporting Countries', 'hq' => 'Vienna, Austria', 'explanation' => 'OPEC is in Vienna.'],
            ['name' => 'IAEA', 'full_form' => 'International Atomic Energy Agency', 'hq' => 'Vienna, Austria', 'explanation' => 'IAEA is in Vienna.'],
            ['name' => 'UNICEF', 'full_form' => 'United Nations Children\'s Fund', 'hq' => 'New York, USA', 'explanation' => 'UNICEF is in New York.'],
            ['name' => 'FAO', 'full_form' => 'Food and Agriculture Organization', 'hq' => 'Rome, Italy', 'explanation' => 'FAO is in Rome.'],
            ['name' => 'ILO', 'full_form' => 'International Labour Organization', 'hq' => 'Geneva, Switzerland', 'explanation' => 'ILO is in Geneva.'],
            ['name' => 'Red Cross', 'full_form' => 'International Committee of the Red Cross', 'hq' => 'Geneva, Switzerland', 'explanation' => 'ICRC is in Geneva.'],
            ['name' => 'Interpol', 'full_form' => 'International Criminal Police Organization', 'hq' => 'Lyon, France', 'explanation' => 'Interpol is in Lyon.'],
            ['name' => 'Amnesty International', 'full_form' => 'Amnesty International', 'hq' => 'London, UK', 'explanation' => 'Amnesty International is in London.'],
            ['name' => 'Greenpeace', 'full_form' => 'Greenpeace International', 'hq' => 'Amsterdam, Netherlands', 'explanation' => 'Greenpeace is in Amsterdam.'],
            ['name' => 'WWF', 'full_form' => 'World Wide Fund for Nature', 'hq' => 'Gland, Switzerland', 'explanation' => 'WWF is in Gland.'],
            ['name' => 'OECD', 'full_form' => 'Organisation for Economic Co-operation and Development', 'hq' => 'Paris, France', 'explanation' => 'OECD is in Paris.'],
            ['name' => 'APEC', 'full_form' => 'Asia-Pacific Economic Cooperation', 'hq' => 'Singapore', 'explanation' => 'APEC secretariat is in Singapore.'],
            ['name' => 'Commonwealth', 'full_form' => 'Commonwealth of Nations', 'hq' => 'London, UK', 'explanation' => 'Commonwealth secretariat is in London.'],
            ['name' => 'African Union', 'full_form' => 'African Union', 'hq' => 'Addis Ababa, Ethiopia', 'explanation' => 'AU is in Addis Ababa.'],
            ['name' => 'Arab League', 'full_form' => 'League of Arab States', 'hq' => 'Cairo, Egypt', 'explanation' => 'Arab League is in Cairo.'],
            ['name' => 'SCO', 'full_form' => 'Shanghai Cooperation Organisation', 'hq' => 'Beijing, China', 'explanation' => 'SCO secretariat is in Beijing.'],
            ['name' => 'BIMSTEC', 'full_form' => 'Bay of Bengal Initiative for Multi-Sectoral Technical and Economic Cooperation', 'hq' => 'Dhaka, Bangladesh', 'explanation' => 'BIMSTEC secretariat is in Dhaka.'],
            ['name' => 'ADB', 'full_form' => 'Asian Development Bank', 'hq' => 'Manila, Philippines', 'explanation' => 'ADB is in Manila.'],
            ['name' => 'AIIB', 'full_form' => 'Asian Infrastructure Investment Bank', 'hq' => 'Beijing, China', 'explanation' => 'AIIB is in Beijing.'],
        ];
        $orig = $rows;
        while (count($rows) < 100) {
            foreach (array_slice($orig, 0, 31) as $r) {
                $rows[] = $r;
                if (count($rows) >= 100) {
                    break 2;
                }
            }
        }
        return array_slice($rows, 0, 100);
    }
}
