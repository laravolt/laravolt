<?php

declare(strict_types=1);

namespace Laravolt\Tests\Feature\SemanticForm;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Testing\TestResponse;
use Laravolt\SemanticForm\DbProxy;
use Laravolt\Tests\PackageTest;

class DropdownProxyTest extends PackageTest
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The package test app does not boot the semantic-form provider, so
        // register its dropdown route the same way packages/semantic-form/routes/web.php does.
        Route::middleware('web')->post('laravolt/api/dropdown', DbProxy::class);

        Schema::create('dropdown_people', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('name');
        });

        DB::table('dropdown_people')->insert([['name' => "O'Brien"], ['name' => 'Siti'], ['name' => 'Budi']]);
    }

    public function test_it_returns_the_options_that_match_the_term(): void
    {
        $this->search('iti')
            ->assertOk()
            ->assertJson(['success' => true, 'results' => [['name' => 'Siti']]]);
    }

    public function test_quotes_in_the_term_are_searched_as_text(): void
    {
        $this->search("O'Bri")
            ->assertOk()
            ->assertJson(['success' => true, 'results' => [['name' => "O'Brien"]]]);
    }

    public function test_database_errors_are_not_sent_to_the_browser(): void
    {
        $response = $this->search('x', "select id, name from dropdown_missing where name like '%%%s%%'");

        $response->assertOk()->assertJson(['success' => false]);
        $this->assertStringNotContainsString('dropdown_missing', (string) $response->getContent());
    }

    private function search(string $term, string $query = "select id, name from dropdown_people where name like '%%%s%%' order by id"): TestResponse
    {
        return $this->withoutMiddleware(ValidateCsrfToken::class)->postJson('/laravolt/api/dropdown', [
            'term' => $term,
            'payload' => encrypt([
                'query_key_column' => 'id',
                'query_display_column' => 'name',
                'query' => $query,
                'connection' => null,
            ]),
        ]);
    }
}
