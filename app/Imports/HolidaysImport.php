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
            $holiday = new Holiday([
                'name' => $row['name'], // Use the header name to map the value
                'date' => \Carbon\Carbon::createFromFormat('d-m-Y', $row['date'])->format('Y-m-d'), // Convert to a string format
                'status' => 'Active', // Set default status to Active
            ]);
            
            $holiday->save(); // Save the holiday record

            // Commit the transaction
            DB::commit();
            return $holiday; // Return the created holiday
        } catch (\Exception $e) {
            // Rollback the transaction if something went wrong
            DB::rollBack();
            // Optionally, you can log the error or handle it as needed
            throw $e; // Rethrow the exception for further handling
        }
    }
}
