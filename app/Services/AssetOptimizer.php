<?php

namespace App\Services;

class AssetOptimizer
{
    /**
     * Optimize CSS content
     */
    public function optimizeCSS($content)
    {
        if (!config('assets.optimization.css.minify', true)) {
            return $content;
        }

        // Remove comments
        $content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content);
        
        // Remove unnecessary whitespace
        $content = preg_replace('/\s+/', ' ', $content);
        
        // Remove whitespace around specific characters
        $content = preg_replace('/\s*([{}|:;,>+~])\s*/', '$1', $content);
        
        return trim($content);
    }

    /**
     * Optimize JavaScript content
     */
    public function optimizeJS($content)
    {
        if (!config('assets.optimization.js.minify', true)) {
            return $content;
        }

        // Basic JS minification (for production, use proper minifier)
        // Remove single line comments
        $content = preg_replace('/\/\/.*$/m', '', $content);
        
        // Remove multi-line comments
        $content = preg_replace('/\/\*[\s\S]*?\*\//', '', $content);
        
        // Remove unnecessary whitespace
        $content = preg_replace('/\s+/', ' ', $content);
        
        return trim($content);
    }

    /**
     * Get cache busting version for assets
     */
    public function getAssetVersion($file)
    {
        $version = config('assets.cache.version', '1.0.0');
        
        if (config('assets.cache.enabled', true)) {
            // Use file modification time for cache busting
            $filePath = public_path($file);
            if (file_exists($filePath)) {
                $version = filemtime($filePath);
            }
        }
        
        return $version;
    }

    /**
     * Generate optimized asset URL
     */
    public function asset($path)
    {
        $version = $this->getAssetVersion($path);
        $url = asset($path);
        
        return $url . '?v=' . $version;
    }
}
