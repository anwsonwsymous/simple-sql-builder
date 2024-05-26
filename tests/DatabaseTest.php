<?php

namespace Tests;

use FpDbTest\Database;
use InvalidArgumentException;
use mysqli;
use PHPUnit\Framework\TestCase;
use stdClass;

class DatabaseTest extends TestCase
{
    private Database $db;

    protected function setUp(): void
    {
        $mysqliMock = $this->createMock(mysqli::class);
        $this->db = new Database($mysqliMock);
    }

    public function testSimpleSelectQuery()
    {
        $query = $this->db->buildQuery('SELECT name FROM users WHERE user_id = 1');
        $expected = 'SELECT name FROM users WHERE user_id = 1';
        $this->assertEquals($expected, $query);
    }

    public function testSelectQueryWithParameter()
    {
        $query = $this->db->buildQuery(
            'SELECT * FROM users WHERE name = ? AND block = 0',
            ['Jack']
        );
        $expected = 'SELECT * FROM users WHERE name = \'Jack\' AND block = 0';
        $this->assertEquals($expected, $query);
    }

    public function testSelectQueryWithIdentifiers()
    {
        $query = $this->db->buildQuery(
            'SELECT ?# FROM users WHERE user_id = ?d AND block = ?d',
            [['name', 'email'], 2, true]
        );
        $expected = 'SELECT `name`, `email` FROM users WHERE user_id = 2 AND block = 1';
        $this->assertEquals($expected, $query);
    }

    public function testUpdateQueryWithAssociativeArray()
    {
        $query = $this->db->buildQuery(
            'UPDATE users SET ?a WHERE user_id = -1',
            [['name' => 'Jack', 'email' => null]]
        );
        $expected = 'UPDATE users SET `name` = \'Jack\', `email` = NULL WHERE user_id = -1';
        $this->assertEquals($expected, $query);
    }

    public function testSelectQueryWithConditionalBlockNull()
    {
        $query = $this->db->buildQuery(
            'SELECT name FROM users WHERE ?# IN (?a){ AND block = ?d}',
            ['user_id', [1, 2, 3], $this->db->skip()]
        );
        $expected = 'SELECT name FROM users WHERE `user_id` IN (1, 2, 3)';
        $this->assertEquals($expected, $query);
    }

    public function testSelectQueryWithConditionalBlockTrue()
    {
        $query = $this->db->buildQuery(
            'SELECT name FROM users WHERE ?# IN (?a){ AND block = ?d}',
            ['user_id', [1, 2, 3], true]
        );
        $expected = 'SELECT name FROM users WHERE `user_id` IN (1, 2, 3) AND block = 1';
        $this->assertEquals($expected, $query);
    }

    public function testBuildQueryWithInvalidType()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->db->buildQuery("SELECT * FROM users WHERE user_id = ?", [new stdClass()]);
    }
}