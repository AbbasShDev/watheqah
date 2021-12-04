<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

class Manual extends Model {

    use HasFactory;

    protected $guarded = [];

    protected $appends = ['url', 'post_image'];

    /**
     *  Setup model event hooks
     */
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) Uuid::generate(4);
        });
    }

    public function getUrlAttribute()
    {
        return route('manual.show', $this);
    }

    public function getPostImageAttribute()
    {
        return presentImage($this->banner);
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function isPrivate()
    {
        return $this->manual_status === "private";
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function tutorials()
    {
        return $this->belongsToMany(Tutorial::class);
    }
}
