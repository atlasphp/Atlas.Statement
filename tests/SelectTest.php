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
use Atlas\Statement\Driver\FakeDriver;

class SelectTest extends StatementTest
{
    public function testDistinct()
    {
        $this->statement->distinct()
                     ->from('t1')
                     ->columns('t1.c1', 't1.c2', 't1.c3');

        $expect = '
            SELECT DISTINCT
                t1.c1,
                t1.c2,
                t1.c3
            FROM
                t1
        ';

        $this->assertQueryString($expect, $this->statement);
    }

    public function testDuplicateFlag()
    {
        $this->statement->distinct()
                    ->distinct()
                    ->from('t1')
                    ->columns('t1.c1', 't1.c2', 't1.c3');

        $expect = '
            SELECT DISTINCT
                t1.c1,
                t1.c2,
                t1.c3
            FROM
                t1
        ';

        $this->assertQueryString($expect, $this->statement);
    }

    public function testFlagUnset()
    {
        $this->statement->distinct()
                    ->distinct(false)
                    ->from('t1')
                    ->columns('t1.c1', 't1.c2', 't1.c3');

        $expect = '
            SELECT
                t1.c1,
                t1.c2,
                t1.c3
            FROM
                t1
        ';

        $this->assertQueryString($expect, $this->statement);
    }

    public function testColumns()
    {
        $this->assertFalse($this->statement->hasColumns());

        $this->statement->columns(
            't1.c1',
            'c2 AS a2',
            'COUNT(t1.c3)'
        );

        $this->assertTrue($this->statement->hasColumns());

        $expect = '
            SELECT
                t1.c1,
                c2 AS a2,
                COUNT(t1.c3)
        ';

        $this->assertQueryString($expect, $this->statement);
    }

    public function testFrom()
    {
        $this->statement->columns('*');
        $this->statement->from('t1')
                    ->from('t2');

        $expect = '
            SELECT
                *
            FROM
                t1,
                t2
        ';

        $this->assertQueryString($expect, $this->statement);
    }

    public function testFromSubSelect()
    {
        $this->statement
            ->columns('*')
            ->from($this->statement->subSelect()
                ->columns('*')
                ->from('t2')
                ->as('a2')
                ->getQueryString()
            );

        $expect = '
            SELECT
                *
            FROM
                (
                    SELECT
                        *
                    FROM
                        t2
                ) AS a2
        ';

        $this->assertQueryString($expect, $this->statement);
    }

    public function testFromSubSelectObject()
    {
        // note that these are "out of order" on purpose,
        // to make sure that sequential binding happens correctly.
        $this->statement->columns('*')
            ->where('a2.baz = ', 'dib')
            ->from($this->statement->subSelect()
                ->columns('*')
                ->from('t2')
                ->where('foo = ', 'bar')
                ->as('a2')
            );

        $expect = '
            SELECT
                *
            FROM
                (
                    SELECT
                        *
                    FROM
                        t2
                    WHERE
                        foo = :_2_1_
                ) AS a2
            WHERE
                a2.baz = :_1_1_
        ';

        $this->assertQueryString($expect, $this->statement);

        $expect = [
            '_1_1_' => ['dib', PDO::PARAM_STR],
            '_2_1_' => ['bar', PDO::PARAM_STR]
        ];

        $this->assertBindValues($expect, $this->statement);
    }

    public function testJoin()
    {
        $this->statement->columns('*');
        $this->statement->from('t1');
        $this->statement->join('left', 't2', 't1.id = t2.id');
        $this->statement->join('inner', 't3 AS a3', 't2.id = a3.id');
        $this->statement->from('t4');
        $this->statement->join('natural', 't5');
        $expect = '
            SELECT
                *
            FROM
                t1
                    LEFT JOIN t2 ON t1.id = t2.id
                    INNER JOIN t3 AS a3 ON t2.id = a3.id,
                t4
                    NATURAL JOIN t5
        ';

        $this->assertQueryString($expect, $this->statement);
    }

