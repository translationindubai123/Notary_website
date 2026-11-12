<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $now = now();

        foreach ([
            'site_name' => 'Al Syed Documents Clearing Services',
            'site_phone' => '+971 55 625 1709',
            'site_whatsapp' => '+971 55 625 1709',
            'site_address' => 'Almas Tower - Dubai Lake Avenue - Street 66 - Dubai - United Arab Emirates',
        ] as $key => $value) {
            DB::table('site_settings')
                ->where('setting_key', $key)
                ->update(['setting_value' => $value, 'updated_at' => $now]);
        }
    }

    public function down()
    {
        // Keep the updated business contact details when rolling back.
    }
};
