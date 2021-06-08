<?php
/**
 *
 * This file is part of Atlas for PHP.
 *
 * @license http://opensource.org/licenses/mit-license.php MIT
 *
 */
declare(strict_types=1);

namespace Atlas\Statement;

use PDO;
use PDOStatement;

class Prepare
{
    static public function pdoStatement(PDO $pdo, Statement $statement) : PDOStatement
    {
        $sth = $pdo->prepare($statement->getQueryString());

        foreach ($statement->getBindValueObjects() as $name => $value) {
            $sth->bindValue($name, $value->getValue(), $value->getType());
        }

        return $sth;
    }
}
