<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    public function run() {
        DB::table( 'roles' )->insert([
            'name'          => 'Administrator',
            'description'   => 'super user',
            'created_at'    => Carbon::now()->format( 'Y-m-d H:i:s' ),
        ]);
    }
}