    public function testJoinAndBind()
    {
        $this->statement->columns('*');
        $this->statement->from('t1');
        $this->statement->join(
            'left',
            't2',
            't1.id = t2.id AND t1.foo = ',
            'bar'
        );
        $this->statement->catJoin(' AND t1.baz = ', 'dib');

        $expect = '
            SELECT
                *
            FROM
                t1
            LEFT JOIN t2 ON t1.id = t2.id AND t1.foo = :_1_1_ AND t1.baz = :_1_2_
        ';

        $this->assertQueryString($expect, $this->statement);

        $expect = [
            '_1_1_' => ['bar', PDO::PARAM_STR],
            '_1_2_' => ['dib', PDO::PARAM_STR]
        ];

        $this->assertBindValues($expect, $this->statement);
    }

    public function testJoinSubSelect()
    {
        $sub1 = '(SELECT * FROM t2) AS a2';
        $sub2 = '(SELECT * FROM t3) AS a3';
        $this->statement->columns('*');
        $this->statement->from('t1');
        $this->statement->join('left', $sub1, 't2.c1 = a3.c1');
        $this->statement->join('natural', $sub2);
        $expect = '
            SELECT
                *
            FROM
                t1
                    LEFT JOIN (SELECT * FROM t2) AS a2 ON t2.c1 = a3.c1
                    NATURAL JOIN (SELECT * FROM t3) AS a3
        ';
        $this->assertQueryString($expect, $this->statement);
    }

    public function testJoinSubSelectObject()
    {
        $sub = $this->statement->subSelect();
        $sub->columns('*')->from('t2')->where('foo = ', 'bar')->as('a3');

        $this->statement->columns('*');
        $this->statement->from('t1');
        $this->statement->join('left', $sub, 't2.c1 = a3.c1');
        $this->statement->where('baz = ', 'dib');

        $expect = '
            SELECT
                *
            FROM
                t1
                    LEFT JOIN (
                        SELECT
                            *
                        FROM
                            t2
                        WHERE
                            foo = :_2_1_
                    ) AS a3 ON t2.c1 = a3.c1
            WHERE
                baz = :_1_1_
        ';
        $this->assertQueryString($expect, $this->statement);
    }

    public function testJoinOrder()
    {
        $this->statement
            ->columns('*')
            ->from('t1')
            ->join('inner', 't2', 't2.id = t1.id')
            ->join('left', 't3', 't3.id = t2.id')
            ->from('t4')
            ->join('inner', 't5', 't5.id = t4.id');

        $expect = '
            SELECT
                *
            FROM
                t1
                    INNER JOIN t2 ON t2.id = t1.id
                    LEFT JOIN t3 ON t3.id = t2.id,
                t4
                    INNER JOIN t5 ON t5.id = t4.id
        ';

        $this->assertQueryString($expect, $this->statement);
    }

    public function testJoinOnAndUsing()
    {
        $this->statement
            ->columns('*')
            ->from('t1')
            ->join('inner', 't2', 'ON t2.id = t1.id')
            ->join('left', 't3', 'USING (id)');

        $expect = '
            SELECT
                *
            FROM
                t1
                    INNER JOIN t2 ON t2.id = t1.id
                    LEFT JOIN t3 USING (id)
        ';

        $this->assertQueryString($expect, $this->statement);
    }

