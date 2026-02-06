<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DatabaseExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $sheets = [];
        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $columnName = "Tables_in_" . $dbName;

        foreach ($tables as $table) {
            $tableName = $table->$columnName ?? current((array)$table);

            // Skip system or internal tables if desired
            if (in_array($tableName, ['migrations', 'failed_jobs', 'jobs', 'cache', 'cache_locks', 'sessions', 'job_batches'])) {
                continue;
            }

            $sheets[] = new TableSheet($tableName);
        }

        return $sheets;
    }
}

class TableSheet implements FromCollection, WithTitle, WithHeadings
{
    protected $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function collection()
    {
        return DB::table($this->table)->get();
    }

    public function title(): string
    {
        return ucfirst(str_replace('_', ' ', $this->table));
    }

    public function headings(): array
    {
        $first = DB::table($this->table)->first();
        if ($first) {
            return array_keys((array)$first);
        }
        return [];
    }
}
