<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table( 'users' )->insert([
            'name'          => 'Admin Admin',
            'email_address' => 'admin@email.com',
            'role'          => 'Administrator',
            'password'      => bcrypt( 'admin' ),
            'created_at'    => Carbon::now()->format( 'Y-m-d H:i:s' ),
        ]);
    }
}
