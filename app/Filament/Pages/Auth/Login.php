<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;

class Login extends BaseLogin
{
    protected string $view = 'filament.pages.auth.login';

    public function getTitle(): string
    {
        return 'Login Admin - Second Cafe';
    }

    public function getHeading(): string
    {
        return 'Welcome Back';
    }

    public function getSubheading(): ?string
    {
        return 'Enter your account credentials to manage orders.';
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email')
            ->placeholder('Masukkan email')
            ->email()
            ->required()
            ->autocomplete('email')
            ->autofocus();
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Password')
            ->placeholder('Masukkan password')
            ->password()
            ->revealable()
            ->required()
            ->autocomplete('current-password');
    }
}
