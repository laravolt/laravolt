<?php

declare(strict_types=1);

namespace Laravolt\Tests\Unit\SemanticForm;

use InvalidArgumentException;
use Laravolt\SemanticForm\DbProxyQuery;
use PHPUnit\Framework\TestCase;

class DbProxyQueryTest extends TestCase
{
    public function test_a_quoted_like_pattern_becomes_one_parameter(): void
    {
        $this->assertSame(
            ['select id, name from people where name like ?', ['%ab%']],
            DbProxyQuery::bind("select id, name from people where name like '%%%s%%'", 'ab'),
        );
    }

    public function test_a_double_quoted_literal_is_bound_the_same_way(): void
    {
        $this->assertSame(
            ['select id from people where name like ?', ['ab%']],
            DbProxyQuery::bind('select id from people where name like "%s%%"', 'ab'),
        );
    }

    public function test_a_bare_placeholder_becomes_a_parameter(): void
    {
        $this->assertSame(
            ['select id, name from cities where province_id = ?', ['12']],
            DbProxyQuery::bind('select id, name from cities where province_id = %s', '12'),
        );
    }

    public function test_every_positional_placeholder_is_bound(): void
    {
        $this->assertSame(
            ['select id from people where first like ? or last like ?', ['x%', '%x']],
            DbProxyQuery::bind("select id from people where first like '%1\$s%%' or last like '%%%1\$s'", 'x'),
        );
    }

    public function test_literals_without_a_placeholder_and_escaped_percent_signs_are_kept(): void
    {
        $this->assertSame(
            ["select '100%' as rate, name from people where status = 'ACTIVE' and name = ?", ['ab']],
            DbProxyQuery::bind("select '100%%' as rate, name from people where status = 'ACTIVE' and name = '%s'", 'ab'),
        );
    }

    public function test_quotes_in_the_term_stay_inside_the_parameter(): void
    {
        $this->assertSame(
            ['select id from people where name like ?', ["%O'Brien%"]],
            DbProxyQuery::bind("select id from people where name like '%%%s%%'", "O'Brien"),
        );
    }

    public function test_a_doubled_quote_inside_the_literal_is_unescaped_in_the_parameter(): void
    {
        $this->assertSame(
            ['select id from people where name = ?', ["O'ab"]],
            DbProxyQuery::bind("select id from people where name = 'O''%s'", 'ab'),
        );
    }

    public function test_a_query_without_placeholder_is_unchanged(): void
    {
        $this->assertSame(['select id, name from people', []], DbProxyQuery::bind('select id, name from people', 'ab'));
    }

    public function test_an_unterminated_literal_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        DbProxyQuery::bind("select id from people where name = '%s", 'ab');
    }

    public function test_other_sprintf_conversions_are_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        DbProxyQuery::bind('select id from people where id = %d', '1');
    }
}
