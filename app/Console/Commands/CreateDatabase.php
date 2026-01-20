<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class CreateDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $database = $this->argument('name') 
            ?? Config::get('database.connections.mysql.database');

        // temporarily remove database name
        Config::set('database.connections.mysql.database', null);

        DB::statement("CREATE DATABASE IF NOT EXISTS `$database` 
                       CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        $this->info("Database '$database' created successfully.");
    }
}
