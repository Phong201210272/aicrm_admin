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
            Log::info('Starting process to create new client with data: ', $data);

            // Lấy thông tin user hiện tại
            $user = Auth::user();
            $user_id = $user->id;

            // Tạo khách hàng mới
            $customer = Customer::create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'address' => $data['address'] ?? null,
                'source' => $data['source'] ?? 'Thêm thủ công',
                'user_id' => $user_id,
                'product_id' => $data['product_id'] ?? null,
            ]);

            Log::info('Customer created successfully: ' . json_encode($customer));

            // Lấy token và thông tin cần thiết từ API Zalo
            $accessToken = $this->zaloOaService->getAccessToken();
            $oa_id = ZaloOa::where('is_active', 1)->first()->id;
            $template_id = AutomationUser::first()->template->template_id;
            $user_template_id = AutomationUser::first()->template_id;
            $automationUserStatus = AutomationUser::first()->status;

            $price = AutomationUser::first()->template->price;
            Log::info('Automation User Status: ' . $automationUserStatus . ' | Price: ' . $price);

            // Kiểm tra trạng thái automation
            if ($automationUserStatus == 1) {
                if ($user->sub_wallet >= $price || $user->wallet >= $price) {
                    try {
                        Log::info('Attempting to send ZNS message via Zalo API');
                        // Gửi yêu cầu tới API Zalo
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
                                    'price' => $price,
                                    'payment' => $customer->source,
                                    'custom_field' => $customer->address,
                                    'product_name' => $data['product_name'] ?? '',
                                ]
                            ]
                        ]);

                        $responseBody = $response->getBody()->getContents();
                        Log::info('Zalo API Response: ' . $responseBody);

                        $responseData = json_decode($responseBody, true);
                        $status = $responseData['error'] == 0 ? 1 : 0;

                        ZnsMessage::create([
                            'name' => $data['name'],
                            'phone' => $data['phone'],
                            'sent_at' => Carbon::now(),
                            'status' => $status,
                            'note' => $responseData['message'],
                            'oa_id' => $oa_id,
                            'template_id' => $user_template_id,
                            'user_id' => $user->id,
                        ]);

                        if ($status == 1) {
                            Log::info('ZNS message sent successfully');
                            // Trừ tiền khi tin nhắn gửi thành công
                            if ($user->sub_wallet >= $price) {
                                $user->sub_wallet -= $price;
                                Log::info('Sub_wallet has enough funds. Subtracted: ' . $price);
                            } elseif ($user->wallet >= $price) {
                                $user->wallet -= $price;
                                Log::info('Main wallet has enough funds. Subtracted: ' . $price);
                            }
                        } else {
                            Log::error('ZNS message failed: ' . $responseBody);
                        }
                    } catch (Exception $e) {
                        Log::error('Error occurred while sending ZNS message: ' . $e->getMessage());

                        // Tạo bản ghi khi gặp lỗi
                        ZnsMessage::create([
                            'name' => $data['name'],
                            'phone' => $data['phone'],
                            'sent_at' => Carbon::now(),
                            'status' => 0,
                            'note' => $e->getMessage(),
                            'oa_id' => $oa_id,
                            'user_id' => $user->id,
                        ]);
                    }
                } else {
                    Log::warning('Not enough funds in both wallets.');
                    ZnsMessage::create([
                        'name' => $data['name'],
                        'phone' => $data['phone'],
                        'sent_at' => Carbon::now(),
                        'status' => 0,
                        'note' => 'Tài khoản của bạn không đủ tiền để thực hiện gửi tin nhắn',
                        'oa_id' => $oa_id,
                        'template_id' => $user_template_id,
                        'user_id' => $user->id,
                    ]);
                }
            } else {
                Log::warning('Automation User is not active');
                ZnsMessage::create([
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'sent_at' => Carbon::now(),
                    'status' => 0,
                    'note' => 'Chưa kích hoạt ZNS Automation',
                    'oa_id' => $oa_id,
                    'template_id' => $user_template_id,
                    'user_id' => $user->id,
                ]);
            }

            $user->save();
            DB::commit();
            Log::info('Transaction committed successfully');
            return $customer;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to add new client: ' . $e->getMessage());
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
