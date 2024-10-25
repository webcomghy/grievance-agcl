<?php

namespace App\Imports;

use App\Models\Holiday;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class HolidaysImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Start a database transaction
        DB::beginTransaction();
        try {
            // Assuming the Excel file has 'name' and 'date' columns
            $holiday = Holiday::updateOrCreate(
                [
                    'name' => $row['name'], // Attributes to check for existing record
                    'date' => \Carbon\Carbon::createFromFormat('d-m-Y', $row['date'])->format('Y-m-d'), // Convert to a string format
                ],
                [
                    'status' => 'Active', // Set default status to Active
                ]
            );

            // Commit the transaction
            DB::commit();
            return $holiday; // Return the created or updated holiday
        } catch (\Exception $e) {
            // Rollback the transaction if something went wrong
            DB::rollBack();
            // Optionally, you can log the error or handle it as needed
            throw $e; // Rethrow the exception for further handling
        }
    }
}
