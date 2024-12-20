<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens; // Import the HasApiTokens trait
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;



class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // Add HasApiTokens here

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'profile_picture',
        'preferences',
        'score',
        'credit_card',
        'expiry_date',
        'cvv',
    ];

    protected $hidden = [
        'password', 'credit_card','remember_token', 'cvv',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    public function updateHighScore($newScore)
    {
        if ($newScore > $this->score) {
            $this->score = $newScore;
            $this->save();

            // Create a new score record in the scores table with required fields
            $this->scores()->create([
                'user_id' => $this->id,
                'name' => $this->name,
                'username' => $this->username,
                'score' => $newScore,
            ]);
        }
    }
}
