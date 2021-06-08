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

class DeleteTest extends StatementTest
{
    public function testCommon()
    {
        $this->statement->from('t1')
                    ->where('foo = :foo')
                    ->where('baz = :baz')
                    ->orWhere('zim = gir')
                    ->returning('foo', 'baz', 'zim')
                    ->bindValues([
                        'foo' => 'bar',
                        'baz' => 'dib',
                    ]);

        $expect = "
            DELETE FROM t1
            WHERE
                foo = :foo
                AND baz = :baz
                OR zim = gir
            RETURNING
                foo,
                baz,
                zim
        ";

        $this->assertQueryString($expect, $this->statement);

        $expect = array(
            'foo' => ['bar', PDO::PARAM_STR],
            'baz' => ['dib', PDO::PARAM_STR],
        );

        $this->assertBindValues($expect, $this->statement);
    }
}
