<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\Rules\Password;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),

            Actions\Action::make('change_password')
                ->modalHeading('Alterar Senha')
                ->modalSubmitActionLabel('Salvar')
                ->form([
                    TextInput::make('password')
                        ->password()
                        ->label('Nova Senha')
                        ->rule(Password::default())
                        ->required()
                        ->revealable(), // Permite revelar a senha
                    TextInput::make('password_confirmation')
                        ->password()
                        ->label('Confirmar Senha')
                        ->same('password')
                        ->rule(Password::default())
                        ->required()
                        ->revealable(), // Permite revelar a confirmação
                ])
                ->action(function (array $data): void {
                    $this->record->update([
                        'password' => bcrypt($data['password']),
                    ]);

                    // Opcional: Adicionar mensagem de sucesso
                    Notification::make()
                        ->title('Saved successfully')
                        ->icon('heroicon-o-document-text')
                        ->iconColor('success')
                        ->send();
                })
                ->modalWidth('md'), // Ajusta o tamanho da modal
        ];
    }
}
