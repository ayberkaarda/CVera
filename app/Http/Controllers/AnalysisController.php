<?php

namespace App\Http\Controllers;

use App\Services\AtsAnalyzer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AnalysisController extends Controller
{
    public function __invoke(Request $request, AtsAnalyzer $analyzer): JsonResponse
    {
        $data = $request->validate(['cv' => 'required|string|min:80|max:60000', 'job' => 'nullable|string|min:30|max:30000']);

        return response()->json($analyzer->analyze($data['cv'], $data['job'] ?? null))->header('Cache-Control', 'no-store, private');
    }
}
