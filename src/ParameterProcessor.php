<?php

namespace FpDbTest;

use InvalidArgumentException;

class ParameterProcessor
{
    public function process(string $specifier, mixed $value): string
    {
        return match ($specifier) {
            'd' => $this->convertToInt($value),
            'f' => $this->convertToFloat($value),
            'a' => $this->convertToArray($value),
            '#' => $this->convertToIdentifier($value),
            default => $this->convertToDefault($value),
        };
    }

    public function isSkipParameter(mixed $param): bool
    {
        return $param instanceof SkipParameter;
    }

    private function convertToInt(mixed $value): string
    {
        return $value === null ? 'NULL' : (string)(int)$value;
    }

    private function convertToFloat(mixed $value): string
    {
        return $value === null ? 'NULL' : (string)(float)$value;
    }

    private function convertToArray(mixed $value): string
    {
        if (!is_array($value)) {
            throw new InvalidArgumentException("Expected array for specifier ?a");
        }
        return $this->isAssociativeArray($value)
            ? $this->convertToAssociativeArray($value)
            : $this->convertToIndexedArray($value);
    }

    private function isAssociativeArray(array $array): bool
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    private function convertToAssociativeArray(array $value): string
    {
        $result = [];
        foreach ($value as $key => $val) {
            $result[] = $this->escapeIdentifier((string)$key) . ' = ' . $this->convertToDefault($val);
        }
        return implode(', ', $result);
    }

    private function convertToIndexedArray(array $value): string
    {
        $result = array_map([$this, 'convertToDefault'], $value);
        return implode(', ', $result);
    }

    private function convertToIdentifier(mixed $value): string
    {
        if (is_array($value)) {
            $result = array_map([$this, 'escapeIdentifier'], $value);
            return implode(', ', $result);
        }
        return $this->escapeIdentifier($value);
    }

    private function convertToDefault(mixed $value): string
    {
        return match (true) {
            is_string($value) => "'" . $this->escapeString($value) . "'",
            is_int($value), is_float($value) => (string)$value,
            is_bool($value) => $value ? '1' : '0',
            $value === null => 'NULL',
            default => throw new InvalidArgumentException("Unsupported data type"),
        };
    }

    private function escapeString(string $value): string
    {
        return addslashes($value);
    }

    private function escapeIdentifier(string $value): string
    {
        return sprintf('`%s`', $this->escapeString($value));
    }
}
