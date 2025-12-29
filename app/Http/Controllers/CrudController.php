<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Crud;

class CrudController extends Controller
{
    // =============================
    // PAGES (BLADE)
    // =============================
    public function indexPage()
    {
        return view('angular.index');
    }

    public function createPage()
    {
        return view('angular.create');
    }

    public function editPage()
    {
        return view('angular.edit');
    }

    // =============================
    // DATA APIs
    // =============================
    public function list()
    {
        return Crud::orderBy('id', 'asc')->get();
    }

    public function store(Request $request)
    {
        Crud::create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return ['status' => true];
    }

    public function show($id)
    {
        return Crud::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        Crud::findOrFail($id)->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return ['status' => true];
    }

    public function destroy($id)
    {
        Crud::findOrFail($id)->delete();
        return ['status' => true];
    }

    // =============================
    // EXPORT CSV
    // =============================
    public function export()
    {
        $filename = "crud_export_" . date('Y-m-d_H-i-s') . ".csv";
        $records = Crud::orderBy('id', 'asc')->get();

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Title', 'Description']);

            foreach ($records as $row) {
                fputcsv($file, [
                    $row->id,
                    $row->title,
                    $row->description,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // =============================
    // IMPORT CSV
    // =============================
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        $rowNumber = 0;
        $inserted = 0;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {

            if ($rowNumber === 0) {
                $rowNumber++;
                continue;
            }

            if (!isset($row[1]) || !isset($row[2])) {
                continue;
            }

            Crud::create([
                'title' => trim($row[1]),
                'description' => trim($row[2]),
            ]);

            $inserted++;
        }

        fclose($handle);

        return response()->json([
            'status' => true,
            'inserted' => $inserted
        ]);
    }
}
