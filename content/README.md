# Content structure for `php artisan content:import-questions`

## Path rules (standard structure)

All subjects follow this structure:

```
SUBJECT/
  en/
    TOPIC/
      [1.json, 2.json, ...]
  hi/
    TOPIC/
      [1.json, 2.json, ...]
```

- **First folder** = **Subject** (e.g. `STATICGK`, `HISTORY`, `COMPUTERAWARENESS`).
- **Language folder** (`en`, `hi`) = language of the questions.
- **Topic folder** = optional. Questions get that subject + topic in the question bank.

## Language

### Standard: `subject/lang/topic/files`

Put a **language code** folder (`en` or `hi`) directly under the subject. Then put topic folders (or JSON files) inside it:

```
STATICGK/
  en/
    CAPITALS_CURRENCIES/
      1.json
    NATIONAL_SYMBOLS/
      1.json
  hi/
    CAPITALS_CURRENCIES/
      1.json
```

- `STATICGK/en/CAPITALS_CURRENCIES/1.json` → Subject: Static GK, Topic: Capitals Currencies, **Language: en**
- `STATICGK/hi/CAPITALS_CURRENCIES/1.json` → Subject: Static GK, Topic: Capitals Currencies, **Language: hi**

**Recognized language codes** (folder name, case-insensitive):  
`en`, `hi`, `mr`, `ta`, `te`, `bn`, `gu`, `kn`, `ml`, `pa`, `ur`

## Summary

| Path pattern | Subject | Topic | Language |
|-------------|---------|--------|----------|
| `SUBJECT/LANG/TOPIC/file.json` | ✓ | ✓ | from folder |

## Current subjects (all use subject/en/topic/files and subject/hi/topic/files)

| Subject | Topics |
|---------|--------|
| BANKINGAWARENESS | GENERAL |
| COMPUTERAWARENESS | COMPUTER_AWARENESS |
| CURRENTAFFAIRS | CURRENT_AFFAIRS |
| DISASTERMANAGEMENT | GENERAL |
| ENGLISH | GENERAL |
| ENVIRONMENT | ENVIRONMENT |
| GENERALSCIENCE | PHYSICS |
| GEOGRAPHY | GEOGRAPHY |
| HINDI | GENERAL |
| HISTORY | ANCIENT, MEDIEVAL, MODERN, INDIAN_ART_CULTURE, WORLD_HISTORY |
| INDIANECONOMY | INDIAN_ECONOMY |
| INDIANPOLITY | INDIAN_POLITY |
| INDIANSOCIETY | GENERAL |
| INTERNALSECURITY | GENERAL |
| INTERNATIONALRELATIONS | GENERAL |
| QUANTITATIVEAPTITUDE | GENERAL |
| REASONING | GENERAL |
| SCIENCETECHNOLOGY | GENERAL |
| STATICGK | BOOKS_AUTHORS, CAPITALS_CURRENCIES, FAMOUS_PERSONALITIES, IMPORTANT_DAYS, INTERNATIONAL_ORGANISATIONS, MONUMENTS_HERITAGE, NATIONAL_SYMBOLS |

## Quiz “Add with JSON” (creator UI)

Questions added via the creator’s “Add with JSON” use the **quiz’s language** for new questions. You can still set optional `"subject"` and `"topic"` in each item to tag them in the question bank.

## JSON format per file

Each JSON file is an **array of question objects**. Each object:

- `prompt` (string) – question text  
- `answers` (array of strings) – 4 options  
- `correct` (0-based index) – which option is correct  
- `explanation` (optional string)

Example:

```json
[
  {
    "prompt": "What is the capital of Japan?",
    "answers": ["Tokyo", "Kyoto", "Osaka", "Nagoya"],
    "correct": 0,
    "explanation": "Tokyo is the capital and largest city of Japan."
  }
]
```
