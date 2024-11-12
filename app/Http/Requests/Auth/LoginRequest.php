<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'login' => 'required',
            'password' => 'required'
        ];
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getCredentials()
    {
        // The form field for providing login or password
        // have name of "login", however, in order to support
        // logging users in with both (login and email)
        // we have to check if user has entered one or another
        $login = $this->get('login');

        if ($this->isEmail($login)) {
            return [
                'email' => $login,
                'password' => $this->get('password')
            ];
        }

        return $this->only('login', 'password');
    }

    /**
     * Validate if provided parameter is valid email.
     *
     * @param $param
     * @return bool
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function isEmail($param)
    {
        $factory = $this->container->make(ValidationFactory::class);

        return ! $factory->make(
            ['login' => $param],
            ['login' => 'email']
        )->fails();
    }
}
