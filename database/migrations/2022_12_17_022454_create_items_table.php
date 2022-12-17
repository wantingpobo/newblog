<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('title', 20);
            $table->string('pic', 255)->nullable();
            $table->integer('price')->default(0);
            $table->boolean('enabled')->default(true);
            $table->text('desc');
            $table->timestamp('enabled_at')->nullable();
            $table->foreignId('cgy_id')->constrained();

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
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['cgy_id']);

        });

        Schema::dropIfExists('items');
    }
};