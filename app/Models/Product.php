<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'category',
        'badge',
        'description',
        'image',
        'is_video',
    ];

    protected $casts = [
        'is_video' => 'boolean',
    ];

    /**
     * Retourne l'URL complète de l'image (locale ou externe)
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) return null;

        // URL externe (Pinterest, Unsplash, etc.)
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        // Fichier local stocké dans storage/app/public
        return asset('storage/' . $this->image);
    }

    /**
     * Surcharge toArray pour inclure image_url dans les réponses JSON
     */
    public function toArray(): array
    {
        $array = parent::toArray();
        $array['image_url'] = $this->image_url;
        return $array;
    }
}