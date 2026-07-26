<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = [
            'BANK B J B',
            'BANK BALI',
            'BANK BENGKULU',
            'BANK D I Y',
            'BANK JAMBI',
            'BANK JATENG',
            'BANK JATIM',
            'BANK KALBAR',
            'BANK KALSEL',
            'BANK KALTENG',
            'BANK KALTIMTARA',
            'BANK LAMPUNG',
            'BANK MALUKU MALUT',
            'BANK N T B SYARIAH',
            'BANK N T T',
            'BANK NAGARI',
            'BANK PAPUA',
            'BANK RIAU KEPRI SYARIAH',
            'BANK SULSELBAR',
            'BANK SULUTGO',
            'BANK SUMSEL BABEL',
            'BANK SUMUT',
            'BATAM',
            'BINTAN',
            'Bolsel',
            'Cianjur',
            'KAMPAR',
            'KARIMUN',
            'KAYONG UTARA',
            'KENDARI',
            'KEPRI',
            'Ketapang',
            'KONAWE',
            'KUBU RAYA',
            'LINGGA',
            'MANGGARAI',
            'MANGGARAI BARAT',
            'MEMPAWAH',
            'Minahasa',
            'PAMEKASAN',
            'Pulang Pisau',
            'RIAU',
            'ROKAN HILIR',
            'Rokan Hulu',
            'Sanggau',
            'SULAWESI BARAT',
            'SUMBA TENGAH',
            'SUMBA TIMUR',
            'TANJABBAR',
            'TANJUNG PINANG',
            'WONOSOBO',
            'KEMENDAGRI',
        ];

        Region::whereNotIn('name', $regions)->delete();

        foreach ($regions as $idx => $name) {
            $code = 'REG-' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT);
            Region::updateOrCreate(
                ['name' => $name],
                [
                    'code' => $code,
                ]
            );
        }
    }
}
