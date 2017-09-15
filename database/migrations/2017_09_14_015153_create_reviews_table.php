<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::beginTransaction();
        Schema::create('reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->char('page_id',32)->index('page_id');
            $table->char('appid',32)->index('appid');
            $table->string('target_id')->nullable();
            $table->string('target_sku')->nullable();
            $table->tinyInteger('type')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('score')->default(0);
            $table->tinyInteger('is_attr')->default(0);
            $table->timestamps();
        });
        Schema::create('reviews_content', function (Blueprint $table) {
            $table->integer('review_id')->primary('review_id');
            $table->string('nickname',50);
            $table->string('email',50)->nullable();
            $table->string('summary',100)->nullable();
            $table->string('review',500);
            $table->string('reply',500)->nullable();
            $table->string('page_url',200);
            $table->string('user',100)->nullable();
            $table->string('ip',15);
            $table->timestamps();
        });
        Schema::create('reviews_attr', function (Blueprint $table) {
            $table->integer('review_id')->index('review_id');
            $table->integer('attr_id');
        });
        DB::commit();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('reviews_content');
        Schema::dropIfExists('reviews_attr');
    }
}
