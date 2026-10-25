<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['google_id', 'name', 'email', 'email_verified_at', 'password', 'ville', 'telephone'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Les pharmacies appartenant à cet utilisateur (pharmacien).
     */
    public function pharmacies()
    {
        return $this->hasMany(Pharmacie::class, 'proprietaire_id');
    }

    /**
     * Les ordonnances soumises par cet utilisateur.
     */
    public function ordonnances()
    {
        return $this->hasMany(Ordonnance::class, 'user_id');
    }

    /**
     * Les commandes passées par cet utilisateur.
     */
    public function commandes()
    {
        return $this->hasMany(Commande::class, 'user_id');
    }
}
