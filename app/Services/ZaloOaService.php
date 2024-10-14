<?php

namespace App\Services;

use App\Models\OaTemplate;
use App\Models\ZaloOa;
use App\Models\ZnsMessage;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ZaloOaService
{
    protected $oaTemplate;
    protected $zaloOa;
    protected $znsMessage;
    protected $client;

    public function __construct(OaTemplate $oaTemplate, ZaloOa $zaloOa, ZnsMessage $znsMessage, Client $client)
    {
        $this->oaTemplate = $oaTemplate;
        $this->zaloOa = $zaloOa;
        $this->znsMessage = $znsMessage;
        $this->client = $client;
    }

    public function addNewOa(array $data)
    {
        DB::beginTransaction();
        try {
            $zaloOa = $this->zaloOa->create([
                'name' => $data['name'],
                'access_token' => $data['access_token'],
                'oa_id' => $data['oa_id'],
                'refresh_token' => $data['refresh_token'],
                'is_active' => 0,
                'user_id' => Auth::user()->id,
            ]);

            DB::commit();
            return $zaloOa;
        } catch (Exception $e) {
            Log::error('Failed to add new OA to database: ' . $e->getMessage());
            throw new Exception('Failed to add new OA to database');
        }
    }


    public function getAccessToken()
    {
        $oa = ZaloOa::where('is_active', 1)->first();

        if (!$oa) {
            Log::error('Không tìm thấy OA nào có trạng thái is_active = 1');
            throw new Exception('Không tìm thấy OA nào có trạng thái is_active = 1');
        }

        $accessToken = Cache::get('access_token');
        $expiration = Cache::get('access_token_expiration');

        if (!$accessToken || now()->greaterThan($expiration)) {
            Log::info('Access token is expired or not available, refreshing token.');

            $refreshToken = $oa->refresh_token;
            $secretKey = env('ZALO_APP_SECRET');
            $appId = env('ZALO_APP_ID');
            $accessToken = $this->refreshAccessToken($refreshToken, $secretKey, $appId);

            // Cập nhật cache với access token mới và thời gian hết hạn
            Cache::put('access_token', $accessToken, 86400);
            Cache::put('access_token_expiration', now()->addHours(24), 86400);
        }

        Log::info('Retrieved access token: ' . $accessToken);
        return $accessToken;
    }

    public function refreshAccessToken($refreshToken, $secretKey, $appId)
    {
        $activeOa = ZaloOa::where('is_active', 1)->first();
        if (!$activeOa) {
            Log::error('No active OA found for refresh token');
            throw new Exception('No active OA found');
        }

        $client = new Client();
        try {
            $response = $client->post('https://oauth.zaloapp.com/v4/oa/access_token', [
                'headers' => [
                    'secret_key' => $secretKey,
                ],
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                    'app_id' => $appId,
                ]
            ]);
            // dd($response);
            $body = json_decode($response->getBody()->getContents(), true);
            Log::info("Refresh token response: ", $body);

            if (isset($body['access_token'])) {
                $activeOa->access_token = $body['access_token'];
                if (isset($body['refresh_token'])) {
                    $activeOa->refresh_token = $body['refresh_token'];
                }
                $activeOa->save();

                Cache::put('access_token', $body['access_token'], 86400);
                Cache::put('access_token_expiration', now()->addHours(24), 86400);

                if (isset($body['refresh_token'])) {
                    Cache::put('refresh_token', $body['refresh_token'], 7776000); // 90 days
                }

                return $body['access_token'];
            } else {
                throw new Exception('Failed to refresh access token');
            }
        } catch (Exception $e) {
            Log::error('Failed to refresh access token: ' . $e->getMessage());
            throw new Exception('Failed to refresh access token');
        }
    }
}
