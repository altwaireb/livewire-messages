<?php

namespace App\Console\Commands;

use Filament\Commands\MakeUserCommand as Command;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class MakeUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:filament-user
                            {--name= : The name of the user}
                            {--username= : The username of the user}
                            {--email= : A valid and unique email address}
                            {--password= : The password for the user (min. 8 characters)}';

    protected string $patternUsername = '/^[a-z0-9]+(?:[_][a-z0-9]+)*$/';

    protected int $lengthMin = 5;

    protected int $lengthMax = 25;

    protected function getUserData(): array
    {
        return [
            'name' => $this->options['name'] ?? text(
                label: 'Name',
                required: true,
            ),

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

            'email' => $this->options['email'] ?? text(
                label: 'Email address',
                required: true,
                validate: fn (string $email): ?string => match (true) {
                    ! filter_var($email, FILTER_VALIDATE_EMAIL) => 'The email address must be valid.',
                    static::getUserModel()::where('email', $email)->exists() => 'A user with this email address already exists',
                    default => null,
                },
            ),

            'password' => Hash::make($this->options['password'] ?? password(
                label: 'Password',
                required: true,
            )),
        ];
    }
}
