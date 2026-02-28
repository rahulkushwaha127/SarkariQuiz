# Quantitative Aptitude - Content Format Guide

This guide explains how to write questions in JSON format for easy, scalable content creation.

## Basic JSON Structure

```json
[
  {
    "question": "Your question text here",
    "answers": [
      { "title": "Option A", "is_correct": false },
      { "title": "Option B", "is_correct": true },
      { "title": "Option C", "is_correct": false },
      { "title": "Option D", "is_correct": false }
    ],
    "explanation": "Solution explanation here",
    "difficulty": "easy"
  }
]
```

---

## Question Types & Examples

### 1. Simple Text Question (Most Common - 80% of questions)

```json
{
  "question": "If a shopkeeper sells an article for Rs. 450 with 10% profit, what is the cost price?",
  "answers": [
    { "title": "Rs. 400", "is_correct": false },
    { "title": "Rs. 409", "is_correct": true },
    { "title": "Rs. 420", "is_correct": false },
    { "title": "Rs. 495", "is_correct": false }
  ],
  "explanation": "CP = SP / (1 + Profit%) = 450 / 1.10 = Rs. 409 (approx)",
  "difficulty": "easy"
}
```

---

### 2. Mathematical Formulas (Using LaTeX)

Use `$...$` for inline math and `$$...$$` for block math.

```json
{
  "question": "Simplify: $\\frac{\\sqrt{125}}{\\sqrt{5}}$",
  "answers": [
    { "title": "$5$", "is_correct": true },
    { "title": "$25$", "is_correct": false },
    { "title": "$\\sqrt{25}$", "is_correct": false },
    { "title": "$\\sqrt{5}$", "is_correct": false }
  ],
  "explanation": "$\\frac{\\sqrt{125}}{\\sqrt{5}} = \\sqrt{\\frac{125}{5}} = \\sqrt{25} = 5$",
  "difficulty": "easy"
}
```

**Common LaTeX Symbols:**
| Symbol | LaTeX | Result |
|--------|-------|--------|
| Fraction | `$\\frac{a}{b}$` | a/b |
| Square root | `$\\sqrt{x}$` | √x |
| Power | `$x^2$` | x² |
| Subscript | `$x_1$` | x₁ |
| Multiplication | `$\\times$` | × |
| Division | `$\\div$` | ÷ |
| Pi | `$\\pi$` | π |
| Percentage | `$25\\%$` | 25% |
| Greater/Less | `$>$, $<$, $\\geq$, $\\leq$` | >, <, ≥, ≤ |

---

### 3. Tables (For Data Interpretation)

Use HTML tables directly in the question text.

```json
{
  "question": "Study the table and answer:\n<table><tr><th>Year</th><th>Sales (in Cr)</th><th>Profit (in Cr)</th></tr><tr><td>2020</td><td>500</td><td>50</td></tr><tr><td>2021</td><td>600</td><td>72</td></tr><tr><td>2022</td><td>750</td><td>90</td></tr></table>\n\nWhat is the profit percentage in 2021?",
  "answers": [
    { "title": "10%", "is_correct": false },
    { "title": "12%", "is_correct": true },
    { "title": "15%", "is_correct": false },
    { "title": "8%", "is_correct": false }
  ],
  "explanation": "Profit % = (72/600) × 100 = 12%",
  "difficulty": "medium"
}
```

**Simple Table Template:**
```html
<table>
  <tr><th>Header1</th><th>Header2</th><th>Header3</th></tr>
  <tr><td>Row1Col1</td><td>Row1Col2</td><td>Row1Col3</td></tr>
  <tr><td>Row2Col1</td><td>Row2Col2</td><td>Row2Col3</td></tr>
</table>
```

---

### 4. Questions with Images (Geometry, Charts)

Only use images when absolutely necessary (complex diagrams).

```json
{
  "question": "In the given figure, find the value of angle x.",
  "image": "geometry/triangle_angle_1.png",
  "answers": [
    { "title": "30°", "is_correct": false },
    { "title": "45°", "is_correct": true },
    { "title": "60°", "is_correct": false },
    { "title": "90°", "is_correct": false }
  ],
  "explanation": "Using angle sum property...",
  "difficulty": "medium"
}
```

Place images in: `content/QUANTITATIVEAPTITUDE/images/`

---

### 5. Answer Options with Images

```json
{
  "question": "Which of the following represents a right angle triangle?",
  "answers": [
    { "title": "Figure A", "is_correct": false },
    { "title": "Figure B", "is_correct": true },
    { "title": "Figure C", "is_correct": false },
    { "title": "Figure D", "is_correct": false }
  ],
  "answer_images": [
    "options/triangle_a.png",
    "options/triangle_b.png",
    "options/triangle_c.png",
    "options/triangle_d.png"
  ],
  "difficulty": "easy"
}
```

---

## Difficulty Levels

| Level | Value | Use For |
|-------|-------|---------|
| Easy | `"easy"` or `0` | Basic concepts, direct formulas |
| Medium | `"medium"` or `1` | 2-3 step problems |
| Hard | `"hard"` or `2` | Complex, multi-step problems |

---

## File Organization

```
content/QUANTITATIVEAPTITUDE/en/
├── Arithmetic/
│   ├── Number_System/
│   │   ├── 1.json          (10-20 questions)
│   │   ├── 2.json
│   │   └── ...
│   ├── Percentage/
│   │   ├── 1.json
│   │   └── ...
│   └── ...
├── Algebra/
│   ├── Linear_Equations/
│   │   └── 1.json
│   └── ...
└── images/
    ├── geometry/
    │   └── triangle_1.png
    └── di/
        └── bar_chart_1.png
```

---

## Quick Templates

### Percentage Question
```json
{
  "question": "A number is increased by 20% and then decreased by 20%. What is the net change?",
  "answers": [
    { "title": "No change", "is_correct": false },
    { "title": "4% decrease", "is_correct": true },
    { "title": "4% increase", "is_correct": false },
    { "title": "2% decrease", "is_correct": false }
  ],
  "explanation": "Net change = $-\\frac{20^2}{100}$ = -4% (decrease)",
  "difficulty": "easy"
}
```

### Ratio Question
```json
{
  "question": "If A:B = 2:3 and B:C = 4:5, then A:B:C = ?",
  "answers": [
    { "title": "2:3:5", "is_correct": false },
    { "title": "8:12:15", "is_correct": true },
    { "title": "4:6:5", "is_correct": false },
    { "title": "2:4:5", "is_correct": false }
  ],
  "explanation": "A:B = 2:3 = 8:12, B:C = 4:5 = 12:15. So A:B:C = 8:12:15",
  "difficulty": "medium"
}
```

### Time & Work Question
```json
{
  "question": "A can do a work in 10 days and B in 15 days. In how many days can they complete it together?",
  "answers": [
    { "title": "5 days", "is_correct": false },
    { "title": "6 days", "is_correct": true },
    { "title": "7 days", "is_correct": false },
    { "title": "8 days", "is_correct": false }
  ],
  "explanation": "Combined = $\\frac{1}{10} + \\frac{1}{15} = \\frac{5}{30} = \\frac{1}{6}$. So 6 days.",
  "difficulty": "easy"
}
```

---

## Import Command

After creating JSON files, run:

```bash
php artisan content:import-questions --path=content --language=en
```

Options:
- `--dry-run` : Preview without inserting
- `--language=hi` : Set language (en, hi, etc.)
- `--flush-subject=quantitative-aptitude` : Clear existing questions first
