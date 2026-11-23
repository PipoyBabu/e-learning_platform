<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

class MemberLocationSeeder extends Seeder
{
    public static function locations(): array
    {
        return [
            ['address' => 'Far Eastern University Institute of Technology, P. Paredes St., Sampaloc, Manila 1008', 'latitude' => 14.6041377, 'longitude' => 120.9885516],
            ['address' => 'Diliman, Quezon City, Metro Manila', 'latitude' => 14.6507, 'longitude' => 121.0494],
            ['address' => 'Ayala Center, Makati City, Metro Manila', 'latitude' => 14.5547, 'longitude' => 121.0244],
            ['address' => 'Ortigas Center, Pasig City, Metro Manila', 'latitude' => 14.5869, 'longitude' => 121.0614],
            ['address' => 'Bonifacio Global City, Taguig, Metro Manila', 'latitude' => 14.5547, 'longitude' => 121.0509],
            ['address' => 'Intramuros, Manila, Metro Manila', 'latitude' => 14.5896, 'longitude' => 120.9747],
            ['address' => 'Mandaluyong City, Metro Manila', 'latitude' => 14.5794, 'longitude' => 121.0359],
            ['address' => 'Pasay City, Metro Manila', 'latitude' => 14.5378, 'longitude' => 120.9896],
            ['address' => 'Parañaque City, Metro Manila', 'latitude' => 14.4793, 'longitude' => 121.0198],
            ['address' => 'Caloocan City, Metro Manila', 'latitude' => 14.6577, 'longitude' => 120.9830],
            ['address' => 'Marikina City, Metro Manila', 'latitude' => 14.6507, 'longitude' => 121.1029],
            ['address' => 'Muntinlupa City, Metro Manila', 'latitude' => 14.4081, 'longitude' => 121.0415],
        ];
    }

    public function run(): void
    {
        $locations = self::locations();

        Member::query()->orderBy('id')->get()->each(function (Member $member, int $index) use ($locations): void {
            $member->update($locations[$index % count($locations)]);
        });
    }
}
