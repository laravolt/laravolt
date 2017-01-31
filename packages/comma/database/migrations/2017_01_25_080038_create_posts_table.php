<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('author_id');
            $table->unsignedInteger('category_id');
            $table->string('type', 255)->default('blog');
            $table->string('title', 255);
            $table->string('slug', 255)->unique()->nullable();
            $table->text('content');
            $table->string('status', 40)->default('draft')->index();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->timestamp('published_at')->nullable();

            $table->foreign('author_id')
                  ->references('id')->on(app(config('auth.providers.users.model'))->getTable())
                  ->onDelete('cascade');

            $table->foreign('category_id')
                  ->references('id')->on('cms_categories')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cms_posts');
    }
}
