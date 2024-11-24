<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class ConverterSetController extends Controller
{
    public function convertPriceList(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:csv,xlsx',
            ]);

            $file = $request->file('file');
            $filePath = $file->getPathname();

            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $columns = $worksheet->getRowIterator(1, 1)->current()->getCellIterator();

            $columnNames = [];
            foreach ($columns as $column) {
                $columnNames[] = $column->getValue();
            }

            return response()->json($columnNames);
        } catch (\Exception $e) {
            Log::error('Error in convertPriceList: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while processing the file.'], 500);
        }
    }
}