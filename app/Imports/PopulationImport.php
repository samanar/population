<?php

namespace App\Imports;

use App\Population;
use Maatwebsite\Excel\Concerns\ToModel;

class PopulationImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        // checks if data is csv header while trying to input data
        // if row is header then dont insert
        if ($row['0'] == 'نام') {
            return null;
        }
     
        // adds data to the database
        // converts them to default format
        return (new Population([
            'province' => persianConvert($row['0'] , ' '),
            'section_1' => $row['1'] ? persianConvert($row['1'], ' ') : null,
            'section_2' => $row['2'] ? persianConvert($row['2'], ' ') : null,
            'urban' => $row['3'] ? persianConvert($row['3'], ' ') : null,
            'rural' => $row['4'] ? persianConvert($row['4'], ' ') : null,
            'population' => numberConvert($row['5']),
        ]));
    }

    // for reding from csv files in batch mode
    public function batchSize(): int
    {
        return 500;
    }

    // for inserting to database in batch mode
    public function chunkSize(): int
    {
        return 500;
    }
}
