<?php

namespace App\Http\Controllers\API;

use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\OfferRequest;
use App\Http\Resources\ApiResponseResource;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        try {
            $offers = Offer::with('merchant')
                ->whereHas('merchant', function ($query) {
                    $query->where('status', 'active');
                })
                ->latest()
                ->paginate($request->get('per_page', 10));

            $response = [
                'offers' => $offers->items(),
                'pagination' => [
                    'total' => $offers->total(),
                    'current_page' => $offers->currentPage(),
                    'last_page' => $offers->lastPage(),
                    'prev_page_url' => $offers->previousPageUrl(),
                    'next_page_url' => $offers->nextPageUrl(),
                ]
            ];

            return ApiResponseResource::successResponse(
                action: ApiResponseResource::ACTION_FETCH,
                modelName: ApiResponseResource::$Offer,
                data: $response
            );
        } catch (\Throwable $e) {
            Log::error('Error fetching offers:', ['error' => $e->getMessage()]);
            return ApiResponseResource::failureResponse('Failed to fetch offers.');
        }
    }

    public function show($id)
    {
        try {
            $offer = Offer::with('merchant')
                ->whereHas('merchant', function ($query) {
                    $query->where('status', 'active');
                })
                ->where('valid_until', '>=', now())
                ->find($id);

            if (!$offer) {
                return ApiResponseResource::failureResponse('Offer not found or expired', 404);
            }

            return ApiResponseResource::successResponse(
                action: ApiResponseResource::ACTION_FETCH,
                modelName: ApiResponseResource::$Offer,
                id: $offer->id,
                data: $offer
            );
        } catch (\Throwable $e) {
            Log::error("Error showing offer ID $id:", ['error' => $e->getMessage()]);
            return ApiResponseResource::failureResponse('Failed to fetch offer.');
        }
    }

    public function store(OfferRequest $request)
    {
        try {
            $validated = $request->validated();

            $offer = Offer::create($validated);

            return ApiResponseResource::successResponse(
                action: ApiResponseResource::ACTION_INSERT,
                modelName: ApiResponseResource::$Offer,
                id: $offer->id,
                data: $offer
            );
        } catch (\Throwable $e) {
            Log::error('Error creating offer:', ['error' => $e->getMessage()]);
            return ApiResponseResource::failureResponse('Failed to create offer.');
        }
    }

    public function update(OfferRequest $request, $id)
    {
        try {
            $offer = Offer::find($id);

            if (!$offer) {
                return ApiResponseResource::failureResponse('Offer not found', 404);
            }

            $validated = $request->validated();

            $offer->update($validated);

            return ApiResponseResource::successResponse(
                action: ApiResponseResource::ACTION_UPDATE,
                modelName: ApiResponseResource::$Offer,
                id: $offer->id,
                data: $offer
            );
        } catch (\Throwable $e) {
            Log::error("Error updating offer ID $id:", ['error' => $e->getMessage()]);
            return ApiResponseResource::failureResponse('Failed to update offer.');
        }
    }

    public function destroy($id)
    {
        try {
            $offer = Offer::find($id);

            if (!$offer) {
                return ApiResponseResource::failureResponse('Offer not found', 404);
            }

            $offer->delete();

            return ApiResponseResource::successResponse(
                action: ApiResponseResource::ACTION_DELETE,
                modelName: ApiResponseResource::$Offer,
                id: $offer->id
            );
        } catch (\Throwable $e) {
            Log::error("Error deleting offer ID $id:", ['error' => $e->getMessage()]);
            return ApiResponseResource::failureResponse('Failed to delete offer.');
        }
    }
}
