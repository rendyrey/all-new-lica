<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// use \DB;

class CreateTestPreAnalyticsView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE VIEW test_pre_analytics_view AS
            select CONCAT('s',tests.id) as unique_id, tests.id as id, tests.name, p.price, p.class, 'single' as 'type'
            from tests
            left join prices p on tests.id = p.test_id
            union
            select CONCAT('p',packages.id) as unique_id, packages.id as id, packages.name,p.price,p.class, 'package' as 'type'
            from packages
            left join prices p on packages.id = p.package_id
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW test_pre_analytics_view");
    }
}
