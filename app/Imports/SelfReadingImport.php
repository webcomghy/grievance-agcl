<?php

namespace App\Imports;

use App\Models\SelfReading;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SelfReadingImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        DB::beginTransaction();
        try {
            if ($row['ID'] === 'ID') {
                return null; 
            }

            $selfReading = SelfReading::updateOrCreate(
                [
                    'ID' => $row['ID'],
                ],
                [
                    'CA_NO' => $row['CA_NO'],
                    'Reading' => $row['Reading'],
                    'MM' => $row['MM'],
                    'YYYY' => $row['YYYY'],
                    'Reading_Date' => $row['Reading_Date'],
                    'Status' => $row['Status'],
                ]
            );

            DB::commit();
            Log::info('SelfReading imported successfully', ['ID' => $selfReading->ID]);
            return $selfReading;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to import SelfReading', [
                'error' => $e->getMessage(),
                'row' => $row,
            ]);
            throw $e; // Rethrow the exception for further handling
        }
    }
}
