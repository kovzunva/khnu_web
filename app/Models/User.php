<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use App\Services\ImageService;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'banned_until',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Аватарка
    public function ava(){
        return ImageService::getImg('profile',$this->id);
    }

    // Сповіщення
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Ролі
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasPermission(...$permissions)
    {
        $user = $this;
        $role = $user->role;

        if (!$role) return false;

        if ($permissions) {
            foreach ($permissions as $permission) {
                if ($role->{$permission} != 1) {
                    return false;
                }
            }
            return true;
        }

        return true;
    }

    // Надіслати сповіщення
    public function myNotify($message,$link=null,$icon=null,$item_id=null)
    {
        $notification = new Notification([
            'user_id' => $this->id,
            'message' => $message,
            'link' => $link,
            'icon' => $icon,
            'item_id' => $item_id,
        ]);

        $notification->save();
    }

    // Чи є непрочитані сповіщення
    function hasUnreadNotifications() {
        return Notification::where('user_id', $this->id)
            ->whereNull('read_at')
            ->exists();
    }

    // Кількість непрочитаних повідомлень
    public function unreadNotificationsCount() {
        return Notification::where('user_id', $this->id)
            ->whereNull('read_at')
            ->count();
    }

    // Забанити
    public function ban($days)
    {
        $bannedUntil = now()->addDays($days);
        $this->update(['banned_until' => $bannedUntil]);
    }

    public function unban()
    {
        $this->update(['banned_until' => null]);
    }

    // Чи забанений користувач 
    public function isBanned()
    {
        $user = $this;
        return $user && $user->banned_until && now()->lt($user->banned_until);
    }  

}
