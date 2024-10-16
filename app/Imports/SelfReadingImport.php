<?php

namespace App\Imports;

use App\Models\SelfReading;
use Maatwebsite\Excel\Concerns\ToModel;

class SelfReadingImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Skip the heading row
        if ($row[0] === 'ID') {
            return null; 
        }

        return new SelfReading([
            'ID' => $row[0],
            'CA_NO' => $row[1],
            'Reading' => $row[2],
            'MM' => $row[3],
            'YYYY' => $row[4],
            'Reading_Date' => $row[5],
            'Status' => $row[6],
        ]);
    }
}
