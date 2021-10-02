<?php
/**
 *
 * This file is part of Atlas for PHP.
 *
 * @license http://opensource.org/licenses/mit-license.php MIT
 *
 */
namespace Atlas\Statement;

use PDO;

class UpdateTest extends StatementTest
{
    public function testCommon()
    {
        $this->statement->table('t1')
                    ->columns(['c1', 'c2', 'c3' => 'c3_value'])
                    ->set('c4', null)
                    ->set('c5', 'NOW()')
                    ->where('foo = :foo')
                    ->where('baz = :baz')
                    ->orWhere('zim = gir')
                    ->bindValues([
                        'foo' => 'bar',
                        'baz' => 'dib',
                    ]);

        $expect = "
            UPDATE t1
            SET
                <<c1>> = :c1,
                <<c2>> = :c2,
                <<c3>> = :c3,
                <<c4>> = NULL,
                <<c5>> = NOW()
            WHERE
                foo = :foo
                AND baz = :baz
                OR zim = gir
        ";

        $this->assertQueryString($expect, $this->statement);

        $expect = array(
            'c3' => ['c3_value', PDO::PARAM_STR],
            'foo' => ['bar', PDO::PARAM_STR],
            'baz' => ['dib', PDO::PARAM_STR],
        );

        $this->assertBindValues($expect, $this->statement);

        // add LIMIT
        $this->statement->limit(10)
                    ->offset(20);

        $expect = "
            UPDATE t1
            SET
                <<c1>> = :c1,
                <<c2>> = :c2,
                <<c3>> = :c3,
                <<c4>> = NULL,
                <<c5>> = NOW()
            WHERE
                foo = :foo
                AND baz = :baz
                OR zim = gir
            LIMIT 10 OFFSET 20
        ";

        $this->assertQueryString($expect, $this->statement);

        // add RETURNING
        $this->statement->returning('c1', 'c2')
                    ->returning('c3');

        $expect = "
            UPDATE t1
            SET
                <<c1>> = :c1,
                <<c2>> = :c2,
                <<c3>> = :c3,
                <<c4>> = NULL,
                <<c5>> = NOW()
            WHERE
                foo = :foo
                AND baz = :baz
                OR zim = gir
            LIMIT 10 OFFSET 20
            RETURNING
                c1,
                c2,
                c3
        ";

        $this->assertQueryString($expect, $this->statement);
    }

    public function testHasColumns()
    {
        $this->statement->table('t1');
        $this->assertFalse($this->statement->hasColumns());
        $this->statement->columns(['c1', 'c2']);
        $this->assertTrue($this->statement->hasColumns());
    }
}
