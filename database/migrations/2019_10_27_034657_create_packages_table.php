<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('disp_order')->unsigned();
            $table->string('pack_id');
            $table->string('pack_name');
            $table->string('pack_description');
            $table->string('pack_type');
            $table->integer('total_credit')->unsigned();
            $table->string('tag_name');
            $table->integer('validity_month')->unsigned();
            $table->decimal('pack_price', 25, 6)->default(0);
            $table->boolean('newbie_first_attend');
            $table->integer('newbie_addition_credit')->unsigned();
            $table->string('newbie_note');
            $table->string('pack_alias');
            $table->decimal('estimate_price', 25, 6)->default(0);
            $table->tinyInteger('status')->unsigned();
            $table->integer('created_by')->unsigned();
            $table->integer('last_updated_by')->unsigned();
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
        Schema::dropIfExists('packages');
    }
}
