<?php

namespace App\Libraries;

use InvalidArgumentException;

class InventoryUnitService
{
    private const SCALE = 6;

    public function normalizeUnit(?string $unit): string
    {
        $normalized = strtolower(trim((string) $unit));

        return match ($normalized) {
            'kg', 'kilogramme', 'kilogrammes' => 'kg',
            'g', 'gramme', 'grammes' => 'g',
            'l', 'litre', 'litres', 'liter', 'liters' => 'litre',
            'ml', 'millilitre', 'millilitres', 'milliliter', 'milliliters' => 'ml',
            default => throw new InvalidArgumentException('Unite non prise en charge.'),
        };
    }

    public function getBaseUnit(string $unit): string
    {
        return match ($this->normalizeUnit($unit)) {
            'kg', 'g' => 'g',
            'litre', 'ml' => 'ml',
        };
    }

    public function getQuantityStep(string $unit): string
    {
        return match ($this->normalizeUnit($unit)) {
            'kg', 'litre' => '0.001',
            default => '1',
        };
    }

    public function toBaseQuantity(string|int|float $quantity, string $unit): string
    {
        $normalizedUnit = $this->normalizeUnit($unit);
        $value = $this->sanitizeDecimal($quantity);

        return match ($normalizedUnit) {
            'kg', 'litre' => $this->trimDecimal(bcmul($value, '1000', self::SCALE)),
            'g', 'ml' => $this->trimDecimal($value),
        };
    }

    public function fromBaseQuantity(string|int|float $quantity, string $displayUnit): string
    {
        $normalizedUnit = $this->normalizeUnit($displayUnit);
        $value = $this->sanitizeDecimal($quantity);

        return match ($normalizedUnit) {
            'kg', 'litre' => $this->trimDecimal(bcdiv($value, '1000', self::SCALE)),
            'g', 'ml' => $this->trimDecimal($value),
        };
    }

    public function toBaseUnitPrice(string|int|float $price, string $unit): string
    {
        $normalizedUnit = $this->normalizeUnit($unit);
        $value = $this->sanitizeDecimal($price);

        return match ($normalizedUnit) {
            'kg', 'litre' => $this->trimDecimal(bcdiv($value, '1000', self::SCALE)),
            'g', 'ml' => $this->trimDecimal($value),
        };
    }

    public function fromBaseUnitPrice(string|int|float $price, string $displayUnit): string
    {
        $normalizedUnit = $this->normalizeUnit($displayUnit);
        $value = $this->sanitizeDecimal($price);

        return match ($normalizedUnit) {
            'kg', 'litre' => $this->trimDecimal(bcmul($value, '1000', self::SCALE)),
            'g', 'ml' => $this->trimDecimal($value),
        };
    }

    public function multiply(string|int|float $left, string|int|float $right): string
    {
        return $this->trimDecimal(
            bcmul($this->sanitizeDecimal($left), $this->sanitizeDecimal($right), self::SCALE)
        );
    }

    public function add(string|int|float $left, string|int|float $right): string
    {
        return $this->trimDecimal(
            bcadd($this->sanitizeDecimal($left), $this->sanitizeDecimal($right), self::SCALE)
        );
    }

    public function subtract(string|int|float $left, string|int|float $right): string
    {
        return $this->trimDecimal(
            bcsub($this->sanitizeDecimal($left), $this->sanitizeDecimal($right), self::SCALE)
        );
    }

    public function compare(string|int|float $left, string|int|float $right): int
    {
        return bccomp($this->sanitizeDecimal($left), $this->sanitizeDecimal($right), self::SCALE);
    }

    public function divide(string|int|float $left, string|int|float $right): string
    {
        if ($this->compare($right, '0') === 0) {
            return '0';
        }

        return $this->trimDecimal(
            bcdiv($this->sanitizeDecimal($left), $this->sanitizeDecimal($right), self::SCALE)
        );
    }

    public function displayLabel(string $unit): string
    {
        return match ($this->normalizeUnit($unit)) {
            'kg' => 'kg',
            'g' => 'g',
            'litre' => 'litre',
            'ml' => 'ml',
        };
    }

    public function enrichProductForDisplay(array $product): array
    {
        $displayUnit = $this->normalizeUnit($product['unite_affichage'] ?? $product['unite_base'] ?? 'g');
        $baseUnit = $this->getBaseUnit($displayUnit);

        $product['unite_affichage'] = $displayUnit;
        $product['unite_base'] = $product['unite_base'] ?? $baseUnit;
        $product['quantite_display'] = $this->fromBaseQuantity($product['quantite'] ?? '0', $displayUnit);
        $product['prix_achat_display'] = $this->fromBaseUnitPrice($product['prix_achat'] ?? '0', $displayUnit);
        $product['prix_vente_display'] = $this->fromBaseUnitPrice($product['prix_vente'] ?? '0', $displayUnit);
        $product['display_unit_label'] = $this->displayLabel($displayUnit);
        $product['base_unit_label'] = $this->displayLabel($baseUnit);
        $product['quantity_step'] = $this->getQuantityStep($displayUnit);
        $product['stock_value'] = $this->multiply($product['quantite'] ?? '0', $product['prix_vente'] ?? '0');

        return $product;
    }

    public function isLowStock(array $product, string $threshold = '5'): bool
    {
        $displayUnit = $product['unite_affichage'] ?? $product['unite_base'] ?? 'g';
        $displayQuantity = $this->fromBaseQuantity($product['quantite'] ?? '0', $displayUnit);

        return $this->compare($displayQuantity, $threshold) < 0;
    }

    public function formatDecimal(string|int|float $value, int $precision = 3): string
    {
        $number = (float) $this->sanitizeDecimal($value);

        return number_format($number, $precision, '.', ' ');
    }

    private function sanitizeDecimal(string|int|float $value): string
    {
        $normalized = str_replace(',', '.', trim((string) $value));

        if ($normalized === '' || !is_numeric($normalized)) {
            return '0';
        }

        return $normalized;
    }

    public function trimDecimal(string $value): string
    {
        $value = rtrim(rtrim($value, '0'), '.');

        return $value === '' || $value === '-0' ? '0' : $value;
    }
}
