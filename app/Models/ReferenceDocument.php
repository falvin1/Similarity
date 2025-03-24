<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferenceDocument extends Model {
    use HasFactory;

    protected $table = 'reference_documents';
    
    protected $fillable = ['title', 'content', 'preprocessed_content', 'file_path'  ,  'file_id',
    'google_drive_link',];
}
