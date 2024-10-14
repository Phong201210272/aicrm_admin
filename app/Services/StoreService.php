<?php

namespace App\Services;

use App\Mail\UserRegistered;
use App\Models\AutomationUser;
use App\Models\Config;
use App\Models\Customer;
use App\Models\User;
use App\Models\ZaloOa;
use App\Models\ZnsMessage;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StoreService
{
    protected $user;
    protected $automationUser;
    protected $signUpService;
    protected $zaloOaService;
    public function __construct(User $user, AutomationUser $automationUser, SignUpService $signUpService, ZaloOaService $zaloOaService)
    {
        $this->user = $user;
        $this->automationUser = $automationUser;
        $this->signUpService = $signUpService;
        $this->zaloOaService = $zaloOaService;
    }

    public function getAllStore(): LengthAwarePaginator
    {
        try {
            return Customer::where('user_id', Auth::id())->orderByDesc('created_at')->paginate(10);
        } catch (Exception $e) {
            Log::error('Failed to fetch stores: ' . $e->getMessage());
            throw new Exception('Failed to fetch stores');
        }
    }

    public function findStoreByID($id)
    {
        try {
            // dd($id);
            return Customer::where('user_id', Auth::id())->find($id);
        } catch (Exception $e) {
            Log::error('Failed to find store info: ' . $e->getMessage());
            throw new Exception('Failed to find store info');
        }
    }

    public function findOwnerByPhone($phone)
    {
        try {
            $customer = Customer::where('user_id', Auth::id())
                ->where('phone', 'like',  "%{$phone}%")
                ->where('role_id', 1)
                ->first();
            return $customer;
        } catch (Exception $e) {
            Log::error('Failed to find client profile: ' . $e->getMessage());
            throw new Exception('Failed to find client profile');
        }
    }

    public function deleteStore($id)
    {
        try {
            // dd($id);
            Log::info("Deleting store");
            $store = Customer::where('user_id', Auth::id())->find($id);
            $store->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete store profile: ' . $e->getMessage());
            throw new Exception('Failed to delete store profile');
        }
    }

    public function addNewStore(array $data)
    {
        DB::beginTransaction();
        try {
            Log::info('Creating new client');
            // dd(Auth::user()->id);
            $user = Auth::user();
            // dd($user);
            $user_id = Auth::user()->id;
            $customer = Customer::create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'address' => $data['address'],
                'source' => $data['source'] ?? 'Thêm thủ công',
                'user_id' => $user_id,
            ]);

            // $user = AutomationUser::first();
            // if ($user->status != 1) {
            //     return;
            // } else {
            //     $accessToken = $this->getAccessToken();
            //     $oa_id = ZaloOa::where('is_active', 1)->first()->id;

            //     try {
            //         // Gửi yêu cầu tới API Zalo
            //         $clientapi = new Client();
            //         $response = $clientapi->post('https://business.openapi.zalo.me/message/template', [
            //             'headers' => [
            //                 'access_token' => $accessToken,
            //                 'Content-Type' => 'application/json'
            //             ],
            //             'json' => [
            //                 'phone' => preg_replace('/^0/', '84', $data['phone']),
            //                 'template_id' => '355330',
            //                 'template_data' => [
            //                     'date' => Carbon::now()->format('d/m/Y') ?? "",
            //                     'name' => $data['name'] ?? "",
            //                     'order_code' => $client->id,
            //                     'phone_number' => $data['phone'],
            //                     'status' => 'Đăng ký thành công'
            //                 ]
            //             ]
            //         ]);
            //     } catch (Exception $e) {
            //         Log::error('Lỗi khi gửi tin nhắn: ' . $e->getMessage());
            //     }
            // }
            $accessToken = $this->zaloOaService->getAccessToken();
            $oa_id = ZaloOa::where('is_active', 1)->first()->id;
            $price = AutoMationUser::first()->template->price;
            $template_id = AutomationUser::first()->template->template_id;
            $user_template_id = AutomationUser::first()->template_id;
            $automationUserStatus = AutomationUser::first()->status;
            if ($automationUserStatus == 1) {
                $price = AutomationUser::first()->template->price;

                // Kiểm tra và trừ tiền từ sub_wallet trước
                if ($user->sub_wallet >= $price) {
                    $user->sub_wallet -= $price;
                } elseif ($user->wallet >= $price) {
                    // Nếu sub_wallet không đủ, trừ từ wallet nếu đủ tiền
                    $user->wallet -= $price;
                } else {
                    // Nếu cả hai ví đều không đủ tiền, không gửi tin nhắn nhưng vẫn tạo bản ghi message
                    ZnsMessage::create([
                        'name' => $data['name'],
                        'phone' => $data['phone'],
                        'sent_at' => Carbon::now(),
                        'status' => 0,
                        'note' => 'Tài khoản của bạn không đủ tiền để thực hiện gửi tin nhắn',
                        'oa_id' => $oa_id,
                        'template_id' => $user_template_id,
                    ]);
                    return; // Dừng thực hiện gửi tin nhắn
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
                            'phone' => preg_replace('/^0/', '84', $data['phone']),
                            'template_id' => $template_id,
                            'template_data' => [
                                'date' => Carbon::now()->format('d/m/Y') ?? "",
                                'name' => $data['name'] ?? "",
                                'order_code' => $customer->id,
                                'phone_number' => $data['phone'],
                                'status' => 'Đăng ký thành công',
                                'payment_status' => 'Thành công',
                                'customer_name' => $data['name'],
                                'phone' => $data['phone'],
                                'price' => $price,
                                'payment' => $customer->source,
                                'custom_field' => $customer->address,
                            ]
                        ]
                    ]);

                    $responseBody = $response->getBody()->getContents();
                    Log::info('Api Response: ' . $responseBody);

                    $responseData = json_decode($responseBody, true);
                    $status = $responseData['error'] == 0 ? 1 : 0;

                    // Tạo bản ghi ZNS Message
                    ZnsMessage::create([
                        'name' => $data['name'],
                        'phone' => $data['phone'],
                        'sent_at' => Carbon::now(),
                        'status' => $status,
                        'note' => $responseData['message'],
                        'template_id' => $user_template_id,
                        'oa_id' => $oa_id,
                    ]);

                    if ($status == 1) {
                        Log::info('Gửi ZNS thành công');
                    } else {
                        Log::error('Gửi ZNS thất bại: ' . $response->getBody());
                    }
                } catch (Exception $e) {
                    Log::error('Lỗi khi gửi tin nhắn: ' . $e->getMessage());
                    // Lưu thông tin tin nhắn vào cơ sở dữ liệu khi gặp lỗi
                    ZnsMessage::create([
                        'name' => $data['name'],
                        'phone' => $data['phone'],
                        'sent_at' => Carbon::now(),
                        'status' => 0,
                        'note' => $e->getMessage(),
                        'oa_id' => $oa_id,
                    ]);
                }
            } else {
                ZnsMessage::create([
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'sent_at' => Carbon::now(),
                    'status' => 0,
                    'note' => 'Chưa kích hoạt ZNS Automation',
                    'oa_id' => $oa_id,
                    'template_id' => $user_template_id,
                ]);
            }
            $user->save();
            DB::commit();
            return $customer;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to add new client: " . $e->getMessage());
            throw new Exception('Failed to add new client');
        }
    }

    // protected function getAccessToken()
    // {
    //     $oa = ZaloOa::where('is_active', 1)->first();

    //     if (!$oa) {
    //         Log::error('Không tìm thấy OA nào có trạng thái is_active = 1');
    //         throw new Exception('Không tìm thấy OA nào có trạng thái is_active = 1');
    //     }

    //     $accessToken = $oa->access_token;
    //     $refreshToken = $oa->refresh_token;

    //     if (!$accessToken || Cache::has('access_token_expired')) {
    //         $secretKey = env('ZALO_APP_SECRET');
    //         $appId = env('ZALO_APP_ID');
    //         $accessToken = $this->refreshAccessToken($refreshToken, $secretKey, $appId);

    //         $oa->update(['access_token' => $accessToken]);
    //     }

    //     Log::info('Retrieved access token: ' . $accessToken);
    //     return $accessToken;
    // }

    // protected function refreshAccessToken($refreshToken, $secretKey, $appId)
    // {
    //     $client = new Client();
    //     try {
    //         $response = $client->post('https://oauth.zaloapp.com/v4/oa/access_token', [
    //             'headers' => [
    //                 'secret_key' => $secretKey,
    //             ],
    //             'form_params' => [
    //                 'grant_type' => 'refresh_token',
    //                 'refresh_token' => $refreshToken,
    //                 'app_id' => $appId,
    //             ]
    //         ]);

    //         $body = json_decode($response->getBody(), true);
    //         Log::info("Refresh token response: " . json_encode($body));

    //         if (isset($body['access_token'])) {
    //             // Lưu access token vào cache và đặt thời gian hết hạn là 24h
    //             Cache::put('access_token', $body['access_token'], 86400);
    //             Cache::forget('access_token_expired');

    //             if (isset($body['refresh_token'])) {
    //                 Cache::put('refresh_token', $body['refresh_token'], 7776000);
    //             }
    //             return [$body['access_token'], $body['refresh_token']];
    //         } else {
    //             throw new Exception('Failed to refresh access token');
    //         }
    //     } catch (Exception $e) {
    //         Log::error('Failed to refresh access token: ' . $e->getMessage());
    //         throw new Exception('Failed to refresh access token');
    //     }
    // }
}
