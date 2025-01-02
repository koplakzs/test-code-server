<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Report::all();

        return response()->json([
            'success' => true,
            'message' => "Data berhasil didapatkan",
            'reports' => $data,
        ], 200);
    }
    public function getStatistic()
    {
        $totalReports = Report::count();

        $programs = Report::select('name', 'province')
            ->selectRaw('SUM(count) as total')
            ->groupBy('name', 'province')
            ->get();

        return response()->json([
            'success' => true,
            'totalReports' => $totalReports,
            'programs' => $programs,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $validatedData = $request->validate([
                'name' => 'required|string',
                'count' => 'required|integer',
                'province' => 'required|string',
                'district' => 'required|string',
                'subDistrict' => 'required|string',
                'date' => 'required|date',
                'proof' => 'required|mimes:jpg,png,pdf|max:2048',
                'note' => 'nullable|string',
            ]);

            if ($request->hasFile('proof')) {
                $proofName = uniqid() . '.' . $request->file('proof')->extension();
                $request->file('proof')->storeAs('public/report', $proofName);
                $validatedData['proof'] = $proofName;
            }

            $validatedData['status'] = "Pending";

            $report = Report::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Report created successfully',
                'data' => $report,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Validation',
                'error' => $th->getMessage(),
            ], 403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            $validatedData = $request->validate([
                'name' => 'required|string',
                'count' => 'required|integer',
                'province' => 'required|string',
                'district' => 'required|string',
                'subDistrict' => 'required|string',
                'date' => 'required|date',
                'proof' => 'mimes:jpg,png,pdf|max:2048',
                'note' => 'nullable|string',
            ]);

            $reportModel = Report::findOrFail($id);
            if ($request->hasFile('proof')) {
                Storage::delete('public/report/' . $reportModel->proof);
                $proofName = uniqid() . '.' . $request->file('proof')->extension();
                $request->file('proof')->storeAs('public/report', $proofName);
                $validatedData['proof'] = $proofName;
            }
            if ($reportModel->status === 'Ditolak' || $reportModel->status === 'Disetujui') {
                throw new \Exception('Report dengan status "tolak" atau "terima" tidak dapat dihapus.');
            }

            $reportModel->name = $validatedData['name'];
            $reportModel->count = $validatedData['count'];
            $reportModel->province = $validatedData['province'];
            $reportModel->district = $validatedData['district'];
            $reportModel->subDistrict = $validatedData['subDistrict'];
            $reportModel->date = $validatedData['date'];
            $reportModel->note = $validatedData['note'];

            $reportModel->save();
            return response()->json([
                'success' => true,
                'message' => 'Report update successfully',

            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'error',
                'error' => $th,
            ], 400);
        }
    }
    public function updateStatus(Request $request, string $id)
    {
        try {
            $validatedData = $request->validate([
                "status" => "required|string",
                "reason" => "nullable|string"
            ]);

            $reportModel = Report::findOrFail($id);

            $reportModel->status = $validatedData['status'];
            $reportModel->reason = $validatedData['reason'];

            $reportModel->save();
            return response()->json([
                'success' => true,
                'message' => 'Report update successfully',

            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'error',
                'error' => $th,
            ], 400);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $reportModel = Report::findOrFail($id);

            if ($reportModel->status === 'Ditolak' || $reportModel->status === 'Disetujui') {
                throw new \Exception('Report dengan status "tolak" atau "terima" tidak dapat dihapus.');
            }
            Storage::delete('public/report/' . $reportModel->proof);
            $reportModel->delete();

            return response()->json([
                'success' => true,
                'message' => 'Report deleted successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'error',
                'error' => $th->getMessage(),
            ], 400);
        }
    }
}
