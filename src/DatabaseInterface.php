<?php

namespace FpDbTest;

interface DatabaseInterface
{
    public function buildQuery(string $query, array $args = []): string;

    public function execute(string $query);

    public function skip();
}
