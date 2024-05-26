<?php

namespace FpDbTest;

use mysqli;

class Database implements DatabaseInterface
{
    private mysqli $mysqli;
    private TemplateParser $parser;

    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
        $this->parser = new TemplateParser();
    }

    public function buildQuery(string $query, array $args = []): string
    {
        return $this->parser->parse($query, $args);
    }

    public function execute(string $query)
    {
        return $this->mysqli->query($query);
    }

    public function skip()
    {
        return new SkipParameter();
    }
}
