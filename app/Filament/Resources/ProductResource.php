<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Product Information')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('Category')
                            ->options(Category::active()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn(string $context, $state, callable $set) =>
                                $context === 'create' ? $set('slug', Str::slug($state)) : null
                            ),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(Product::class, 'slug', ignoreRecord: true),

                        Forms\Components\Textarea::make('short_description')
                            ->label('Short Description')
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('description')
                            ->label('Full Description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Product Images')
                    ->schema([
                        Forms\Components\FileUpload::make('images')
                            ->label('Product Images')
                            ->image()
                            ->multiple()
                            ->directory('products')
                            ->maxFiles(5)
                            ->reorderable()
                            ->columnSpanFull()
                            ->helperText('Upload up to 5 product images. First image will be the main image.'),
                    ]),

                Forms\Components\Section::make('Pricing & Inventory')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Regular Price')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01),

                        Forms\Components\TextInput::make('sale_price')
                            ->label('Sale Price')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->helperText('Leave empty if no discount'),

                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->required()
                            ->maxLength(255)
                            ->unique(Product::class, 'sku', ignoreRecord: true),

                        Forms\Components\TextInput::make('stock_quantity')
                            ->label('Stock Quantity')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Forms\Components\Toggle::make('manage_stock')
                            ->label('Manage Stock')
                            ->default(true)
                            ->helperText('Enable stock management for this product'),

                        Forms\Components\Toggle::make('in_stock')
                            ->label('In Stock')
                            ->default(true),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Product will be visible on the website'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Shipping Information')
                    ->schema([
                        Forms\Components\TextInput::make('weight')
                            ->label('Weight (kg)')
                            ->numeric()
                            ->step(0.01),

                        Forms\Components\TextInput::make('dimensions')
                            ->label('Dimensions (L x W x H)')
                            ->maxLength(255)
                            ->placeholder('e.g., 10 x 5 x 3 cm'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('first_image')
                    ->label('Image')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl(asset('images/no-image.svg'))
                    ->getStateUsing(function ($record) {
                        if ($record->images && is_array($record->images) && count($record->images) > 0) {
                            return asset('storage/' . $record->images[0]);
                        }
                        return null;
                    }),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('SKU copied')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sale_price')
                    ->label('Sale Price')
                    ->money('USD')
                    ->sortable()
                    ->placeholder('No discount'),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->numeric()
                    ->sortable()
                    ->color(fn(string $state): string => match (true) {
                        $state > 10 => 'success',
                        $state > 0 => 'warning',
                        default => 'danger',
                    }),

                Tables\Columns\IconColumn::make('in_stock')
                    ->label('In Stock')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('in_stock')
                    ->query(fn(Builder $query): Builder => $query->where('in_stock', true))
                    ->label('In Stock Only'),

                Tables\Filters\Filter::make('active')
                    ->query(fn(Builder $query): Builder => $query->where('is_active', true))
                    ->label('Active Only'),

                Tables\Filters\Filter::make('on_sale')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('sale_price'))
                    ->label('On Sale'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ReplicateAction::make()
                    ->beforeReplicaSaved(function (Product $replica, array $data): void {
                        $replica->sku = $data['sku'] . '-copy';
                        $replica->slug = $data['slug'] . '-copy';
                    }),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
