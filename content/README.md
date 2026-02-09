# Content structure for `php artisan content:import-questions`

## Path rules

- **First folder** = **Subject** (e.g. `STATICGK`, `HISTORY`, `COMPUTERAWARENESS`).
- **Topic** = optional subfolder. Questions get that subject + topic in the question bank.

## Language

### Option 1: Language folder inside subject (recommended for mixed language content)

Put a **language code** folder directly under the subject. Then put topic folders (or JSON files) inside it:

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

If the second segment is **not** one of these codes, it is treated as a **topic** (backward compatible):

- `STATICGK/CAPITALS_CURRENCIES/1.json` → subject + topic, language from `--language` (default `hi`)

### Option 2: Default language (no language folder)

- `STATICGK/CAPITALS_CURRENCIES/1.json` → language = `--language` (default `hi`)
- `php artisan content:import-questions --language=en` → all files without a language folder use English

## Summary

| Path pattern | Subject | Topic | Language |
|-------------|---------|--------|----------|
| `SUBJECT/file.json` | ✓ | — | `--language` |
| `SUBJECT/TOPIC/file.json` | ✓ | ✓ | `--language` |
| `SUBJECT/LANG/file.json` | ✓ | — | from folder |
| `SUBJECT/LANG/TOPIC/file.json` | ✓ | ✓ | from folder |

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
