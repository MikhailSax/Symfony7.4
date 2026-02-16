<?php

namespace App\Service;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class PriceCalculationService
{
    private ExpressionLanguage $expressionLanguage;

    public function __construct()
    {
        $this->expressionLanguage = new ExpressionLanguage();
    }

    public function calculate(string $formula, array $variables = []): float
    {
        $result = $this->expressionLanguage->evaluate($formula, $variables);

        return round((float) $result, 2);
    }
}
