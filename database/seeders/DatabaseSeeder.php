<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PlanSeeder::class,
            BusinessCategorySeeder::class,
            BusinessSeeder::class,
            PermissionSeeder::class,
            OptionTableSeeder::class,
            UserSeeder::class,
            LanguageSeeder::class,
            CurrencySeeder::class,
            GatewaySeeder::class,
            PlanSubscribeSeeder::class,
            AdvertiseSeeder::class,
            CategorySeeder::class,
            UnitSeeder::class,
            PaymentTypeSeeder::class,
            ManufacturerSeeder::class,
            MedicineTypeSeeder::class,
            BoxSizeSeeder::class,
            TaxSeeder::class,
            ProductSeeder::class,
            StockSeeder::class,
            PartySeeder::class,
            IncomeSeeder::class,
            ExpenseSeeder::class,
            SaleSeeder::class,
            SaleReturnSeeder::class,
            PurchaseSeeder::class,
            PurchaseReturnSeeder::class,
            ProductSettingSeeder::class,
        ]);
    }
}
