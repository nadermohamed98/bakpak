<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookShelf extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'short_description', 'user_id', 'organization_id', 'instructor_ids', 'category_id', 'course_type', 'language_id', 'description', 'is_private', 'video_source', 'video', 'image', 'image_media_id', 'duration',
        'is_downloadable', 'is_free', 'price', 'is_discountable', 'discount_type', 'discount', 'discount_start_at', 'discount_end_at', 'is_featured', 'deleted_at', 'tags', 'level_id', 'total_enrolled', 'subject_id',
        'is_renewable', 'renew_after', 'meta_title', 'meta_keywords', 'meta_description', 'meta_image', 'status', 'is_published', 'total_rating', 'total_lesson', 'capacity', 'class_ends_at',
    ];

    protected $casts    = [
        'instructor_ids' => 'array',
        'video'          => 'array',
        'image'          => 'array',
        'meta_image'     => 'array',
        'tags'           => 'array',
        'status'         => 'string',
    ];
}
