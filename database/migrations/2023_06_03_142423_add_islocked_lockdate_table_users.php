<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIslockedLockdateTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->longText('tokenvalidation')->nullable()->after('password');
            $table->boolean('islocked')->after('remember_token')->default(false)->nullable();
            $table->dateTime('lock_date')->after('islocked')->nullable();
            $table->integer('attempts')->after('lock_date')->default(0)->nullable();
            $table->timestamp('last_access')->after('attempts')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('islocked');
            $table->dropColumn('lock_date');
            $table->dropColumn('attempts');
            $table->dropColumn('last_access');
            $table->dropColumn('tokenvalidation');
        });
    }
}