    public function testWhere()
    {
        $this->statement
            ->columns('*')
            ->where('c1 = c2')
            ->andWhere('c3 = :c3')
            ->andWhere('c4 IN', [null, true, 1])
            ->catWhere(' AND c5 = ' . $this->statement->bindInline(2))
            ->bindValue('c3', 'foo');

        $expect = '
            SELECT
                *
            WHERE
                c1 = c2
                AND c3 = :c3
                AND c4 IN(:_1_1_, :_1_2_, :_1_3_) AND c5 = :_1_4_
        ';

        $this->assertQueryString($expect, $this->statement);

        $expect = [
            '_1_1_' => [null, PDO::PARAM_NULL],
            '_1_2_' => [true, PDO::PARAM_BOOL],
            '_1_3_' => [1, PDO::PARAM_INT],
            '_1_4_' => [2, PDO::PARAM_INT],
            'c3' => ['foo', PDO::PARAM_STR],
        ];
        $this->assertBindValues($expect, $this->statement);
    }

    public function testOrWhere()
    {
        $this->statement
            ->columns('*')
            ->catWhere('c1 = ', 'bar')
            ->orWhere('c3 = :c3')
            ->bindValue('c3', 'foo');

        $expect = '
            SELECT
                *
            WHERE
                c1 = :_1_1_
                OR c3 = :c3
        ';

        $this->assertQueryString($expect, $this->statement);

        $expect = [
            '_1_1_' => ['bar', PDO::PARAM_STR],
            'c3' => ['foo', PDO::PARAM_STR]
        ];

        $this->assertBindValues($expect, $this->statement);
    }

    public function testWhereEquals()
    {
        $actual = $this->statement
            ->columns('*')
            ->whereEquals([
                'foo' => [1, 2, 3],
                'bar' => null,
                'baz' => 'baz_value',
                'dib = NOW()',
                'gir' => []
            ]);

        $expect = '
            SELECT
                *
            WHERE
                foo IN (:_1_1_, :_1_2_, :_1_3_)
                AND bar IS NULL
                AND baz = :_1_4_
                AND dib = NOW()
                AND FALSE
        ';

        $this->assertQueryString($expect, $this->statement);
    }

    public function testWhereSprintf()
    {
        $actual = $this->statement
            ->columns('*')
            ->whereSprintf('c1 BETWEEN %s AND %s', 11, 22)
            ->andWhereSprintf('c2 BETWEEN %s AND %s', 33, 44)
            ->orWhereSprintf('c3 BETWEEN %s AND %s', 55, 66)
            ->catWhereSprintf(' UNLESS c4 BETWEEN %s AND %s', 77, 88);

        $expect = '
            SELECT
                *
            WHERE
                c1 BETWEEN :_1_1_ AND :_1_2_
                AND c2 BETWEEN :_1_3_ AND :_1_4_
                OR c3 BETWEEN :_1_5_ AND :_1_6_ UNLESS c4 BETWEEN :_1_7_ AND :_1_8_
        ';

        $this->assertQueryString($expect, $this->statement);
    }

    public function testGroupBy()
    {
        $this->statement
            ->columns('*')
            ->groupBy('c1')
            ->groupBy('t2.c2');

        $expect = '
            SELECT
                *
            GROUP BY
                c1,
                t2.c2
        ';

        $this->assertQueryString($expect, $this->statement);
    }

    public function testHaving()
    {
        $this->statement
            ->columns('*')
            ->having('c1 = c2')
            ->andHaving('c3 = :c3')
            ->orHaving('(c4 = 1 ')
            ->catHaving('AND c5 = 2)')
            ->bindValue('c3', 'foo');

        $expect = '
            SELECT
                *
            HAVING
                c1 = c2
                AND c3 = :c3
                OR (c4 = 1 AND c5 = 2)
        ';

        $this->assertQueryString($expect, $this->statement);

        $expect = [
            'c3' => ['foo', PDO::PARAM_STR]
        ];
        $this->assertBindValues($expect, $this->statement);
    }

    public function testOrHaving()
    {
        $this->statement
            ->columns('*')
            ->orHaving('c1 = c2')
            ->orHaving('c3 = :c3')
            ->bindValue('c3', 'foo');

        $expect = '
            SELECT
                *
            HAVING
                c1 = c2
                OR c3 = :c3
        ';

        $this->assertQueryString($expect, $this->statement);

        $expect = [
            'c3' => ['foo', PDO::PARAM_STR],
        ];

        $this->assertBindValues($expect, $this->statement);
    }

