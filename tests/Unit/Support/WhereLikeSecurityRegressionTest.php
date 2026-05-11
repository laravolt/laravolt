<?php

declare(strict_types=1);

namespace Laravolt\Tests\Unit\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravolt\Support\SupportServiceProvider;
use Laravolt\Tests\UnitTest;

class WhereLikeSecurityRegressionTest extends UnitTest
{
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
    }

    protected function getPackageProviders($app): array
    {
        return [
            SupportServiceProvider::class,
        ];
    }

    public function test_query_builder_where_like_uses_wrapped_identifier_and_binding(): void
    {
        $query = DB::table('users')->whereLike('name', 'John Doe');
        $expectedFragment = sprintf('LOWER(%s) LIKE ?', $query->getGrammar()->wrap('name'));

        $this->assertStringContainsString($expectedFragment, $query->toSql());
        $this->assertContains('%john doe%', $query->getBindings());
        $this->assertStringNotContainsString('%john doe%', $query->toSql());
    }

    public function test_eloquent_where_like_direct_column_uses_wrapped_identifier_and_binding(): void
    {
        $query = WhereLikeSecurityUser::query()->whereLike('name', 'John Doe');
        $grammar = $query->getQuery()->getGrammar();
        $expectedFragment = sprintf(
            'LOWER(%s.%s) LIKE ?',
            $grammar->wrap($query->getModel()->getTable()),
            $grammar->wrap('name')
        );

        $this->assertStringContainsString($expectedFragment, $query->toSql());
        $this->assertContains('%john doe%', $query->getBindings());
        $this->assertStringNotContainsString('%john doe%', $query->toSql());
    }

    public function test_eloquent_where_like_relation_column_uses_wrapped_identifier_and_binding(): void
    {
        $query = WhereLikeSecurityUser::query()->whereLike('profile.bio', 'John Doe');
        $expectedFragment = sprintf('LOWER(%s) LIKE ?', $query->getQuery()->getGrammar()->wrap('bio'));

        $this->assertStringContainsString($expectedFragment, $query->toSql());
        $this->assertContains('%john doe%', $query->getBindings());
        $this->assertStringNotContainsString('%john doe%', $query->toSql());
    }
}

class WhereLikeSecurityUser extends Model
{
    protected $table = 'users';
    public $timestamps = false;

    public function profile()
    {
        return $this->hasOne(WhereLikeSecurityProfile::class, 'user_id');
    }
}

class WhereLikeSecurityProfile extends Model
{
    protected $table = 'profiles';
    public $timestamps = false;
}
