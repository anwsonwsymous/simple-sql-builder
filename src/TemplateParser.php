<?php

namespace FpDbTest;

class TemplateParser
{
    private const CONDITIONAL_PATTERN = '/\{.*?\}/s';
    private const PLACEHOLDER_PATTERN = '/\?([a-zA-Z#]?)/';

    private ParameterProcessor $parameterProcessor;

    public function __construct()
    {
        $this->parameterProcessor = new ParameterProcessor();
    }

    public function parse(string $template, array $params): string
    {
        return $this->parsePlaceholders(
            $this->parseConditionalBlocks($template, $params),
            $params
        );
    }

    private function parseConditionalBlocks(string $template, array $params): string
    {
        return preg_replace_callback(
            self::CONDITIONAL_PATTERN,
            function ($matches) use ($params) {
                $block = $matches[0];
                if ($this->shouldSkipBlock($block, $params)) {
                    return '';
                }
                return substr($block, 1, -1);
            },
            $template
        );
    }

    private function parsePlaceholders(string $template, array $params): string
    {
        $index = 0;
        return preg_replace_callback(
            self::PLACEHOLDER_PATTERN,
            function ($matches) use ($params, &$index) {
                $specifier = $matches[1] ?? '';
                $value = $params[$index++] ?? null;
                return $this->parameterProcessor->process($specifier, $value);
            },
            $template
        );
    }

    private function shouldSkipBlock(string $block, array $params): bool
    {
        preg_match_all(self::PLACEHOLDER_PATTERN, $block, $matches);
        foreach ($matches[1] as $ignored) {
            foreach ($params as $param) {
                if ($this->parameterProcessor->isSkipParameter($param)) {
                    return true;
                }
            }
        }
        return false;
    }
}