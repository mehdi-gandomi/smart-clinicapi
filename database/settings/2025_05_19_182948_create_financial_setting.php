<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('financial.blood_pressure_price', 20000);
        $this->migrator->add('financial.online_visit_price', 20000);
    }
};
