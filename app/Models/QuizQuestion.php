<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    // Property to store the userId value
    public $userId;  // It is now a public property for the QuizQuestion instance

    protected $fillable = [
        'quiz_id',
        'question_type',
        'question',
        'answers',
        'status',
    ];

    protected $casts = [
        'answers' => 'array',
    ];

    public function quiz(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Method to get the answer for the user.
     *
     * @param  mixed $userId Optional userId passed to override the instance property.
     * @return mixed QuizAnswer result.
     */
    public function getAnswer($userId = null)
    {
        // Create the base query to find an answer for this question
        $query = $this->hasOne(QuizAnswer::class, 'quiz_question_id');
        
        // Logic for selecting the correct user ID
        if (auth()->user()->user_type == 'student') {
            // If user is a student, use the authenticated user's ID
            $query->where('user_id', auth()->user()->id);
        } elseif ($userId !== null) {
            // If a userId is passed, use it
            $query->where('user_id', $userId);
        } else {
            // Otherwise, fall back to the instance's stored userId
            $query->where('user_id', $this->userId);
        }

        // Return the first result from the query
        return $query;
    }
}

