<?php

namespace app\widgets;

use yii\base\Widget;

class StarsWidget extends Widget
{
    public int $rating = 0;
    public string $size = 'big'; // тут значения либо 'small' али 'big' добавить в пхпдок nado

    public function run(): string
    {
        $filled = round($this->rating);
        $htmlCode = '<div class="stars-rating ' . $this->size . '">';

        for ($i = 1; $i <= 5; $i++) {
            $class = $i <= $filled ? 'fill-star' : '';
            $htmlCode .= '<span class="' . $class . '">&nbsp;</span>';
        }

        $htmlCode .= '</div>';

        return $htmlCode;
    }
}
