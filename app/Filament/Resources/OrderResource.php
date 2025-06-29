<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Orders';

    protected static ?string $modelLabel = 'Order';

    protected static ?string $pluralModelLabel = 'Orders';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Information')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->required()
                            ->maxLength(255)
                            ->disabled(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'shipped' => 'Shipped',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required(),
                        Forms\Components\Select::make('payment_status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                'expired' => 'Expired',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('payment_method')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Order Amounts')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled(),
                        Forms\Components\TextInput::make('tax_amount')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled(),
                        Forms\Components\TextInput::make('shipping_amount')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled(),
                        Forms\Components\TextInput::make('total_amount')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Shipping Information')
                    ->schema([
                        Forms\Components\DateTimePicker::make('shipped_at')
                            ->label('Shipped Date'),
                        Forms\Components\DateTimePicker::make('delivered_at')
                            ->label('Delivered Date'),
                    ])->columns(2),

                Forms\Components\Section::make('Addresses')
                    ->schema([
                        Forms\Components\Textarea::make('billing_address')
                            ->label('Billing Address')
                            ->disabled()
                            ->formatStateUsing(function ($state) {
                                if (is_array($state)) {
                                    return implode("\n", [
                                        $state['name'] ?? '',
                                        $state['address'] ?? '',
                                        ($state['city'] ?? '') . ', ' . ($state['state'] ?? '') . ' ' . ($state['zip'] ?? ''),
                                        $state['country'] ?? '',
                                        'Phone: ' . ($state['phone'] ?? ''),
                                        'Email: ' . ($state['email'] ?? ''),
                                    ]);
                                }
                                return $state;
                            }),
                        Forms\Components\Textarea::make('shipping_address')
                            ->label('Shipping Address')
                            ->disabled()
                            ->formatStateUsing(function ($state) {
                                if (is_array($state)) {
                                    return implode("\n", [
                                        $state['name'] ?? '',
                                        $state['address'] ?? '',
                                        ($state['city'] ?? '') . ', ' . ($state['state'] ?? '') . ' ' . ($state['zip'] ?? ''),
                                        $state['country'] ?? '',
                                        'Phone: ' . ($state['phone'] ?? ''),
                                        'Email: ' . ($state['email'] ?? ''),
                                    ]);
                                }
                                return $state;
                            }),
                    ])->columns(2),

                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'primary',
                        'shipped' => 'success',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'cancelled' => 'danger',
                        'expired' => 'secondary',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Payment Method')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'cod' => 'Cash on Delivery',
                        'xendit_invoice' => 'Xendit Invoice',
                        'xendit_va' => 'Virtual Account',
                        'xendit_ewallet' => 'E-Wallet',
                        default => ucfirst(str_replace('_', ' ', $state)),
                    }),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Order Date')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('shipped_at')
                    ->label('Shipped')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('delivered_at')
                    ->label('Delivered')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'cod' => 'Cash on Delivery',
                        'xendit_invoice' => 'Xendit Invoice',
                        'xendit_va' => 'Virtual Account',
                        'xendit_ewallet' => 'E-Wallet',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve_payment')
                    ->label('Approve Payment')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(Order $record): bool => $record->payment_status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Payment')
                    ->modalDescription('Are you sure you want to approve this payment? This will mark the payment as paid and update the order status to processing.')
                    ->action(function (Order $record) {
                        $record->update([
                            'payment_status' => 'paid',
                            'status' => 'processing',
                        ]);

                        Notification::make()
                            ->title('Payment Approved')
                            ->body("Payment for order #{$record->order_number} has been approved.")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('mark_shipped')
                    ->label('Mark as Shipped')
                    ->icon('heroicon-o-truck')
                    ->color('primary')
                    ->visible(fn(Order $record): bool => $record->status === 'processing')
                    ->requiresConfirmation()
                    ->action(function (Order $record) {
                        $record->update([
                            'status' => 'shipped',
                            'shipped_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Order Shipped')
                            ->body("Order #{$record->order_number} has been marked as shipped.")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('mark_delivered')
                    ->label('Mark as Delivered')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn(Order $record): bool => $record->status === 'shipped')
                    ->requiresConfirmation()
                    ->action(function (Order $record) {
                        $record->update([
                            'status' => 'delivered',
                            'delivered_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Order Delivered')
                            ->body("Order #{$record->order_number} has been marked as delivered.")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('cancel_order')
                    ->label('Cancel Order')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(Order $record): bool => in_array($record->status, ['pending', 'processing']) && $record->status !== 'cancelled')
                    ->requiresConfirmation()
                    ->modalHeading('Cancel Order')
                    ->modalDescription('Are you sure you want to cancel this order? This will restore stock and cannot be undone.')
                    ->action(function (Order $record) {
                        // Cancel the order
                        $record->update([
                            'status' => 'cancelled',
                            'payment_status' => $record->payment_status === 'pending' ? 'cancelled' : $record->payment_status,
                        ]);

                        // Restore stock
                        foreach ($record->orderItems as $item) {
                            if ($item->product) {
                                $item->product->increment('stock_quantity', $item->quantity);
                            }
                        }

                        Notification::make()
                            ->title('Order Cancelled')
                            ->body("Order #{$record->order_number} has been cancelled and stock has been restored.")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve_payments')
                        ->label('Approve Payments')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->payment_status === 'pending') {
                                    $record->update([
                                        'payment_status' => 'paid',
                                        'status' => 'processing',
                                    ]);
                                    $count++;
                                }
                            }

                            Notification::make()
                                ->title('Payments Approved')
                                ->body("{$count} payments have been approved.")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OrderItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            // 'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