    public function testHavingSprintf()
    {
        $this->statement
            ->columns('*')
            ->HavingSprintf('c1 BETWEEN %s AND %s', 11, 22)
            ->andHavingSprintf('c2 BETWEEN %s AND %s', 33, 44)
            ->orHavingSprintf('c3 BETWEEN %s AND %s', 55, 66)
            ->catHavingSprintf(' UNLESS c4 BETWEEN %s AND %s', 77, 88);

        $expect = '
            SELECT
                *
            HAVING
                c1 BETWEEN :_1_1_ AND :_1_2_
                AND c2 BETWEEN :_1_3_ AND :_1_4_
                OR c3 BETWEEN :_1_5_ AND :_1_6_ UNLESS c4 BETWEEN :_1_7_ AND :_1_8_
        ';

        $this->assertQueryString($expect, $this->statement);
    }

    public function testOrderBy()
    {
        $this->statement
            ->columns('*')
            ->orderBy('c1', 'UPPER(t2.c2)');

        $expect = '
            SELECT
                *
            ORDER BY
                c1,
                UPPER(t2.c2)
        ';

        $this->assertQueryString($expect, $this->statement);
    }

    public function testLimitOffset()
    {
        $this->statement->columns('*');
        $this->statement->limit(10);
        $expect = '
            SELECT
                *
            LIMIT 10
        ';
        $this->assertQueryString($expect, $this->statement);

        $this->statement->offset(40);

        $expect = '
            SELECT
                *
            LIMIT 10 OFFSET 40
        ';

        $this->assertQueryString($expect, $this->statement);
    }

    public function testPage()
    {
        $this->statement->columns('*');
        $this->statement->page(5);
        $expect = '
            SELECT
                *
            LIMIT 10 OFFSET 40
        ';
        $this->assertQueryString($expect, $this->statement);

        $this->statement->perPage(25);
        $expect = '
            SELECT
                *
            LIMIT 25 OFFSET 100
        ';
        $this->assertQueryString($expect, $this->statement);
    }

    public function testForUpdate()
    {
        $this->statement->columns('*');
        $this->statement->forUpdate();
        $expect = '
            SELECT
                *
            FOR UPDATE
        ';
        $this->assertQueryString($expect, $this->statement);
    }

    public function testUnion()
    {
        $this->statement->columns('c1')
                     ->from('t1')
                     ->union()
                     ->columns('c2')
                     ->from('t2')
                     ->union()
                     ->columns('c3')
                     ->from('t3')
                     ->union()
                     ->columns('c4')
                     ->from('t4');

        $expect = '
            SELECT
                c1
            FROM
                t1
            UNION
            SELECT
                c2
            FROM
                t2
            UNION
            SELECT
                c3
            FROM
                t3
            UNION
            SELECT
                c4
            FROM
                t4
        ';

        $this->assertQueryString($expect, $this->statement);
    }

    public function testUnionAll()
    {
        $this->statement->columns('c1')
                     ->from('t1')
                     ->unionAll()
                     ->columns('c2')
                     ->from('t2')
                     ->unionAll()
                     ->columns('c3')
                     ->from('t3')
                     ->unionAll()
                     ->columns('c4')
                     ->from('t4');

        $expect = '
            SELECT
                c1
            FROM
                t1
            UNION ALL
            SELECT
                c2
            FROM
                t2
            UNION ALL
            SELECT
                c3
            FROM
                t3
            UNION ALL
            SELECT
                c4
            FROM
                t4
        ';

        $this->assertQueryString($expect, $this->statement);
    }

