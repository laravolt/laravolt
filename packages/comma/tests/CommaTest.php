<?php

namespace Laravolt\Comma\Tests;

use Laravolt\Comma\Models\Category;
use Laravolt\Comma\Models\Post;

class CommaTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_created()
    {
        $author = $this->createUser();

        app('laravolt.comma')->makePost($author, 'New Post', 'Hello world', 'category 1');

        $this->assertDatabaseHas(app(Post::class)->getTable(), [
            'title'       => 'New Post',
            'content'     => 'Hello world',
            'author_id'   => 1,
            'category_id' => 1,
        ]);
    }

    /**
     * @test
     */
    public function it_can_generate_slug()
    {
        $author = $this->createUser();

        app('laravolt.comma')->makePost($author, 'New Post', 'Hello world', 'category 1');

        $this->assertDatabaseHas(app(Post::class)->getTable(), [
            'slug' => 'new-post',
        ]);
    }

    /**
     * @test
     */
    public function it_can_generate_unique_slug()
    {
        $author = $this->createUser();

        app('laravolt.comma')->makePost($author, 'New Post', 'Hello world', 'category 1');
        app('laravolt.comma')->makePost($author, 'New Post', 'Hello world again', 'category 1');

        $this->assertDatabaseHas(app(Post::class)->getTable(), [
            'slug' => 'new-post',
        ]);

        $this->assertDatabaseHas(app(Post::class)->getTable(), [
            'slug' => 'new-post-1',
        ]);
    }

    /**
     * @test
     */
    public function it_can_automatically_create_category()
    {
        $author = $this->createUser();

        app('laravolt.comma')->makePost($author, 'New Post', 'Hello world', 'category 1');

        $this->assertDatabaseHas(app(Category::class)->getTable(), [
            'name' => 'category 1',
            'slug' => 'category-1',
        ]);
    }

    /**
     * @test
     */
    public function it_can_be_created_with_tag()
    {
        $author = $this->createUser();
        $tags = ['php', 'laravel'];

        $post = app('laravolt.comma')->makePost($author, 'New Post', 'Hello world', 'category 1', $tags);

        $this->assertCount(2, $post->tags);
    }

    /**
     * @test
     */
    public function it_can_be_created_with_default_type()
    {
        $author = $this->createUser();
        $this->app['config']->set('laravolt.comma.default_type', 'default');

        app('laravolt.comma')->makePost($author, 'New Post', 'Hello world', 'category 1');

        $this->assertDatabaseHas(app(Post::class)->getTable(), [
            'title' => 'New Post',
            'type'  => 'default',
        ]);
    }

    /**
     * @test
     */
    public function it_can_be_created_with_custom_type()
    {
        $author = $this->createUser();
        $type = 'faq';

        app('laravolt.comma')->makePost($author, 'New Post', 'Hello world', 'category 1', null, $type);

        $this->assertDatabaseHas(app(Post::class)->getTable(), [
            'title' => 'New Post',
            'type'  => 'faq',
        ]);
    }

}
