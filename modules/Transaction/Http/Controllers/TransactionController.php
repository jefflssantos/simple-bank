<?php

namespace Modules\Transaction\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Transaction\DTOs\TransactionDTO;
use Modules\Transaction\Exceptions\TransactionException;
use Modules\Transaction\Http\Requests\TransferRequest;
use Modules\Transaction\Services\TransactionService;

class TransactionController extends Controller
{
    public function __invoke(TransferRequest $request): JsonResponse
    {
        $transactionDTO = new TransactionDTO(
            $request->value, (int) $request->payer, (int) $request->payee
        );

        try {
            $transactionService = new TransactionService();
            $transactionService->transfer($transactionDTO);

            return response()->json([], JsonResponse::HTTP_NO_CONTENT);
        } catch (TransactionException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_PAYMENT_REQUIRED);
        }
    }
}
