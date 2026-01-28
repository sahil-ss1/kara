<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CurrenciesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('currencies')->delete();
        
        \DB::table('currencies')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'US Dollar',
                'code' => 'USD',
                'symbol' => '$',
                'format' => '$1,0.00',
                'exchange_rate' => '0',
                'active' => 1,
                'created_at' => '2022-11-26 22:37:16',
                'updated_at' => '2022-11-26 22:37:16',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
                'format' => '1.0,00 €',
                'exchange_rate' => '0',
                'active' => 1,
                'created_at' => '2022-11-26 22:37:21',
                'updated_at' => '2022-11-26 22:37:21',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'UAE Dirham',
                'code' => 'AED',
                'symbol' => 'دإ‏',
                'format' => 'دإ‏ 1,0.00',
                'exchange_rate' => '0',
                'active' => 0,
                'created_at' => '2022-11-26 22:39:07',
                'updated_at' => '2022-11-26 22:39:07',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Afghanistan, Afghani',
                'code' => 'AFN',
                'symbol' => '؋',
                'format' => '؋1,0.00',
                'exchange_rate' => '0',
                'active' => 0,
                'created_at' => '2022-11-26 22:39:14',
                'updated_at' => '2022-11-26 22:39:14',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Albania, Lek',
                'code' => 'ALL',
                'symbol' => 'Lek',
                'format' => '1,0.00Lek',
                'exchange_rate' => '0',
                'active' => 0,
                'created_at' => '2022-11-26 22:39:19',
                'updated_at' => '2022-11-26 22:39:19',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Bosnia and Herzegovina, Convertible Marks',
                'code' => 'BAM',
                'symbol' => 'КМ',
                'format' => '1,0.00 КМ',
                'exchange_rate' => '0',
                'active' => 0,
                'created_at' => '2022-11-26 22:39:24',
                'updated_at' => '2022-11-26 22:39:24',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Barbados Dollar',
                'code' => 'BBD',
                'symbol' => '$',
                'format' => '$1,0.00',
                'exchange_rate' => '0',
                'active' => 0,
                'created_at' => '2022-11-26 22:39:29',
                'updated_at' => '2022-11-26 22:39:29',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Bangladesh, Taka',
                'code' => 'BDT',
                'symbol' => '৳',
                'format' => '৳ 1,0.',
                'exchange_rate' => '0',
                'active' => 0,
                'created_at' => '2022-11-26 22:39:34',
                'updated_at' => '2022-11-26 22:39:34',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Armenian Dram',
                'code' => 'AMD',
                'symbol' => '&#1423;',
                'format' => '1,0.00 &#1423;',
                'exchange_rate' => '0',
                'active' => 0,
                'created_at' => '2022-11-26 22:39:40',
                'updated_at' => '2022-11-26 22:39:40',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'Netherlands Antillian Guilder',
                'code' => 'ANG',
                'symbol' => 'ƒ',
                'format' => 'ƒ1,0.00',
                'exchange_rate' => '0',
                'active' => 0,
                'created_at' => '2022-11-26 22:39:43',
                'updated_at' => '2022-11-26 22:39:43',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'Argentine Peso',
                'code' => 'ARS',
                'symbol' => '$',
                'format' => '$ 1,0.00',
                'exchange_rate' => '0',
                'active' => 0,
                'created_at' => '2022-11-26 22:39:49',
                'updated_at' => '2022-11-26 22:39:49',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'Australian Dollar',
                'code' => 'AUD',
                'symbol' => '$',
                'format' => '$1,0.00',
                'exchange_rate' => '0',
                'active' => 0,
                'created_at' => '2022-11-26 22:39:52',
                'updated_at' => '2022-11-26 22:39:52',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'Aruban Guilder',
                'code' => 'AWG',
                'symbol' => 'ƒ',
                'format' => 'ƒ1,0.00',
                'exchange_rate' => '0',
                'active' => 0,
                'created_at' => '2022-11-26 22:39:56',
                'updated_at' => '2022-11-26 22:39:56',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'Angola, Kwanza',
                'code' => 'AOA',
                'symbol' => 'Kz',
                'format' => 'Kz1,0.00',
                'exchange_rate' => '0',
                'active' => 0,
                'created_at' => '2022-11-26 22:40:00',
                'updated_at' => '2022-11-26 22:40:00',
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'Bulgarian Lev',
                'code' => 'BGN',
                'symbol' => 'лв.',
                'format' => '1 0,00 лв.',
                'exchange_rate' => '0',
                'active' => 0,
                'created_at' => '2022-11-26 22:40:04',
                'updated_at' => '2022-11-26 22:40:04',
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'Bahraini Dinar',
                'code' => 'BHD',
                'symbol' => '.د.',
                'format' => '.د. 1,0.000',
                'exchange_rate' => '0',
                'active' => 0,
                'created_at' => '2022-11-26 22:40:10',
                'updated_at' => '2022-11-26 22:40:10',
            ),
            16 => 
            array (
                'id' => 17,
                'name' => 'Burundi Franc',
                'code' => 'BIF',
                'symbol' => 'FBu',
                'format' => '1,0.FBu',
                'exchange_rate' => '0',
                'active' => 0,
                'created_at' => '2022-11-26 22:40:15',
                'updated_at' => '2022-11-26 22:40:15',
            ),
            17 => 
            array (
                'id' => 18,
                'name' => 'Bermudian Dollar',
                'code' => 'BMD',
                'symbol' => '$',
                'format' => '$1,0.00',
                'exchange_rate' => '0',
                'active' => 0,
                'created_at' => '2022-11-26 22:40:21',
                'updated_at' => '2022-11-26 22:40:21',
            ),
            18 => 
            array (
                'id' => 19,
                'name' => 'Brunei Dollar',
                'code' => 'BND',
                'symbol' => '$',
                'format' => '$1,0.',
                'exchange_rate' => '0',
                'active' => 0,
                'created_at' => '2022-11-26 22:40:25',
                'updated_at' => '2022-11-26 22:40:25',
            ),
            19 => 
            array (
                'id' => 20,
                'name' => 'Azerbaijanian Manat',
                'code' => 'AZN',
                'symbol' => '₼',
                'format' => '1 0,00 ₼',
                'exchange_rate' => '0',
                'active' => 0,
                'created_at' => '2022-11-26 22:40:28',
                'updated_at' => '2022-11-26 22:40:28',
            ),
            20 => 
            array (
                'id' => 21,
                'name' => 'Bolivia, Boliviano',
                'code' => 'BOB',
                'symbol' => 'Bs',
                'format' => 'Bs 1,0.00',
                'exchange_rate' => '0',
                'active' => 0,
                'created_at' => '2022-11-26 22:40:31',
                'updated_at' => '2022-11-26 22:40:31',
            ),
            21 => 
            array (
                'id' => 22,
                'name' => 'Brazilian Real',
                'code' => 'BRL',
                'symbol' => 'R$',
                'format' => 'R$ 1,0.00',
                'exchange_rate' => '0',
                'active' => 0,
                'created_at' => '2022-11-26 22:40:36',
                'updated_at' => '2022-11-26 22:40:36',
            ),
            22 => 
            array (
                'id' => 23,
                'name' => 'Bahamian Dollar',
                'code' => 'BSD',
                'symbol' => '$',
                'format' => '$1,0.00',
                'exchange_rate' => '0',
                'active' => 0,
                'created_at' => '2022-11-26 22:40:39',
                'updated_at' => '2022-11-26 22:40:39',
            ),
            23 => 
            array (
                'id' => 24,
                'name' => 'Bhutan, Ngultrum',
                'code' => 'BTN',
                'symbol' => 'Nu.',
                'format' => 'Nu. 1,0.0',
                'exchange_rate' => '0',
                'active' => 0,
                'created_at' => '2022-11-26 22:40:54',
                'updated_at' => '2022-11-26 22:40:54',
            ),
            24 => 
            array (
                'id' => 25,
                'name' => 'Botswana, Pula',
                'code' => 'BWP',
                'symbol' => 'P',
                'format' => 'P1,0.00',
                'exchange_rate' => '0',
                'active' => 0,
                'created_at' => '2022-11-26 22:40:58',
                'updated_at' => '2022-11-26 22:40:58',
            ),
            25 => 
            array (
                'id' => 26,
                'name' => 'Belarussian Ruble',
                'code' => 'BYN',
                'symbol' => 'р.',
                'format' => '1 0,00 р.',
                'exchange_rate' => '0',
                'active' => 0,
                'created_at' => '2022-11-26 22:41:05',
                'updated_at' => '2022-11-26 22:41:05',
            ),
        ));
        
        
    }
}