<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\AutomationUser;
use App\Models\City;
use App\Models\Customer;
use App\Models\ZaloOa;
use App\Models\ZnsMessage;
use App\Services\SignUpService;
use App\Services\StoreService;
use App\Services\UserService;
use App\Services\ZaloOaService;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel as FacadesExcel;

class StoreController extends Controller
{
    protected $storeService;
    protected $signUpService;
    protected $zaloOaService;
    public function __construct(StoreService $storeService, SignUpService $signUpService, ZaloOaService $zaloOaService)
    {
        $this->storeService = $storeService;
        $this->signUpService = $signUpService;
        $this->zaloOaService = $zaloOaService;
    }

    public function index()
    {
        try {
            $stores = $this->storeService->getAllStore();
            return view('admin.store.index', compact('stores'));
        } catch (Exception $e) {
            Log::error('Failed to find any store' . $e->getMessage());
            return ApiResponse::error('Failed to find any store', 500);
        }
    }

    public function import(Request $request)
    {
        try {
            $filePath = $request->file('import_file')->getRealPath();

            $fileExtension = $request->file('import_file')->getClientOriginalExtension();
            $fileType = $fileExtension === 'xlsx' ? Excel::XLSX : Excel::XLS;
            $user = Auth::user();

            $rows = FacadesExcel::toArray(new class implements ToArray
            {
                public function array(array $array)
                {
                    return $array;
                }
            }, $filePath, null, $fileType)[0];

            foreach (array_slice($rows, 1) as $row) {
                if (isset($row[0]) && !empty($row[0])) {
                    $existingUser = Customer::where('phone', $row[1])->first();
                    $city = City::where('name', $row[4])->first();

                    if (!$existingUser) {
                        try {
                            $dob = Carbon::createFromFormat('d/m/Y', $row[3])->format('Y-m-d');
                        } catch (Exception $e) {
                            $dob = null;
                        }

                        $newUser = Customer::create([
                            'name' => $row[0],
                            'phone' => $row[1],
                            'email' => $row[2] ?? null,
                            'city_id' => $city->id ?? null,
                            'address' => $row[5] ?? null,
                            'source' => $request->source,
                            'user_id' => Auth::user()->id,
                        ]);
                        if ($newUser) {

                            $accessToken = $this->zaloOaService->getAccessToken();
                            $oa_id = ZaloOa::where('is_active', 1)->first()->id;
                            $price = AutomationUser::first()->template->price;
                            $template_id = AutomationUser::first()->template->template_id;
                            $user_template_id = AutomationUser::first()->template_id;
                            $automationUserStatus = AutomationUser::first()->status;

                            if ($automationUserStatus == 1) {
                                $price = AutomationUser::first()->template->price;

                                if ($user->sub_wallet >= $price) {
                                    // Nếu sub_wallet đủ tiền, trừ sub_wallet
                                    $user->sub_wallet -= $price;
                                } elseif ($user->wallet >= $price) {
                                    // Nếu sub_wallet không đủ, kiểm tra wallet, trừ wallet nếu đủ tiền
                                    $user->wallet -= $price;
                                } else {
                                    // Nếu cả 2 ví đều không đủ tiền, không gửi tin nhắn nhưng vẫn tạo bản ghi message
                                    ZnsMessage::create([
                                        'name' => $newUser->name,
                                        'phone' => $newUser->phone,
                                        'sent_at' => Carbon::now(),
                                        'status' => 0,
                                        'note' => 'Tài khoản của bạn không đủ tiền để thực hiện gửi tin nhắn',
                                        'oa_id' => $oa_id,
                                        'template_id' => $user_template_id,
                                        'user_id' => $user->id,
                                    ]);
                                    continue; // Bỏ qua phần gửi tin nhắn
                                }

                                try {
                                    // Gửi yêu cầu tới API ZALO
                                    $client = new Client();
                                    $response = $client->post('https://business.openapi.zalo.me/message/template', [
                                        'headers' => [
                                            'access_token' => $accessToken,
                                            'Content-Type' => 'application/json'
                                        ],
                                        'json' => [
                                            'phone' => preg_replace('/^0/', '84', $newUser->phone),
                                            'template_id' => $template_id,
                                            'template_data' => [
                                                'date' => Carbon::now()->format('d/m/Y') ?? "",
                                                'name' => $newUser->name ?? "",
                                                'order_code' => $newUser->id,
                                                'phone_number' => $newUser->phone,
                                                'status' => 'Đăng ký thành công',
                                                'payment_status' => 'Thành công',
                                                'customer_name' => $newUser->name,
                                                'phone' => $newUser->phone,
                                                'price' => $price,
                                                'payment' => $request->source,
                                                'custom_field' => $newUser->address,
                                            ]
                                        ]
                                    ]);

                                    $responseBody = $response->getBody()->getContents();
                                    Log::info('Api Response: ' . $responseBody);

                                    $responseData = json_decode($responseBody, true);
                                    $status = $responseData['error'] == 0 ? 1 : 0;

                                    // Lưu thông tin ZNS đã gửi
                                    ZnsMessage::create([
                                        'name' => $newUser->name,
                                        'phone' => $newUser->phone,
                                        'sent_at' => Carbon::now(),
                                        'status' => $status,
                                        'note' => $responseData['message'],
                                        'template_id' => $user_template_id,
                                        'oa_id' => $oa_id,
                                        'user_id' => $user->id,
                                    ]);

                                    if ($status == 1) {
                                        Log::info('Gửi ZNS thành công');
                                    } else {
                                        Log::error('Gửi ZNS thất bại: ' . $response->getBody());
                                    }
                                } catch (Exception $e) {
                                    Log::error('Lỗi khi gửi tin nhắn: ' . $e->getMessage());
                                    ZnsMessage::create([
                                        'name' => $newUser->name,
                                        'phone' => $newUser->phone,
                                        'sent_at' => Carbon::now(),
                                        'status' => 0,
                                        'note' => $e->getMessage(),
                                        'oa_id' => $oa_id,
                                        'user_id' => $user->id,
                                    ]);
                                }
                            } else {
                                ZnsMessage::create([
                                    'name' => $newUser->name,
                                    'phone' => $newUser->phone,
                                    'sent_at' => Carbon::now(),
                                    'status' => 0,
                                    'note' => 'Chưa kích hoạt ZNS Automation',
                                    'oa_id' => $oa_id,
                                    'template_id' => $user_template_id,
                                    'user_id' => $user->id,
                                ]);
                            }
                        }
                    }
                    $user->save();
                }
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Import khách hàng thành công'
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            Log::error('Có lỗi trong quá trình import khách hàng: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to add new Customers'], 500);
        }
    }
    public function findByPhone(Request $request)
    {
        try {
            $owner = $this->storeService->findOwnerByPhone($request->input('phone'));
            $stores = new LengthAwarePaginator(
                $owner ? [$owner] : [],
                $owner ? 1 : 0,
                10,
                1,
                ['path' => Paginator::resolveCurrentPath()]
            );
            return view('admin.store.index', compact('stores'));
        } catch (Exception $e) {
            Log::error('Failed to find store owner:' . $e->getMessage());
            return response()->json(['error' => 'Failed to find store owner'], 500);
        }
    }
    public function detail($id)
    {
        try {
            $stores = $this->storeService->findStoreByID(request()->id);
            return view('admin.store.edit', compact('stores'));
        } catch (Exception $e) {
            Log::error('Cannot find store info: ' . $e->getMessage());
            return ApiResponse::error('Cannot find store info', 500);
        }
    }

