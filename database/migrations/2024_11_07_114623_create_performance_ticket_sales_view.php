<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
    CREATE VIEW performance_ticket_sales AS
    SELECT
        performances.title AS performance_title,
        DATE(tickets.created_at) AS sale_date,
        COUNT(tickets.id) AS tickets_sold
    FROM
        tickets
    JOIN
        shows ON tickets.show_id = shows.id
    JOIN
        performances ON shows.performance_id = performances.id
    WHERE
        tickets.created_at BETWEEN '2024-10-01' AND '2024-10-31'
    GROUP BY
        performances.title,
        sale_date
    ORDER BY
        performances.title,
        sale_date;
");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS performance_ticket_sales");
    }
};
