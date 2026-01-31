<?php

namespace App\Services\Content;

use fivefilters\Readability\Configuration;
use fivefilters\Readability\Readability;
use PhpOffice\PhpWord\IOFactory;
use Smalot\PdfParser\Parser;

class TextExtractors
{
    public static function fromUrl(string $url): string
    {
        $html = @file_get_contents($url);
        if ($html === false) {
            return '';
        }

        $readability = new Readability(new Configuration);
        $readability->parse($html);

        return (string) $readability->getExcerpt();
    }

    public static function fromPdf(string $path): string
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($path);
        return (string) $pdf->getText();
    }

    public static function fromDocx(string $path): string
    {
        $phpWord = IOFactory::load($path);

        $text = '';
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $chunk = $element->getText();
                    if (is_string($chunk)) {
                        $text .= $chunk . "\n";
                    }
                }
            }
        }

        return $text;
    }
}

