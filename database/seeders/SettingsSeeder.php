<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;


class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            ['key' => 'percent_drop_seller', 'value' => '25'],
            ['key' => 'percent_drop_buyer', 'value' => '5'],
            ['key' => 'percent_one_token_seller', 'value' => '10'],
            ['key' => 'percent_one_token_buyer', 'value' => '5'],
            ['key' => 'percent_author_receive', 'value' => '66.6'],
            ['key' => 'currency_rate_dkk', 'value' => '6.45'],
            ['key' => 'default_time_drop_line', 'value' => '120'], // seconds
            ['key' => 'default_time_drop_payment', 'value' => '1200'], // seconds
            ['key' => 'default_time_drop_payment', 'value' => '1200'], // seconds
            ['key' => 'keys_number', 'value' => '20'], // keys
        ];

        foreach ($items as $item) {
            Setting::create($item);
        }
    }
}
