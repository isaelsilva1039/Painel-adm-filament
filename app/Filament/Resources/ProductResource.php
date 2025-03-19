<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                        ->reactive()
                        ->afterStateUpdated(function ($state, $set){
                            $state = Str::slug($state);
                            $set('slug', $state);
                        })
                        ->label('Nome produto')
                        ->required(),
                Forms\Components\TextInput::make('description')->label('Descrição produto')->required(),
                Forms\Components\TextInput ::make('price')->label('Preço produto')->required(),
                Forms\Components\TextInput::make('amount')
                    ->label('Quantidade')
                    ->required()
                    ->numeric()
                    ->minValue(0),
                Forms\Components\TextInput::make('slug'),
                Forms\Components\FileUpload::make('photo')
                    ->image()
                    ->directory('products')
//                Forms\Components\Select::make('categories')->relationship('categories', 'name')->multiple()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')->circular()->height(60),
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Preço')
                    ->formatStateUsing(fn ($state) => number_format($state / 100, 2, ',', '.'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Quantidade')
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\TextColumn::make('created_at')->date('d/m/y H:i')
                    ->sortable(),
            ])
            ->filters([

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label('Criado a partir de'),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label('Criado até'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'], fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date));
                    }),


                Tables\Filters\Filter::make('amount_greater_than_100')
                    ->label('Quantidade maior cem')
                    ->query(function (Builder $query): Builder {
                        return $query->where('amount', '>', 100);
                    }),

                Tables\Filters\Filter::make('amount_equal_zero')
                    ->label('Quantidade igual a zero')
                    ->query(function (Builder $query): Builder {
                        return $query->where('amount', 0);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
          ProductResouceResource\RelationManagers\CategoriesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
