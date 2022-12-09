<?php

namespace App\Services;

use App\Models\User;
use App\Services\Base\BaseService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

final class AuthService extends BaseService
{
    private User $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param $request
     * @param bool $withCredentials
     * @return array
     */
    public function register($request, bool $withCredentials = true): array
    {
        try {
            $user = $this->user->newInstance();
            $user->name = $request->name;
            $user->surname = $request->surname;
            $user->email = $request->email;
            $user->tel = $request->tel;
            $user->password = Hash::make($request->password);
            $user->save();
            $credentials = $this->checkCredentials($user->email, $request->password);
            $data = ['user' => $user, 'credentials' => $credentials];
            return $this->serviceReturn(true, __('success.register_success'), 200, $data);
        } catch (Exception $e) {
            return $this->serviceReturn(false, $e->getMessage(), 500, $e);
        }
    }

    /**
     * @param $request
     * @return array
     */
    public function login($request): array
    {
        try {
            $response = $this->checkCredentials($request->credentials, $request->password);
            if (!empty($response['access_token'])) {
                $user = User::query()->where('email', $request->credentials)->firstOrFail();
//                $user->load(['roles.permissions', 'permissions']);
                $data = [
                    'credentials' => $response,
                    'user' => $user
                ];
                return $this->serviceReturn(true, __('success.get_data'), 200, $data);
            }
            return $this->serviceReturn(false, __('fail.invalid_credential'), 401, null);
        } catch (Exception $e) {
            return $this->serviceReturn(false, $e->getMessage(), 500, null);
        }
    }

    public function me(): array
    {
        try {
            $user = auth()->user();
            $user->load(['roles.permissions', 'permissions']);
            return $this->serviceReturn(true, __('success.get_data'), 200, $user);
        } catch (Exception $e) {
            return $this->serviceReturn(false, $e->getMessage(), 500, null);
        }
    }

    /**
     * @return array
     */
    public function logout(): array
    {
        try {
            $logout = auth()->user()->token()->revoke();
            if ($logout) {
                return $this->serviceReturn(true, __('success.logged_out'), 200, null);
            }
            return $this->serviceReturn(false, __('fail.token_not_found'), 401, null);
        } catch (Exception $e) {
            return $this->serviceReturn(false, $e->getMessage(), 500, null);
        }
    }

    /**
     * @param $credentials
     * @param $password
     * @return array|mixed
     */
    public function checkCredentials($credentials, $password): mixed
    {
        return Http::asForm()->acceptJson()->post(config('services.passport.base_url'), [
            'grant_type' => 'password',
            'client_id' => config('services.passport.client_id'),
            'client_secret' => config('services.passport.client_secret'),
            'username' => $credentials,
            'password' => $password,
            'scope' => '',
        ])->json();
    }
}
