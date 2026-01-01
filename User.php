<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'role', 'available_days', 'overtime_hours', 
        'hire_date', 'max_vacation_days'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'hire_date' => 'datetime',
    ];

    // Accessor for full name
    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // Relationships
    public function leaveRequests()
    {
        return $this->hasMany(\App\Models\OnlineRequest::class, 'user_email', 'email')
                    ->where('type', 'leave_request');
    }

    public function approvedRequests()
    {
        return $this->hasMany(\App\Models\OnlineRequest::class, 'approved_by');
    }

    public function rejectedRequests()
    {
        return $this->hasMany(\App\Models\OnlineRequest::class, 'rejected_by');
    }

    // Scopes
    public function scopeSupervisors($query)
    {
        return $query->where('role', 'supervisor');
    }

    public function scopeHr($query)
    {
        return $query->where('role', 'hr');
    }

    public function scopeStaff($query)
    {
        return $query->where('role', 'staff');
    }

    // Methods
    public function isSupervisor()
    {
        return $this->role === 'supervisor';
    }

    public function isHr()
    {
        return $this->role === 'hr';
    }

    public function calculateEarnedDays()
    {
        $hireDate = $this->hire_date ?? now()->subYear(); // Default to 1 year ago if not set
        $monthsWorked = $hireDate->diffInMonths(now());
        
        if ($this->first_name . ' ' . $this->last_name == 'Hazem Alashi') {
            return (26/12) * $monthsWorked;
        } elseif (in_array($this->first_name . ' ' . $this->last_name, ['Ayman Hussein', 'Nidal Ahmad', 'Yasin Mohamad'])) {
            return (30/12) * $monthsWorked;
        } else {
            return (14/12) * $monthsWorked;
        }
    }
}