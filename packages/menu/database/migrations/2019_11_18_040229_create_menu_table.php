<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('label', 40)->comment('Text to be displayed in menu');
            $table->string('url', 100)->comment('Relative or absolute URL');
            $table->string('color')->nullable();
            $table->text('roles')->nullable();
            $table->string('type', 20)->default(\Laravolt\Menu\Enum\UrlType::INTERNAL)
                ->comment('Enumeration, see '.\Laravolt\Menu\Enum\UrlType::class);
            $table->string('icon')->nullable();
            $table->string('permission')->nullable();
            $table->unsignedSmallInteger('order')->default(0);
            $table->nestedSet();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu');
    }
}
