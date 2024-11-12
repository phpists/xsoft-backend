<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\UnauthorizedException;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\User\UserResource;
use App\Mail\ResetPasswordKodMail;
use App\Mail\ResetPasswordLinkMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;

class AuthController extends CoreController
{
    public static function routers()
    {
        Route::group(
            [
                'prefix' => 'auth'
            ],
            function () {
                Route::post('register', [static::class, 'register']);
                Route::post('login', [static::class, 'login'])->name('login');

                Route::post('forgot-password', [static::class, 'forgotPassword']);
//                Route::post('forgot-password-verification-code', [static::class, 'forgotPasswordCheckKod']);
//                Route::match(['get', 'post'], 'reset-password', [static::class, 'resetPassword'])->name('password.reset');
//
                Route::group(
                    [
                        'middleware' => 'auth:sanctum'
                    ],
                    function () {
                        Route::get('user', [static::class, 'getUser']);
                        Route::match(['get', 'post'], 'logout', [static::class, 'logout']);
                    }
                );

            }
        );
    }

    /**
     * Реєстрація користувача
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function register(RegisterRequest $request)
    {
        $input = $request->all();

        try {
            $user = new User();
            $user->role_id = User::CUSTOMER;
            $user->first_name = $input['first_name'];
            $user->last_name = $input['last_name'];
            $user->email = $input['email'];
            $user->phone = $input['phone'];
            $user->password = Hash::make($input['password']);
            $user->saveOrFail();

            $token = $user->createToken('access_token')->plainTextToken;

            return $this->responseSuccess([
                'user' => new UserResource($user),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(sprintf('Register error: %s:%d: %s', $e->getFile(), $e->getLine(), $e->getMessage()));

            return $this->responseError('Не вдалось створити нового користувача');
        }
    }

    /**
     * Вхід
     * @return \Illuminate\Http\JsonResponse
     * @throws UnauthorizedException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->getCredentials();

        if (!Auth::validate($credentials)) {
            throw new UnauthorizedException('Дані не знайдені');
        };

        $user = Auth::getProvider()->retrieveByCredentials($credentials);
        $token = $user->createToken('access_token')->plainTextToken;

        return $this->responseSuccess([
            'user' => new UserResource($user),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    /**
     * Отримання даних користувача
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser()
    {
        $user = User::find(Auth::id());
        return $this->responseSuccess(new UserResource($user));
    }

    /**
     * Розлогінювання
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return $this->responseSuccess(true);
    }

    /**
     * Відправка листа с силкою для відновлення пароля
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $email = $request->email;

        $user = User::where('email', $email)->first();

        if ($user) {
            $token = Password::createToken($user);
            $resetLink = url("/reset-password?token={$token}&email={$email}");

            Mail::to($email)->send(new ResetPasswordLinkMail($resetLink));
        }

        return $this->responseSuccess([
            'message' => 'Посилання для скидання паролю надіслано на вашу електронну пошту.',
        ], 200);
    }
}
