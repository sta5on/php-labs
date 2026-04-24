<?php

declare(strict_types=1);

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Регистрирует пользовательские Twig-фильтры проекта.
 */
final class TransactionTwigExtension extends AbstractExtension
{
    /**
     * @return array<int, TwigFilter>
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('currency', static fn(int|float|string $amount, string $currency = 'MDL'): string => formatCurrency($amount, $currency)),
        ];
    }
}
