<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('reference_documents', function (Blueprint $table) {
            $table->string('file_id')->nullable()->after('file_path'); 
            $table->string('google_drive_link')->nullable()->after('file_id'); 
        });
    }

    public function down()
    {
        Schema::table('reference_documents', function (Blueprint $table) {
            $table->dropColumn(['file_id', 'google_drive_link']);
        });
    }
};
