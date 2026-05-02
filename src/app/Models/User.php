<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function instructor()
    {
        return $this->hasOne(Instructor::class);
    }

    public function manager()
    {
        return $this->hasOne(Manager::class);
    }

    public function physicalEvaluations()
    {
        return $this->hasMany(PhysicalEvaluation::class);
    }


    public function role(): string
    {
        if ($this->manager()->exists()) return 'manager';
        if ($this->instructor()->exists()) return 'instructor';
        return 'student';
    }

    public function isManager(): bool
    {
        return $this->role() === 'manager';
    }

    public function isInstructor(): bool
    {
        return $this->role() === 'instructor';
    }

    public function isStudent(): bool
    {
        return $this->role() === 'student';
    }
}