<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'sale_price',
        'sku',
        'stock_quantity',
        'manage_stock',
        'in_stock',
        'is_active',
        'images',
        'weight',
        'dimensions',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'weight' => 'decimal:2',
            'manage_stock' => 'boolean',
            'in_stock' => 'boolean',
            'is_active' => 'boolean',
            'images' => 'array',
        ];
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('in_stock', true);
    }

    // Helper methods
    public function getDisplayPriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    public function hasDiscount()
    {
        return $this->sale_price && $this->sale_price < $this->price;
    }

    public function getDiscountPercentageAttribute()
    {
        if (!$this->hasDiscount()) {
            return 0;
        }

        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    public function decreaseStock($quantity)
    {
        if ($this->manage_stock) {
            $this->stock_quantity -= $quantity;
            if ($this->stock_quantity <= 0) {
                $this->in_stock = false;
                $this->stock_quantity = 0;
            }
            $this->save();
        }
    }

    public function increaseStock($quantity)
    {
        if ($this->manage_stock) {
            $this->stock_quantity += $quantity;
            if ($this->stock_quantity > 0) {
                $this->in_stock = true;
            }
            $this->save();
        }
    }

    // Image helper methods
    public function getMainImageAttribute()
    {
        // Handle null or empty images
        if (empty($this->images)) {
            return asset('images/no-image.svg');
        }

        // Handle array format
        if (is_array($this->images) && count($this->images) > 0) {
            $firstImage = $this->images[0];
            // Make sure it's a string
            if (is_string($firstImage) && !empty($firstImage)) {
                return asset('storage/' . $firstImage);
            }
        }

        // Handle string format (single image)
        if (is_string($this->images) && !empty($this->images)) {
            return asset('storage/' . $this->images);
        }

        // Default fallback
        return asset('images/no-image.svg');
    }

    public function getAllImagesAttribute()
    {
        // Handle null or empty images
        if (empty($this->images)) {
            return [asset('images/no-image.svg')];
        }

        // Handle array format
        if (is_array($this->images)) {
            $validImages = array_filter($this->images, function ($image) {
                return is_string($image) && !empty($image);
            });

            if (!empty($validImages)) {
                return array_map(function ($image) {
                    return asset('storage/' . $image);
                }, $validImages);
            }
        }

        // Handle string format (single image)
        if (is_string($this->images) && !empty($this->images)) {
            return [asset('storage/' . $this->images)];
        }

        // Default fallback
        return [asset('images/no-image.svg')];
    }
}
