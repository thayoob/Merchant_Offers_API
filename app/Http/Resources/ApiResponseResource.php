<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\JsonResponse;

class ApiResponseResource extends JsonResource
{
    const ACTION_INSERT = 'insert';
    const ACTION_FETCH = 'fetch';
    const ACTION_DELETE = 'delete';
    const ACTION_UPDATE = 'update';

    public static string $Merchant = 'Merchant';
    public static string $Offer = 'Offer';
    public static string $VoucherCode = 'VoucherCode';

    public function toArray($request): array
    {
        if (!$this->resource) {
            return [];
        }

        $attributes = $this->resource->getAttributes();

        if ($request->has('fields')) {
            $fields = explode(',', $request->fields);
            $attributes = array_intersect_key($attributes, array_flip($fields));
        }

        return $attributes;
    }

    public static function failureResponse(string $message, int $statusCode = 400, $errors = []): JsonResponse
    {
        $response = [
            'status' => $statusCode,
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }


    public static function successResponse(
        string $action,
        ?string $modelName = null,
        $id = null,
        int $statusCode = 200,
        $data = null,
        ?string $customMessage = null
    ): JsonResponse {
        $response = [
            'status' => $statusCode,
            'success' => true,
            'message' => $customMessage ?? self::generateActionMessage($action, $modelName, $id),
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    private static function generateActionMessage(string $action, ?string $modelName, $id = null): string
    {
        $model = $modelName ?? 'Record';

        return match ($action) {
            self::ACTION_INSERT => "$model created successfully.",
            self::ACTION_FETCH  => "$model data fetched successfully.",
            self::ACTION_DELETE => "$model (ID: $id) deleted successfully.",
            self::ACTION_UPDATE => "$model (ID: $id) updated successfully.",
            default             => "Operation completed successfully.",
        };
    }
}
