<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendReportRequest;
use Illuminate\Http\Request;
use App\Jobs\SendReport;

class ReportController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(SendReportRequest $request)
    {
        $email = $request->input('email');
        $text = $request->input('text');

        SendReport::dispatch($email, $text);
        return response()->json(['status' => 'queued']);
    }
}
