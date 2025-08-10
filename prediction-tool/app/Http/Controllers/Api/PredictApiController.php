<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PredictApiController extends Controller
{
    public function predict(Request $request)
    {
        // Forward the request to Python FastAPI backend
        $apiResponse = Http::post('http://localhost:8000/predict', $request->all());

        return $apiResponse->json();
    }
}
