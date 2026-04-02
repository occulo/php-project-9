<?php

namespace Hexlet\Code;

class Database
{
    public static function connect(string $dbUrl): \PDO
    {
        $db = parse_url($dbUrl);

        $name = trim($db['path'], '/');
        $host = $db['host'];
        $port = $db['port'];
        $user = $db['user'];
        $password = $db['pass'];

        return new \PDO("pgsql:host=$host;port=$port;dbname=$name", $user, $password);
    }
}
