<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('reviews_content', function (Blueprint $table) {
            $table->text('review_images')->nullable();
            $table->string('summary', 255)->nullable()->change();
            $table->text('review')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('reviews_content', function (Blueprint $table) {
            $table->dropColumn('review_images');
        });
    }
}
