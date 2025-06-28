<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'orderItems';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('product_name')
                    ->required()
                    ->maxLength(255)
                    ->disabled(),
                Forms\Components\TextInput::make('product_sku')
                    ->label('SKU')
                    ->disabled(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->disabled(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled(),
                Forms\Components\TextInput::make('total')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_name')
            ->columns([
                Tables\Columns\TextColumn::make('product_name')
                    ->label('Product')
                    ->searchable(),
                Tables\Columns\TextColumn::make('product_sku')
                    ->label('SKU')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Qty')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Unit Price')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Remove create action since order items shouldn't be created manually
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // Remove bulk actions for order items
            ])
            ->paginated(false); // Show all items without pagination
    }
}