    public function delete($id)
    {
        try {
            // dd($id);
            $this->storeService->deleteStore(request()->id);
            session()->flash('success', 'Xóa thông tin khách hàng thànhc công');
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Failed to delete store profile: ' . $e->getMessage());
            return ApiResponse::error('Failed to update store profile ', 500);
        }
    }
    public function store(Request $request)
    {
        try {
            Log::info('Start validation for adding new client');

            $validated = $request->validate([
                'name' => 'required',
                'phone' => 'required|unique:sgo_customers,phone',
                'email' => 'nullable|email|unique:sgo_customers,email',
                'address' => 'nullable',
                'source' => 'nullable',
                'product_id' => 'nullable|exists:sgo_products,id', // Kiểm tra product_id
            ]);

            Log::info('Validation passed', $validated);

            // Tiến hành thêm khách hàng mới
            $client = $this->storeService->addNewStore($validated);

            return response()->json([
                'success' => true,
                'message' => 'Client added successfully',
            ]);
        } catch (ValidationException $e) {
            Log::error('Validation error: ' . json_encode($e->errors()));

            return response()->json([
                'success' => false,
                'errors' => $e->errors(),  // Trả về lỗi validation chi tiết
            ], 422);
        } catch (Exception $e) {
            Log::error('Error occurred while adding client: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to add new Client',
            ], 500);
        }
    }
}
