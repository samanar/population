<?php

namespace App\Http\Controllers;

use App\Imports\PopulationImport;
use App\Population;
use Maatwebsite\Excel\Facades\Excel;

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

}
