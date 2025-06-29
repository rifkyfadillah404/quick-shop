<?php

namespace App\Helpers;

class ImageHelper
{
    /**
     * Get optimized image URL with lazy loading attributes
     */
    public static function getOptimizedImageUrl($imagePath, $width = null, $height = null)
    {
        if (!$imagePath) {
            return asset('images/placeholder.jpg');
        }

        // If it's already a full URL, return as is
        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            return $imagePath;
        }

        // Add storage path if needed
        if (!str_starts_with($imagePath, 'storage/')) {
            $imagePath = 'storage/' . $imagePath;
        }

        return asset($imagePath);
    }

    /**
     * Get lazy loading attributes for images
     */
    public static function getLazyLoadingAttributes($alt = '', $class = '')
    {
        return [
            'loading' => 'lazy',
            'decoding' => 'async',
            'alt' => $alt,
            'class' => $class,
            'style' => 'content-visibility: auto;'
        ];
    }

    /**
     * Generate responsive image srcset
     */
    public static function getResponsiveImageSrcset($imagePath, $sizes = [300, 600, 900, 1200])
    {
        $baseUrl = self::getOptimizedImageUrl($imagePath);
        $srcset = [];

        foreach ($sizes as $size) {
            $srcset[] = $baseUrl . " {$size}w";
        }

        return implode(', ', $srcset);
    }

    /**
     * Get placeholder image data URL
     */
    public static function getPlaceholderDataUrl($width = 300, $height = 200)
    {
        // Simple gray placeholder
        return "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='{$width}' height='{$height}' viewBox='0 0 {$width} {$height}'%3E%3Crect width='100%25' height='100%25' fill='%23f3f4f6'/%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' dy='.3em' fill='%239ca3af' font-family='sans-serif' font-size='14'%3ELoading...%3C/text%3E%3C/svg%3E";
    }
}
