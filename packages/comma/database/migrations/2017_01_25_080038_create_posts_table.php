<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->unsignedBigInteger('author_id');
            $table->string('type', 255)->default(config('laravolt.comma.default_type'));
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
