<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model {
    use HasFactory;

    protected $fillable = ['title', 'content', 'preprocessed_content', 'file_path', 'user_id', 'similarity_percentage'];

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function getStatusAttribute()
    {
        if ($this->similarity_percentage > 40) {
            return 'plagiarized';
        } elseif ($this->similarity_percentage >= 31 && $this->similarity_percentage <= 40) {
            return 'suspicious';
        } else {
            return 'clean';
        }
    }
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'plagiarized' => 'red',
            'suspicious' => 'yellow',
            'clean' => 'green',
            default => 'gray',
        };
    }
}
