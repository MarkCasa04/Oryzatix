<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotKnowledge extends Model
{
    use HasFactory;

    protected $table = 'chatbot_knowledge';

    protected $fillable = [
        'question',
        'answer',
        'category',
        'language',
        'status',
    ];
}
