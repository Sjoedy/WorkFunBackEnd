<?php

namespace App\Services;

use App\Models\User;
use App\Services\Base\BaseService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

final class AuthService extends BaseService
{
    private User $user;
    private ExceptionService $exceptionService;

    /**
     * @param User $user
     * @param ExceptionService $exceptionService
     */
    public function __construct(User             $user,
                                ExceptionService $exceptionService)
    {
        $this->user = $user;
        $this->exceptionService = $exceptionService;
    }

    /**
     * @param $request
     * @return array
     */
    public function register($request): array
    {
        try {
            $user = $this->user->newInstance();
            $user->name = $request->name;
            $user->surname = $request->surname;
            $user->email = $request->email;
            $user->tel = $request->tel;
            $user->position = $request->position;
            $user->password = Hash::make($request->password);
            $user->save();
            $credentials = $this->checkCredentials($user->email, $request->password);
            $data = ['user' => $user, 'credentials' => $credentials];
            return $this->serviceResponse(true, __('success.register_success'), 200, $data);
        } catch (Exception $e) {
            $info = $this->exceptionService->getInfo($e);
            return $this->serviceResponse(false, $info['message'], $info['code'], null);
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
                $data = [
                    'credentials' => $response,
                    'user' => $user
                ];
                return $this->serviceResponse(true, __('success.get_data'), 200, $data);
            }
            return $this->serviceResponse(false, __('fail.invalid_credential'), 401, null);
        } catch (Exception $e) {
            $info = $this->exceptionService->getInfo($e);
            return $this->serviceResponse(false, $info['message'], $info['code'], null);
        }
    }

    public function me($request): array
    {
        try {
            $user = User::query()->where('id', $request->user('api')->id)->first();
            return $this->serviceResponse(true, __('success.get_data'), 200, $user);
        } catch (Exception $e) {
            $info = $this->exceptionService->getInfo($e);
            return $this->serviceResponse(false, $info['message'], $info['code'], null);
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
                return $this->serviceResponse(true, __('success.logged_out'), 200, null);
            }
            return $this->serviceResponse(false, __('fail.token_not_found'), 401, null);
        } catch (Exception $e) {
            $info = $this->exceptionService->getInfo($e);
            return $this->serviceResponse(false, $info['message'], $info['code'], null);
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