    public function testWhereSubSelect()
    {
        $select = $this->newStatement();

        $select->columns('*')
            ->from('table2 AS t2')
            ->where('field IN ', $select->subSelect()
                ->columns('col1')
                ->from('table1 AS t1')
            );

        $expect = '
            SELECT
                *
            FROM
                table2 AS t2
            WHERE
                field IN (SELECT
                col1
            FROM
                table1 AS t1)
        ';

        $this->assertQueryString($expect, $select);
    }

    public function testProperPaging()
    {
        $limit = new \Atlas\Statement\Clause\Component\Limit();

        $this->assertSame(0, $limit->getPage());
        $this->assertSame(10, $limit->getPerPage());
        $this->assertSame(0, $limit->getLimit());
        $this->assertSame(0, $limit->getOffset());

        $limit->setPage(3);
        $this->assertSame(3, $limit->getPage());
        $this->assertSame(10, $limit->getPerPage());
        $this->assertSame(10, $limit->getLimit());
        $this->assertSame(20, $limit->getOffset());

        $limit->setLimit(10);
        $this->assertSame(0, $limit->getPage());
        $this->assertSame(10, $limit->getPerPage());
        $this->assertSame(10, $limit->getLimit());
        $this->assertSame(0, $limit->getOffset());

        $limit->setPage(3);
        $limit->setPerPage(50);
        $this->assertSame(3, $limit->getPage());
        $this->assertSame(50, $limit->getPerPage());
        $this->assertSame(50, $limit->getLimit());
        $this->assertSame(100, $limit->getOffset());

        $limit->setOffset(10);
        $this->assertSame(0, $limit->getPage());
        $this->assertSame(50, $limit->getPerPage());
        $this->assertSame(0, $limit->getLimit());
        $this->assertSame(10, $limit->getOffset());
    }

    public function testAs()
    {
        $this->statement->columns('*')->from('t1')->as('foo');
        $expect = '
            (
                SELECT
                    *
                FROM
                    t1
            ) AS foo
        ';
        $this->assertQueryString($expect, $this->statement);
    }

    public function test__clone()
    {
        $select = new FakeSelect(new FakeDriver());
        $clone = clone $select;

        $vars = ['bind', 'flags', 'columns', 'from', 'where', 'groupBy', 'having', 'orderBy', 'limit'];
        foreach ($vars as $var) {
            $this->assertNotSame($select->$var, $clone->$var);
        }
    }

    public function testUnionSelectCanHaveSameAliasesInDifferentSelects()
    {
        $this->statement
            ->columns(
                '...'
            )
            ->from('a')
            ->join('INNER', 'c', 'a_cid = c_id')
            ->union()
            ->columns(
                '...'
            )
            ->from('b')
            ->join('INNER', 'c', 'b_cid = c_id');

        $expect = 'SELECT
                    ...
                    FROM
                    a
                    INNER JOIN c ON a_cid = c_id
                    UNION
                    SELECT
                    ...
                    FROM
                    b
                    INNER JOIN c ON b_cid = c_id';

        $this->assertQueryString($expect, $this->statement);
    }

    public function testQuoteIdentifier()
    {
        $actual = $this->statement->quoteIdentifier('foo');
        $this->assertSame('<<foo>>', $actual);
    }

    public function testSetFlag()
    {
        $this->statement->columns('*')->from('t1')->setFlag('LOW_PRIORITY');
        $expect = '
            SELECT LOW_PRIORITY
                *
            FROM
                t1
        ';
        $this->assertQueryString($expect, $this->statement);
    }

    public function testSqlsrvLimitOffset()
    {
        $this->statement = Select::new('sqlsrv');

        $this->statement->columns('*');
        $this->statement->limit(10);
        $expect = '
            SELECT TOP 10
                *
        ';
        $this->assertQueryString($expect, $this->statement);

        $this->statement->offset(40);
        $expect = '
            SELECT
                *
            OFFSET 40 ROWS FETCH NEXT 10 ROWS ONLY
        ';
        $this->assertQueryString($expect, $this->statement);
    }

