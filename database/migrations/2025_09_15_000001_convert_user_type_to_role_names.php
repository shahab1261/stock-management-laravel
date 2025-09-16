<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // First, change the column type to string to allow role names
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_type')->default('Employee')->comment('Role name: SuperAdmin, Admin, Employee')->change();
        });

        // Now convert existing numeric codes to role name strings
        DB::table('users')->where('user_type', '0')->update(['user_type' => 'SuperAdmin']);
        DB::table('users')->where('user_type', '1')->update(['user_type' => 'Admin']);
        DB::table('users')->where('user_type', '2')->update(['user_type' => 'Employee']);
        DB::table('users')->where('user_type', '3')->update(['user_type' => 'Employee']); // Some might have 3 for employee
    }

    public function down(): void
    {
        // Best-effort rollback mapping back to numbers (Employee -> 2)
        DB::table('users')->where('user_type', 'SuperAdmin')->update(['user_type' => '0']);
        DB::table('users')->where('user_type', 'Admin')->update(['user_type' => '1']);
        DB::table('users')->where('user_type', 'Employee')->update(['user_type' => '2']);

        Schema::table('users', function (Blueprint $table) {
            $table->string('user_type')->default('0')->comment('0=user,1=admin,3=employee')->change();
        });
    }
};


