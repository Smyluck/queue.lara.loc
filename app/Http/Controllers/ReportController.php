<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendReportRequest;
use Illuminate\Http\Request;
use App\Jobs\SendReport;
use OpenApi\Attributes as OA;

class ReportController extends Controller
{
    #[OA\Post(
        path: '/api/send-report',
        summary: 'Queue report sending',
        tags: ['Reports'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'text'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'text', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Queued',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'queued'),
                    ]
                )
            ),
        ]
    )]
    public function __invoke(SendReportRequest $request)
    {
        $email = $request->input('email');
        $text = $request->input('text');

        SendReport::dispatch($email, $text);
        return response()->json(['status' => 'queued']);
    }
}
