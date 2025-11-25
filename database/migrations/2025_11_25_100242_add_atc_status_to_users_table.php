<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active_atc')->default(false)->after('token_expires');
            $table->boolean('is_visiting_atc')->default(false)->after('is_active_atc');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_active_atc', 'is_visiting_atc']);
        });
    }
};
