<?php

use Illuminate\Database\Seeder;
use App\Essential\Models\Staff;

class StaffsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            [
                'name'     => 'root',
                'realname' => '总管理员',
                'password' => app('hash')->make('elemenx'),
            ]
        ];

        foreach ($items as $item) {
            $staff = Staff::create($item);
        }
    }
}
