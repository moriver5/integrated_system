<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(ContentsTableSeeder::class);
        $this->call(Convert_tablesTableSeeder::class);
        $this->call(Grant_pointsTableSeeder::class);
        $this->call(GroupsTableSeeder::class);
        $this->call(Magnification_settingsTableSeeder::class);
        $this->call(Mail_contentsTableSeeder::class);
        $this->call(Point_categoriesTableSeeder::class);
        $this->call(PointsSeeder::class);
    }
}
