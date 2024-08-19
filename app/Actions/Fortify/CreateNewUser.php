<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Rules\Username;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Spatie\Permission\Models\Role;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function create(array $input): User
    {
        $input += ['role_id' => $this->getRoleUser()];

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:5', 'max:25', new Username, 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            'role_id' => 'exists:roles,id',
        ], [
            'exists' => __('Please talk to your Administrator that you cannot register.'),
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'username' => $input['username'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        $user->assignRole($input['role_id']);

        return $user;
    }

    public function getRoleUser(): ?int
    {
        return Role::where('name', 'user')->first()->id ?? null;
    }
}
