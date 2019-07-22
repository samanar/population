<?php

namespace App\Http\Controllers;

use App\Imports\PopulationImport;
use App\Population;
use Maatwebsite\Excel\Facades\Excel;
use App\IranCity;
use DB;

class PopulationController extends Controller
{
    public function import()
    {
        Excel::import(new PopulationImport, '/data/1.csv');
        Excel::import(new PopulationImport, '/data/2.csv');
        Excel::import(new PopulationImport, '/data/3.csv');
        Excel::import(new PopulationImport, '/data/4.csv');
        Excel::import(new PopulationImport, '/data/5.csv');
        Excel::import(new PopulationImport, '/data/6.csv');
        Excel::import(new PopulationImport, '/data/7.csv');
        Excel::import(new PopulationImport, '/data/8.csv');
        Excel::import(new PopulationImport, '/data/9.csv');
        Excel::import(new PopulationImport, '/data/10.csv');
        Excel::import(new PopulationImport, '/data/11.csv');
        Excel::import(new PopulationImport, '/data/12.csv');
        Excel::import(new PopulationImport, '/data/13.csv');
        Excel::import(new PopulationImport, '/data/14.csv');
        Excel::import(new PopulationImport, '/data/15.csv');
        Excel::import(new PopulationImport, '/data/16.csv');
        Excel::import(new PopulationImport, '/data/17.csv');
        Excel::import(new PopulationImport, '/data/18.csv');
        Excel::import(new PopulationImport, '/data/19.csv');
        Excel::import(new PopulationImport, '/data/20.csv');
        Excel::import(new PopulationImport, '/data/21.csv');
        Excel::import(new PopulationImport, '/data/22.csv');
        Excel::import(new PopulationImport, '/data/23.csv');
        Excel::import(new PopulationImport, '/data/24.csv');
        Excel::import(new PopulationImport, '/data/25.csv');
        Excel::import(new PopulationImport, '/data/26.csv');
        Excel::import(new PopulationImport, '/data/27.csv');
        Excel::import(new PopulationImport, '/data/28.csv');
        Excel::import(new PopulationImport, '/data/29.csv');
        Excel::import(new PopulationImport, '/data/30.csv');
        Excel::import(new PopulationImport, '/data/31.csv');
        return 'done';
    }

    public function convert()
    {
        Population::chunk(250, function ($items) {
            foreach ($items as $item) {
                $item->province = \persianConvert($item->province, ' ');
                $item->section_1 = \persianConvert($item->section_1, ' ');
                $item->section_2 = \persianConvert($item->section_2, ' ');
                $item->urban = \persianConvert($item->urban, ' ');
                $item->rural = \persianConvert($item->rural, ' ');
                $item->population = \numberConvert($item->population);
                $item->save();
            }
        });

        return 'converted';
    }

    public function addCitiesToDadsun()
    {
        $all = IranCity::where('type', '!=', 'استان')->get();
        foreach ($all as $item) {
            switch ($item->type) {
                case 'شهرستان':
                    $province = IranCity::find($item->parent_id);
                    if (!$this->findCityInDadsun($item->name)) {
                        $dadsun_state_id = $this->getDadsunProvinceId($province->name);
                        if ($dadsun_state_id != 0) {
                            DB::table('tbs_city')->insert([
                                'type' => 'جدید',
                                'city_name' => $item->name,
                                'state_id' => $dadsun_state_id
                            ]);
                        } else {
                            logger('استان مربوط به شهرستان یافت شند ');
                            logger($item->name);
                        }
                    }
                    break;
                case 'منطقه':
                    $city = IranCity::find($item->parent_id);
                    $province = IranCity::find($city->parent_id);
                    if (!$this->findCityInDadsun($item->name)) {
                        $dadsun_state_id = $this->getDadsunProvinceId($province->name);
                        if ($dadsun_state_id != 0) {
                            DB::table('tbs_city')->insert([
                                'type' => 'جدید',
                                'city_name' => $item->name,
                                'state_id' => $dadsun_state_id
                            ]);
                        } else {
                            logger('استان مربوط به شهرستان یافت شند ');
                            logger($item->name);
                        }
                    }
                    break;
            }
        }
        $this->findDuplicates();
        return 'done';
    }

    private function findCityInDadsun($cityName)
    {
        $city = DB::table('tbs_city')->where('city_name', persianConvert($cityName, ' '))->first();
        if ($city) return true;
        return false;
    }

    private function getDadsunProvinceId($province_name)
    {
        $province_name = str_replace(' ', '%', $province_name);
        $province = DB::table('tbs_state')->where('state_name', 'like', persianConvert($province_name, ' '))->first();
        if (!$province) {
            logger('province not found ');
            logger($province_name);
            return 0;
        }

        return $province->state_id;
    }

    private function findDuplicates()
    {
        $moved_to_cities = DB::table('tbs_city')->whereType('جابه جایی به شهرستان')->get();
        $moved_to_sections = DB::table('tbs_city')->whereType('جابه جایی به منطقه')->get();
        foreach ($moved_to_cities as $item) {
            $name = $this->getNewName($item->meta);
            $new_added = DB::table('tbs_city')->whereType('جدید')->where('city_name', $name)->first();
            if ($new_added) {
                $this->updateDuplicate($new_added->city_id);
            }
        }
        foreach ($moved_to_sections as $item) {
            $name = $this->getNewName($item->meta);
            $new_added = DB::table('tbs_city')->whereType('جدید')->where('city_name', $name)->first();
            if ($new_added) {
                $this->updateDuplicate($new_added->city_id);
            }
        }
    }

    private function getNewName($meta)
    {
        $parts = explode(':', $meta);
        return $parts[count($parts) - 1];
    }
    
    private function updateDuplicate($city_id)
    {
        DB::table('tbs_city')->where('city_id', $city_id)->update([
            'type' => 'حذف'
        ]);
    }
}
