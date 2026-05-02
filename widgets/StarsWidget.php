<?php

namespace app\widgets;

use yii\base\Widget;

/**
 * Виджет для отрисовки рейтинга в виде звёздочек.
 */
class StarsWidget extends Widget
{
    /** @var string 'small' или 'big' */
    public string $size = 'big';
    public float $rating = 0.0;

    public function run(): string
    {
        $filled = (int) round($this->rating);
        $htmlCode = '<div class="stars-rating ' . $this->size . '">';

        for ($i = 1; $i <= 5; $i++) {
            $class = $i <= $filled ? 'fill-star' : '';
            $htmlCode .= '<span class="' . $class . '">&nbsp;</span>';
        }

        $htmlCode .= '</div>';

        return $htmlCode;
    }
}
