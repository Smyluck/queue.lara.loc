<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Jobs\ProcessEvent;
use OpenApi\Attributes as OA;

class EventController extends Controller
{
    #[OA\Post(
        path: '/api/events',
        summary: 'Queue event sending',
        tags: ['Events'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['event', 'data'],
                properties: [
                    new OA\Property(property: 'event', type: 'string', example: 'user created'),
                    new OA\Property(
                        property: 'data',
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'user_id', type: 'integer', example: 1),
                            new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                        ],
                        required: ['user_id', 'email']
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Queued',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Событие отправлено в очередь!'),
                    ]
                )
            ),
        ]
    )]
    public function sendEvent(EventRequest $eventRequest)
    {
        ProcessEvent::dispatch($eventRequest->all());

        return response()->json(['message' => 'Событие отправлено в очередь!']);
    }
}
