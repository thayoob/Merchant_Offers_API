<?php

namespace App\Http\Controllers\API;

use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\MerchantRequest;
use App\Http\Resources\ApiResponseResource;

class MerchantController extends Controller
{
    public function index(Request $request)
    {
        try {
            $merchants = Merchant::latest()
                ->paginate($request->get('per_page', 10));

            $response = [
                'merchants' => $merchants->items(),
                'pagination' => [
                    'total' => $merchants->total(),
                    'current_page' => $merchants->currentPage(),
                    'last_page' => $merchants->lastPage(),
                    'prev_page_url' => $merchants->previousPageUrl(),
                    'next_page_url' => $merchants->nextPageUrl(),
                ]
            ];

            return ApiResponseResource::successResponse(
                action: ApiResponseResource::ACTION_FETCH,
                modelName: ApiResponseResource::$Merchant,
                data: $response
            );
        } catch (\Throwable $e) {
            Log::error('Error fetching merchants:', ['error' => $e->getMessage()]);
            return ApiResponseResource::failureResponse('Failed to fetch merchants.');
        }
    }

    public function show($id)
    {
        try {
            $merchant = Merchant::find($id);

            if (!$merchant) {
                return ApiResponseResource::failureResponse('Merchant not found', 404);
            }

            return ApiResponseResource::successResponse(
                action: ApiResponseResource::ACTION_FETCH,
                modelName: ApiResponseResource::$Merchant,
                id: $merchant->id,
                data: $merchant
            );
        } catch (\Throwable $e) {
            Log::error("Error showing merchant ID $id:", ['error' => $e->getMessage()]);
            return ApiResponseResource::failureResponse('Failed to fetch merchant.');
        }
    }

    public function store(MerchantRequest $request)
    {
        try {
            $merchant = Merchant::create($request->validated());

            return ApiResponseResource::successResponse(
                action: ApiResponseResource::ACTION_INSERT,
                modelName: ApiResponseResource::$Merchant,
                id: $merchant->id,
                data: $merchant
            );
        } catch (\Throwable $e) {
            Log::error('Error creating merchant:', ['error' => $e->getMessage()]);
            return ApiResponseResource::failureResponse('Failed to create merchant.');
        }
    }

    public function update(MerchantRequest $request, $id)
    {
        try {
            $merchant = Merchant::find($id);

            if (!$merchant) {
                return ApiResponseResource::failureResponse('Merchant not found', 404);
            }

            $merchant->update($request->validated());

            return ApiResponseResource::successResponse(
                action: ApiResponseResource::ACTION_UPDATE,
                modelName: ApiResponseResource::$Merchant,
                id: $merchant->id,
                data: $merchant
            );
        } catch (\Throwable $e) {
            Log::error("Error updating merchant ID $id:", ['error' => $e->getMessage()]);
            return ApiResponseResource::failureResponse('Failed to update merchant.');
        }
    }

    public function destroy($id)
    {
        try {
            $merchant = Merchant::find($id);

            if (!$merchant) {
                return ApiResponseResource::failureResponse('Merchant not found', 404);
            }

            $hasActiveOffers = $merchant->offers()
                ->where('valid_until', '>=', now())
                ->exists();

            if ($hasActiveOffers) {
                return ApiResponseResource::failureResponse(
                    'Cannot delete merchant with active offers. Please expire or delete the offers first.',
                    422
                );
            }

            $merchant->delete();

            return ApiResponseResource::successResponse(
                action: ApiResponseResource::ACTION_DELETE,
                modelName: ApiResponseResource::$Merchant,
                id: $merchant->id
            );
        } catch (\Throwable $e) {
            Log::error("Error deleting merchant ID $id:", ['error' => $e->getMessage()]);
            return ApiResponseResource::failureResponse('Failed to delete merchant.');
        }
    }
}
