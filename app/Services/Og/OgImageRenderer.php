<?php

declare(strict_types=1);

namespace App\Services\Og;

use Imagick;
use ImagickDraw;
use ImagickPixel;

/**
 * Renders a 1200×630 Open Graph share card with Imagick: a navy field, the
 * object's own dominant colour as a glowing disc, an elegant serif title, a
 * sans subtitle and the site kicker. Pure rendering — no I/O, no model access.
 */
final class OgImageRenderer
{
    private const WIDTH = 1200;

    private const HEIGHT = 630;

    private const BG = '#0a0e1a';

    private const INK = '#e8eaf0';

    private const MUTED = '#9aa3b8';

    private const AMBER = '#e0b872';

    /**
     * @return string PNG binary
     */
    public function render(string $title, ?string $subtitle = null, ?string $discColour = null): string
    {
        $colour = $this->safeHex($discColour) ?? self::AMBER;
        $serif = (string) config('og.fonts.serif');
        $sans = (string) config('og.fonts.sans');

        $img = new Imagick;
        $img->newImage(self::WIDTH, self::HEIGHT, new ImagickPixel(self::BG));
        $img->setImageFormat('png');

        $this->drawDisc($img, $colour);

        // Title — serif, auto-fitted so long names/designations stay on canvas.
        $titleDraw = new ImagickDraw;
        $titleDraw->setFont($serif);
        $titleDraw->setFillColor(new ImagickPixel(self::INK));
        $titleDraw->setFontSize($this->fitFontSize($img, $serif, $title, 150, 620, 56));
        $img->annotateImage($titleDraw, 90, 330, 0, $title);

        if ($subtitle !== null && $subtitle !== '') {
            $sub = new ImagickDraw;
            $sub->setFont($sans);
            $sub->setFillColor(new ImagickPixel(self::MUTED));
            $sub->setFontSize(40);
            $img->annotateImage($sub, 94, 400, 0, $this->truncate($img, $sans, 40, $subtitle, 600));
        }

        $rule = new ImagickDraw;
        $rule->setStrokeColor(new ImagickPixel(self::AMBER));
        $rule->setStrokeWidth(3);
        $rule->line(94, 460, 300, 460);
        $img->drawImage($rule);

        $kicker = new ImagickDraw;
        $kicker->setFont($sans);
        $kicker->setFillColor(new ImagickPixel(self::AMBER));
        $kicker->setFontSize(30);
        $img->annotateImage($kicker, 94, 540, 0, config('site.name').' · '.config('site.tagline'));

        $png = $img->getImageBlob();
        $img->clear();

        return $png;
    }

    private function drawDisc(Imagick $img, string $colour): void
    {
        $glow = new Imagick;
        $glow->newPseudoImage(760, 760, "radial-gradient:{$colour}-".self::BG);
        $glow->setImageMatte(true);
        $glow->evaluateImage(Imagick::EVALUATE_MULTIPLY, 0.5, Imagick::CHANNEL_ALPHA);
        $img->compositeImage($glow, Imagick::COMPOSITE_SCREEN, 720, -150);
        $glow->clear();

        $disc = new ImagickDraw;
        $disc->setFillColor(new ImagickPixel($colour));
        $disc->circle(1010, 250, 1010, 355);
        $img->drawImage($disc);
    }

    /** Shrink the font size until the text fits within $maxWidth. */
    private function fitFontSize(Imagick $img, string $font, string $text, int $start, int $maxWidth, int $min): int
    {
        $size = $start;
        $draw = new ImagickDraw;
        $draw->setFont($font);

        while ($size > $min) {
            $draw->setFontSize($size);
            if ($img->queryFontMetrics($draw, $text)['textWidth'] <= $maxWidth) {
                break;
            }
            $size -= 6;
        }

        return $size;
    }

    private function truncate(Imagick $img, string $font, int $size, string $text, int $maxWidth): string
    {
        $draw = new ImagickDraw;
        $draw->setFont($font);
        $draw->setFontSize($size);

        if ($img->queryFontMetrics($draw, $text)['textWidth'] <= $maxWidth) {
            return $text;
        }

        while (mb_strlen($text) > 1) {
            $text = mb_substr($text, 0, -1);
            if ($img->queryFontMetrics($draw, $text.'…')['textWidth'] <= $maxWidth) {
                break;
            }
        }

        return rtrim($text).'…';
    }

    private function safeHex(?string $hex): ?string
    {
        return ($hex !== null && preg_match('/^#[0-9a-fA-F]{6}$/', $hex)) ? $hex : null;
    }
}
