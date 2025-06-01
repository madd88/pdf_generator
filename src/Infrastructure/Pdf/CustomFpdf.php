<?php
namespace App\Infrastructure\Pdf;

use Fpdf\FPDF;

class CustomFpdf extends FPDF
{
    public function DashedRect(float $x, float $y, float $w, float $h, float $step = 3): void
    {
        $this->SetDrawColor(0); // Черный цвет
        $this->SetLineWidth(0.2); // Ширина линии

        $max = $x + $w;
        $maxY = $y + $h;

        // Верхняя линия
        for ($i = $x; $i <= $max; $i += $step * 2) {
            $this->Line($i, $y, min($i + $step, $max), $y);
        }

        // Правая линия
        for ($i = $y; $i <= $maxY; $i += $step * 2) {
            $this->Line($max, $i, $max, min($i + $step, $maxY));
        }

        // Нижняя линия
        for ($i = $max; $i >= $x; $i -= $step * 2) {
            $this->Line($i, $maxY, max($i - $step, $x), $maxY);
        }

        // Левая линия
        for ($i = $maxY; $i >= $y; $i -= $step * 2) {
            $this->Line($x, $i, $x, max($i - $step, $y));
        }
    }
}