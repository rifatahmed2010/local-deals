<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\District;
use App\Models\Division;
use App\Models\Union;
use App\Models\Upazila;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AdminAreaData extends Seeder
{
    /**
     * Run the database seeds.
     * php artisan db:seed --class=AdminAreaData
     */
    public function run(): void
    {
        $division_json = File::get(base_path('database/seeders/division_data.json'));
        $divisions = json_decode($division_json);
        foreach ($divisions as $division){
            $div = new Division();
            $div->name = $division->division_name_eng;
            $div->code = $division->bbs_code;
            $div->save();
        }

        $district_json = File::get(base_path('database/seeders/district_data.json'));
        $districts = json_decode($district_json);
        foreach ($districts as $district){
            $dis = new District();
            $dis->name = $district->district_name_eng;
            $dis->code = $district->district_bbs_code;
            $dis->latitude = $district->latitude;
            $dis->longitude = $district->longitude;
            $dis->division_id = Division::where('code' , '=', $district->division_bbs_code)->first()->id;
            $dis->save();
        }

        $upazila_json = File::get(base_path('database/seeders/upazila_data.json'));
        $upazilas = json_decode($upazila_json);
        foreach ($upazilas as $upazila){
            $uz = new Upazila();
            $uz->name = $upazila->upazila_name;
            $uz->code = $upazila->upazila_code;
            $uz->district_id = District::where('code', '=', $upazila->district_code)->first()->id;
            $uz->save();
        }

        $union_json = File::get(base_path('database/seeders/union_data.json'));
        $unions = json_decode($union_json);
        foreach ($unions as $union){
            $un = new Union();
            $un->name = $union->union_name;
            $un->code = $union->union_code;
            $un->upazila_id = Upazila::where('code', '=', $union->upazila_code)->first()->id;
            $un->save();
        }

        $dhaka_north_json = File::get(base_path('database/seeders/dhaka_north_city.json'));
        $dhaka_norths = json_decode($dhaka_north_json);
        $dhaka_south_json = File::get(base_path('database/seeders/dhaka_south_city.json'));
        $dhaka_souths = json_decode($dhaka_south_json);
        $chittagong_json = File::get(base_path('database/seeders/chittagong_city.json'));
        $chittagong_cities = json_decode($chittagong_json);
        foreach ($dhaka_norths as $dhaka_north){
            $city = new City();
            //print_r($dhaka_north);exit;
            $city->city = $dhaka_north->City;
            $city->thana = $dhaka_north->Thana;
            $city->ward = $dhaka_north->Ward;
            $city->ward_code = $dhaka_north->ward_code;
            $city->area = $dhaka_north->area;
            $city->pop = $dhaka_north->pop;
            $city->save();
        }

        foreach ($dhaka_souths as $dhaka_south){
            $city = new City();
            $city->city = $dhaka_south->City;
            $city->thana = $dhaka_south->Thana;
            $city->ward = $dhaka_south->Ward;
            $city->ward_code = $dhaka_south->ward_code;
            $city->area = $dhaka_south->area;
            $city->pop = $dhaka_south->pop;
            $city->save();
        }

        foreach ($chittagong_cities as $chittagong_city){
            $city = new City();
            print_r($chittagong_city);exit;
            $city->city = $chittagong_city->city;
            $city->thana = $chittagong_city->Thana;
            $city->ward = $chittagong_city->Ward;
            $city->ward_code = $chittagong_city->ard_code;
            $city->area = $chittagong_city->area;
            $city->pop = $chittagong_city->pop;
            $city->save();
        }
    }
}
