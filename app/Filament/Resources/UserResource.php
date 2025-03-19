<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name'),
                Forms\Components\TextInput::make('email')->email()->required(),
//                Forms\Components\TextInput::make('password')->password()->rule(Password::default())->required(),
//               Forms\Components\TextInput::make('password_confirmation')
//                    ->same('password')
//                   ->password()
//                   ->rule(Password::default())
//                   ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')->date('d/m/y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),


                Tables\Actions\Action::make('change_password')
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
                    ->action(function (User  $record ,array $data): void {
                        $record->update([
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

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];

    }


    /**
     *apenas exemplo de como você pode fazer
     */
//    public static function canCreate(): bool
//    {
//        return false;
//    }
}