    public function testSqlsrvPage()
    {
        $this->statement = Select::new('sqlsrv');

        $this->statement->columns('*');
        $this->statement->page(5);
        $expect = '
            SELECT
                *
            OFFSET 40 ROWS FETCH NEXT 10 ROWS ONLY
        ';
        $this->assertQueryString($expect, $this->statement);
    }

    public function testBindSprintf()
    {
        $this->statement->columns('*')
                    ->from('t1')
                    ->where($this->statement->bindSprintf(
                        'c2 BETWEEN %s AND %s',
                        6,
                        9
                    ));

        $expect = '
            SELECT
                *
            FROM
                t1
            WHERE
                c2 BETWEEN :_1_1_ AND :_1_2_
        ';

        $this->assertQueryString($expect, $this->statement);

        $expect = [
            '_1_1_' => [6, PDO::PARAM_INT],
            '_1_2_' => [9, PDO::PARAM_INT],
        ];
        $this->assertBindValues($expect, $this->statement);
    }

    public function testWith()
    {
        $this->statement
            ->with('cte1', 'SELECT dib, zim FROM gir')
            ->withColumns('cte2', ['foo', 'bar'], 'SELECT * FROM baz')
            ->columns('*')
            ->from('cte1')
            ->union()
            ->columns('*')
            ->from('cte2');

        $expect = '
            WITH
                <<cte1>> AS (
                    SELECT dib, zim FROM gir
                ),
                <<cte2>> (<<foo>>, <<bar>>) AS (
                    SELECT * FROM baz
                )
            SELECT
                *
            FROM
                cte1
            UNION
            SELECT
                *
            FROM
                cte2
        ';

        $this->assertQueryString($expect, $this->statement);
    }

    public function testWithQueryObject()
    {
        $cte1 = $this->newStatement()
            ->columns('*')
            ->from('baz')
            ->where('c1 = ', 'v1');

        $cte2 = $this->newStatement()
            ->columns('dib', 'zim')
            ->from('gir')
            ->where('c2 = ', 'v2');

        $this->statement
            ->with('cte1', $cte2)
            ->withColumns('cte2', ['foo', 'bar'], $cte1)
            ->columns('*')
            ->from('cte1')
            ->union()
            ->columns('*')
            ->from('cte2');

        $expect = '
        WITH
            <<cte1>> AS (
                SELECT
                    dib,
                    zim
                FROM
                    gir
                WHERE
                    c2 = :_3_1_
            ),
            <<cte2>> (<<foo>>, <<bar>>) AS (
                SELECT
                *
                FROM
                    baz
                WHERE
                    c1 = :_2_1_
            )
        SELECT
            *
        FROM
            cte1
        UNION
        SELECT
            *
        FROM
            cte2
        ';

        $this->assertQueryString($expect, $this->statement);

        $expect = [
            '_3_1_' => [
                0 => 'v2',
                1 => 2,
            ],
            '_2_1_' => [
                0 => 'v1',
                1 => 2,
            ],
        ];

        $this->assertBindValues($expect, $this->statement);
    }

    public function testWithRecursive()
    {
        $this->statement
            ->withRecursive()
            ->with('cte1', 'SELECT dib, zim FROM gir')
            ->withColumns('cte2', ['foo', 'bar'], 'SELECT * FROM baz')
            ->columns('*')
            ->from('cte1')
            ->union()
            ->columns('*')
            ->from('cte2');

        $expect = '
            WITH RECURSIVE
                <<cte1>> AS (
                    SELECT dib, zim FROM gir
                ),
                <<cte2>> (<<foo>>, <<bar>>) AS (
                    SELECT * FROM baz
                )
            SELECT
                *
            FROM
                cte1
            UNION
            SELECT
                *
            FROM
                cte2
        ';

        $this->assertQueryString($expect, $this->statement);
    }
}
