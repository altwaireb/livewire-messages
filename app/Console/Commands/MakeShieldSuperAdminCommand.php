<?php

namespace App\Console\Commands;

use BezhanSalleh\FilamentShield\Commands\MakeShieldSuperAdminCommand as Command;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class MakeShieldSuperAdminCommand extends Command
{
    public $signature = 'shield:super-admin
        {--user= : ID of user to be made super admin.}
        {--panel= : Panel ID to get the configuration from.}
    ';

    public string $patternUsername = '/^[a-z0-9]+(?:[_][a-z0-9]+)*$/';

    protected int $lengthMin = 5;

    protected int $lengthMax = 25;

    protected function createSuperAdmin(): Authenticatable
    {
        return static::getUserModel()::create([
            'name' => text(label: 'Name', required: true),
            'username' => $this->options['username'] ?? text(
                label: 'Username',
                required: true,
                validate: fn (string $username): ?string => match (true) {
                    str($username)->length() < $this->lengthMin => 'The username is very short, it must be at least '.$this->lengthMin.' letters.',
                    str($username)->length() > $this->lengthMax => 'The username is very long, it must be at least '.$this->lengthMax.' letters.',
                    strtolower($username) != $username => 'The username must be lowercase.',
                    ! preg_match($this->patternUsername, $username) => 'The username match be start with a letter and number with or without underscore.',
                    static::getUserModel()::where('username', $username)->exists() => 'A user with this username already exists',
                    default => null,
                },
            ),
            'email' => text(
                label: 'Email address',
                required: true,
                validate: fn (string $email): ?string => match (true) {
                    ! filter_var($email, FILTER_VALIDATE_EMAIL) => 'The email address must be valid.',
                    static::getUserModel()::where('email', $email)->exists() => 'A user with this email address already exists',
                    default => null,
                },
            ),
            'password' => Hash::make(password(
                label: 'Password',
                required: true,
                validate: fn (string $value) => match (true) {
                    strlen($value) < 8 => 'The password must be at least 8 characters.',
                    default => null
                }
            )),
        ]);
    }
}
