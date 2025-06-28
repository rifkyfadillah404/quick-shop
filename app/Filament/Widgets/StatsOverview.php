<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Calculate stats
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_amount');
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalCustomers = User::where('role', 'customer')->count();

        // This month stats
        $thisMonthRevenue = Order::where('payment_status', 'paid')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');

        $thisMonthOrders = Order::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Last month for comparison
        $lastMonthRevenue = Order::where('payment_status', 'paid')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->sum('total_amount');

        $lastMonthOrders = Order::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();

        // Calculate percentage changes
        $revenueChange = $lastMonthRevenue > 0 ?
            (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;

        $ordersChange = $lastMonthOrders > 0 ?
            (($thisMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100 : 0;

        return [
            Stat::make('Total Revenue', '$' . number_format($totalRevenue, 2))
                ->description($revenueChange >= 0 ?
                    '+' . number_format($revenueChange, 1) . '% from last month' :
                    number_format($revenueChange, 1) . '% from last month')
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueChange >= 0 ? 'success' : 'danger'),

            Stat::make('Total Orders', number_format($totalOrders))
                ->description($ordersChange >= 0 ?
                    '+' . number_format($ordersChange, 1) . '% from last month' :
                    number_format($ordersChange, 1) . '% from last month')
                ->descriptionIcon($ordersChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($ordersChange >= 0 ? 'success' : 'danger'),

            Stat::make('Total Products', number_format($totalProducts))
                ->description('Active products in catalog')
                ->descriptionIcon('heroicon-m-cube')
                ->color('info'),

            Stat::make('Total Customers', number_format($totalCustomers))
                ->description('Registered customers')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning'),
        ];
    }
}
