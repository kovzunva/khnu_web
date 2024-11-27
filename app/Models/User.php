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

    // Відстежуються
    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'user_to_follow_id');
    }

    // Відстежувачі
    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_to_follow_id', 'user_id');
    }

    // Чи відстежується
    public function isFollowing($otherUser)
    {
        return Follower::where('user_id', $this->id)
            ->where('user_to_follow_id', $otherUser->id)
            ->exists();
    }

    // Чи є орієнтиром
    public function isOrientator($otherUserId)
    {
        $userId = $this->id;
        $query = "SELECT COUNT(*) AS count 
                FROM orientator 
                WHERE user_id = :userId 
                AND user_orientator_id = :otherUserId";
        $result = DB::select($query, ['userId' => $userId, 'otherUserId' => $otherUserId]);
        $count = $result[0]->count;
        return $count > 0;
    }

    public function orientators()
    {
        $orientators = DB::table('orientator as o')
            ->join('users as u', 'o.user_orientator_id', '=', 'u.id')
            ->where('user_id', $this->id)
            ->select('u.id', 'u.name')
            ->get()
            ->toArray();

        return User::hydrate($orientators);
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

    // Надсилання сповіщень відстежувачам
    public function sendNotificationsToFollowers($message, $link = null, $icon = null, $item_id = null)
    {
        $currentUser = auth()->user();
        $followers = $currentUser->followers;

        // Чи не було в відстежувачів такого сповіщення
        $existingNotifications = Notification::whereIn('user_id', $followers->pluck('id'))
            ->where('link', $link)
            ->where('item_id', $item_id)
            ->get();

        // Якщо немає такого сповіщення, надсилання кожному відстежувачу
        foreach ($followers as $follower) {
            $followerId = $follower->id;

            $notificationExists = $existingNotifications->where('user_id', $followerId)->isNotEmpty();

            if (!$notificationExists) {
                $follower->myNotify($message,$link,$icon,$item_id);
            }
        }
    }

    // Видалення неактуальних сповіщень у відстежувачів
    public function deleteNotification($icon, $item_id)
    {
        Notification::where('user_id', $this->id)
            ->where('icon', $icon)
            ->where('item_id', $item_id)
            ->delete();
    }

    // Видалення неактуальних сповіщень у відстежувачів
    public function deleteFollowersNotifications($icon, $item_id)
    {
        $followerIds = $this->followers()->pluck('id')->toArray();


        Notification::whereIn('user_id', $followerIds)
            ->where('icon', $icon)
            ->where('item_id', $item_id)
            ->delete();
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
