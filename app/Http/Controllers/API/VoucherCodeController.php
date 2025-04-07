<?php

namespace App\Http\Controllers\API;

use App\Models\VoucherCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\VoucherCodeRequest;
use App\Http\Resources\ApiResponseResource;

class VoucherCodeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $voucherCodes = VoucherCode::with('offer')
                ->whereHas('offer', function ($query) {
                    $query->where('valid_until', '>=', now());
                })
                ->where('valid_date', '>=', now())
                ->latest()
                ->paginate($request->get('per_page', 10));

            $response = [
                'voucher_codes' => $voucherCodes->items(),
                'pagination' => [
                    'total' => $voucherCodes->total(),
                    'current_page' => $voucherCodes->currentPage(),
                    'last_page' => $voucherCodes->lastPage(),
                    'prev_page_url' => $voucherCodes->previousPageUrl(),
                    'next_page_url' => $voucherCodes->nextPageUrl(),
                ]
            ];

            return ApiResponseResource::successResponse(
                action: ApiResponseResource::ACTION_FETCH,
                modelName: ApiResponseResource::$VoucherCode,
                data: $response
            );
        } catch (\Throwable $e) {
            Log::error('Error fetching voucher codes:', ['error' => $e->getMessage()]);
            return ApiResponseResource::failureResponse('Failed to fetch voucher codes.');
        }
    }

    public function show($id)
    {
        try {
            $voucherCode = VoucherCode::with('offer')
                ->whereHas('offer', function ($query) {
                    $query->where('valid_until', '>=', now());
                })
                ->where('valid_date', '>=', now())
                ->where('status', VoucherCode::STATUS_ACTIVE)
                ->find($id);

            if (!$voucherCode) {
                return ApiResponseResource::failureResponse('Voucher code not found or expired', 404);
            }

            return ApiResponseResource::successResponse(
                action: ApiResponseResource::ACTION_FETCH,
                modelName: ApiResponseResource::$VoucherCode,
                id: $voucherCode->id,
                data: $voucherCode
            );
        } catch (\Throwable $e) {
            Log::error("Error showing voucher code ID $id:", ['error' => $e->getMessage()]);
            return ApiResponseResource::failureResponse('Failed to fetch voucher code.');
        }
    }

    public function store(VoucherCodeRequest $request)
    {
        try {
            $voucherCode = VoucherCode::create($request->validated());

            return ApiResponseResource::successResponse(
                action: ApiResponseResource::ACTION_INSERT,
                modelName: ApiResponseResource::$VoucherCode,
                id: $voucherCode->id,
                data: $voucherCode
            );
        } catch (\Throwable $e) {
            Log::error('Error creating voucher code:', ['error' => $e->getMessage()]);
            return ApiResponseResource::failureResponse('Failed to create voucher code.');
        }
    }

    public function update(VoucherCodeRequest $request, $id)
    {
        try {
            $voucherCode = VoucherCode::find($id);

            if (!$voucherCode) {
                return ApiResponseResource::failureResponse('Voucher code not found', 404);
            }

            if ($voucherCode->isExpired()) {
                return ApiResponseResource::failureResponse('Cannot update an expired voucher code.', 403);
            }

            $voucherCode->update($request->validated());

            return ApiResponseResource::successResponse(
                action: ApiResponseResource::ACTION_UPDATE,
                modelName: ApiResponseResource::$VoucherCode,
                id: $voucherCode->id,
                data: $voucherCode
            );
        } catch (\Throwable $e) {
            Log::error("Error updating voucher code ID $id:", ['error' => $e->getMessage()]);
            return ApiResponseResource::failureResponse('Failed to update voucher code.');
        }
    }

    public function destroy($id)
    {
        try {
            $voucherCode = VoucherCode::find($id);

            if (!$voucherCode) {
                return ApiResponseResource::failureResponse('Voucher code not found', 404);
            }

            $voucherCode->delete();

            return ApiResponseResource::successResponse(
                action: ApiResponseResource::ACTION_DELETE,
                modelName: ApiResponseResource::$VoucherCode,
                id: $voucherCode->id
            );
        } catch (\Throwable $e) {
            Log::error("Error deleting voucher code ID $id:", ['error' => $e->getMessage()]);
            return ApiResponseResource::failureResponse('Failed to delete voucher code.');
        }
    }

    public function getByOffer(Request $request, $offerId)
    {
        try {
            $voucherCodes = VoucherCode::where('offer_id', $offerId)
                ->where('valid_date', '>=', now())
                ->where('status', VoucherCode::STATUS_ACTIVE)
                ->latest()
                ->paginate($request->get('per_page', 10));

            $response = [
                'voucher_codes' => $voucherCodes->items(),
                'pagination' => $voucherCodes->paginationData()
            ];

            return ApiResponseResource::successResponse(
                action: ApiResponseResource::ACTION_FETCH,
                modelName: ApiResponseResource::$VoucherCode,
                data: $response
            );
        } catch (\Throwable $e) {
            Log::error("Error fetching voucher codes for offer $offerId:", ['error' => $e->getMessage()]);
            return ApiResponseResource::failureResponse('Failed to fetch offer voucher codes.');
        }
    }
}
