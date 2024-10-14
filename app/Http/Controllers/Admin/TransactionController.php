<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\SuperAdmin;
use App\Models\Transaction;
use App\Services\TransactionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class TransactionController extends Controller
{
    protected $transactionService;
    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(Request $request)
    {
        try {
            $status = $request->input('status');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $authUser = session('authUser');
            $userId = $authUser->id;

            $transactions = $this->transactionService->getPaginatedTransactionsForAdmin($userId, $status, $startDate, $endDate);
            if ($request->ajax()) {
                return response()->json([
                    'html' => view('admin.transaction.table', compact('transactions'))->render(),
                    'pagination' => $transactions->links('pagination::custom')->render(),
                ]);
            }
            return view('admin.transaction.index', compact('transactions'));
        } catch (Exception $e) {
            Log::error("Failed to get paginated Transaction list: " . $e->getMessage());
            return ApiResponse::error("Failed to get paginated Transaction list", 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $status = $request->input('status');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $authUser = session('authUser');
            $userId = $authUser->id;

            $transactions = $this->transactionService->getPaginatedTransactionsForAdmin($userId, $status, $startDate, $endDate);
            if ($request->ajax()) {
                $html = view('admin.transaction.table', compact('transactions'))->render();
                $pagination = $transactions->appends(['id' => $userId, 'status' => $status, 'start_date' => $startDate, 'end_date' => $endDate])->links('pagination::custom')->render();
                return response()->json(['html' => $html, 'pagination' => $pagination]);
            }
            return view('admin.transaction.index', compact('transactions'));
        } catch (Exception $e) {
            Log::error("Failed to get paginated Transaction list: " . $e->getMessage());
            return ApiResponse::error("Failed to get paginated Transaction list", 500);
        }
    }

    public function payment()
    {
        $description = $this->generateRandomDescription();
        $authUser = Auth::user();
        return view('admin.transaction.payment', compact('authUser', 'description'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $authUser = session('authUser');
        $transaction = $this->transactionService->createNewTransaction($request->all(), $authUser->id);

        if ($request->has('requestInvoice') && $request->input('requestInvoice') === 'true') {
            $html = view('pdf.transaction', compact('transaction'))->render();
            $pdf = Pdf::loadHTML($html);
            $pdfFileName = 'hoa_don_giao_dich_' . $transaction->user->name . '.pdf';
            $pdf->save(public_path($pdfFileName));
            return response()->json([
                'pdf_url' => asset($pdfFileName),
                'message' => 'Thanh toán thành công'
            ]);
        }

        Session::flash('action', 'Thanh toán thành công');
        return redirect()->route('admin.{username}.transaction.index', ['username' => Auth::user()->username]);
    }


    public function generateQrCode(Request $request)
    {
        $superAdmin = SuperAdmin::first();
        $amount = $request->input('amount');
        $bank_id = $superAdmin->bank->shortName;
        $bank_account = $superAdmin->bank_account;
        $description = $request->input('description');
        $account_name = $superAdmin->name;

        // Tạo URL cho QR code
        $template = 'compact2';
        $qrCodeUrl = "https://img.vietqr.io/image/" . $bank_id . "-" . $bank_account . "-" . $template . ".png?amount=" . $amount . "&addInfo=" . urlencode($description) . "&accountName=" . urlencode($account_name);

        // Trả về URL cho QR code
        return $qrCodeUrl;
    }


    public function generateRandomDescription()
    {
        // Tạo 5 ký tự số ngẫu nhiên
        $randomNumbers = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        // Kết hợp với tiền tố "SGO_"
        $description = 'SGO_' . $randomNumbers;

        return $description;
    }

    public function updateNotification($id)
    {
        try {
            $transaction = Transaction::find(request()->id);
            $transaction->notification = 3;
            $transaction->save();
            return to_route('admin.transaction.index');
        } catch (Exception $e) {
            Log::error('failed to update mark-as-read this transaction: ' . $e->getMessage());
            return ApiResponse::error('Failed to mark-as-read this transaction', 500);
        }
    }
}
