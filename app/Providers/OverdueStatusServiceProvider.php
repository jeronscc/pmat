<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB; // ✅ Make sure this is imported

class OverdueStatusServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        //
    }
    
    public function boot()
    {
        // Check if DB connection is available before querying
        if (app()->bound('db')) {
            DB::connection('ilcdb')->table('procurement_form')
                ->whereRaw("
                    (dt_received1 IS NULL AND dt_submitted1 IS NOT NULL AND NOW() > DATE_ADD(dt_submitted1, INTERVAL 1 DAY))
                    OR (dt_received2 IS NULL AND dt_submitted2 IS NOT NULL AND NOW() > DATE_ADD(dt_submitted2, INTERVAL 1 DAY))
                    OR (dt_received3 IS NULL AND dt_submitted3 IS NOT NULL AND NOW() > DATE_ADD(dt_submitted3, INTERVAL 1 DAY))
                    OR (dt_received4 IS NULL AND dt_submitted4 IS NOT NULL AND NOW() > DATE_ADD(dt_submitted4, INTERVAL 1 DAY))
                    OR (dt_received5 IS NULL AND dt_submitted5 IS NOT NULL AND NOW() > DATE_ADD(dt_submitted5, INTERVAL 1 DAY))
                    OR (dt_received6 IS NULL AND dt_submitted6 IS NOT NULL AND NOW() > DATE_ADD(dt_submitted6, INTERVAL 1 DAY))
                ")
                ->update(['status' => 'Overdue']);
        }

        if (app()->bound('db')) {
            DB::connection('ilcdb')->table('honoraria_form')
                ->whereRaw("
                    (dt_received IS NULL AND dt_submitted IS NOT NULL AND NOW() > DATE_ADD(dt_submitted, INTERVAL 1 DAY))
                ")
                ->update(['status' => 'Overdue']);
        }

        if (app()->bound('db')) {
            DB::connection('ilcdb')->table('otherexpense_form')
                ->whereRaw("
                    (dt_received IS NULL AND dt_submitted IS NOT NULL AND NOW() > DATE_ADD(dt_submitted, INTERVAL 1 DAY))
                ")
                ->update(['status' => 'Overdue']);
        }
    }
    
}

