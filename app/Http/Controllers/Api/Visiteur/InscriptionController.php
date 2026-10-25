<?php

namespace App\Http\Controllers\Api\Visiteur;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;
use Spatie\Permission\Models\Role;
use Throwable;

class InscriptionController extends Controller
{
    /**
     * Liste des providers OAuth supportés.
     */
    protected array $providers = ['google'];

    /**
     * Vérifie si le provider demandé est supporté.
     */
    protected function isProviderAllowed(string $provider): bool
    {
        return in_array($provider, $this->providers, true);
    }

    /**
     * Retourne l'URL de redirection OAuth en format JSON (Idéal pour Frontend SPA/Mobile).
     */
    public function getUrl(string $provider): JsonResponse
    {
        if (!$this->isProviderAllowed($provider)) {
            return response()->json([
                'success' => false,
                'message' => "Le fournisseur d'authentification [{$provider}] n'est pas supporté.",
            ], 404);
        }

        try {
            /** @var AbstractProvider $driver */
            $driver = Socialite::driver($provider);
            $url = $driver->stateless()->redirect()->getTargetUrl();

            return response()->json([
                'success' => true,
                'provider' => $provider,
                'url' => $url,
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => "Impossible de générer le lien de connexion {$provider}.",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Redirige directement le navigateur vers le fournisseur OAuth.
     */
    public function redirect(Request $request, string $provider): RedirectResponse|JsonResponse
    {
        if (!$this->isProviderAllowed($provider)) {
            return response()->json([
                'success' => false,
                'message' => "Le fournisseur d'authentification [{$provider}] n'est pas supporté.",
            ], 404);
        }

        try {
            /** @var AbstractProvider $driver */
            $driver = Socialite::driver($provider);

            return $driver->stateless()->redirect();
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => "Erreur lors de la redirection vers {$provider}.",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Gère le retour (callback) de Google et procède à l'inscription / connexion d'un Visiteur.
     */
    public function callback(Request $request, string $provider): JsonResponse
    {
        if (!$this->isProviderAllowed($provider)) {
            return response()->json([
                'success' => false,
                'message' => "Le fournisseur d'authentification [{$provider}] n'est pas supporté.",
            ], 404);
        }

        try {
            /** @var AbstractProvider $driver */
            $driver = Socialite::driver($provider);

            // Récupération des données utilisateur en mode stateless (obligatoire pour une API sans session)
            $socialUser = $driver->stateless()->user();
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => "Échec de l'authentification auprès de {$provider}.",
                'error' => $e->getMessage(),
            ], 400);
        }

        $googleId = $socialUser->getId();
        $email = $socialUser->getEmail();
        $name = $socialUser->getName() ?? $socialUser->getNickname() ?? 'Visiteur';

        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => "Impossible de récupérer l'adresse email depuis {$provider}.",
            ], 422);
        }

        try {
            $user = DB::transaction(function () use ($googleId, $email, $name) {
                // 1. Recherche par google_id en priorité
                $user = User::where('google_id', $googleId)->first();

                if (!$user) {
                    // 2. Recherche par email si non trouvé par google_id
                    $user = User::where('email', $email)->first();

                    if ($user) {
                        // Lier le google_id au compte existant
                        $user->update([
                            'google_id' => $googleId,
                        ]);
                    } else {
                        // 3. Inscription du nouvel utilisateur
                        $user = User::create([
                            'name' => $name,
                            'email' => $email,
                            'google_id' => $googleId,
                            'email_verified_at' => now(),
                            'password' => null, // Mot de passe optionnel pour OAuth
                        ]);
                    }
                } else {
                    // Mise à jour éventuelle du nom
                    if ($user->name !== $name) {
                        $user->update(['name' => $name]);
                    }
                }

                // 4. Assigner le rôle 'Visiteur' s'il n'a pas encore de rôle
                Role::firstOrCreate(['name' => 'Visiteur', 'guard_name' => 'web']);
                if ($user->roles()->count() === 0) {
                    $user->assignRole('Visiteur');
                }

                return $user;
            });

            // 5. Génération du jeton personnel d'API Sanctum
            $token = $user->createToken('google_auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Authentification réussie.',
                'token_type' => 'Bearer',
                'access_token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'google_id' => $user->google_id,
                    'roles' => $user->getRoleNames(),
                    'created_at' => $user->created_at,
                ],
            ], 200);

        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => "Erreur lors de l'enregistrement de l'utilisateur.",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Récupère le profil du visiteur connecté via son token Sanctum.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'google_id' => $user->google_id,
                'ville' => $user->ville,
                'telephone' => $user->telephone,
                'roles' => $user->getRoleNames(),
            ],
        ], 200);
    }

    /**
     * Déconnexion (révocation du jeton Sanctum actif).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion effectuée avec succès.',
        ], 200);
    }
}
