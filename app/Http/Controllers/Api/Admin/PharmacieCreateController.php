<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePharmacieRequest;
use App\Models\User;
use App\Models\Pharmacie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class PharmacieCreateController extends Controller
{
    public function index(Request $request)
    {
        try {
            if (!$request->user() || !$request->user()->hasRole('Admin')) {
                return response()->json(['success' => false, 'message' => 'Accès refusé.'], 403);
            }
            $pharmacies = Pharmacie::with('proprietaire')->latest()->paginate(15);
            return response()->json([
                'success'    => true,
                'message'    => 'Liste des pharmacies partenaires récupérée.',
                'pharmacies' => $pharmacies,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des pharmacies.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    public function show($id, Request $request)
    {
        try {
            if (!$request->user() || !$request->user()->hasRole('Admin')) {
                return response()->json(['success' => false, 'message' => 'Accès refusé.'], 403);
            }
            $pharmacie = Pharmacie::with('proprietaire')->find($id);
            if (!$pharmacie) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pharmacie non trouvée.',
                ], 404);
            }
            return response()->json([
                'success'   => true,
                'message'   => 'Pharmacie trouvée avec succès.',
                'pharmacie' => $pharmacie,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de la pharmacie.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    public function createPharmacie(CreatePharmacieRequest $request)
    {
        try
        {
            if (!$request->user()->hasAnyRole('Admin')) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Accès refusé.'
                ], 403);
            }
            $validated = $request->validated();
           #Transaction atomique (si une étape échoue, tout est annulé)
           #En informatique et en base de données, cela signifie qu'un groupe d'opérations forme un seul bloc inséparable.
           #Sa règle d'or est le « Tout ou rien » (All or Nothing) :
           #Soit toutes les opérations réussissent, et elles sont enregistrées définitivement (Commit).
           #Soit une seule opération échoue (ou une erreur survient), et la base de données annule immédiatement toutes les opérations précédentes pour revenir exactement à l'état initial (Rollback).
           $result = DB::transaction(function () use ($validated) {
                $user = User::create([
                    'name'=>$validated['name'],
                    'prenom'=>$validated['prenom'], 
                    'email'=>$validated['email'],
                    'password'=>$validated['password'],
                    'telephone'=>$validated['telephone'] ?? null,
                    'ville'=>$validated['ville'],
                ]);
                $user->assignRole('Pharmacien');
                $pharmacie = Pharmacie::create([
                    'proprietaire_id'=>$user->id,
                    'nom_pharmacie'=>$validated['nom_pharmacie'],
                    'ville'=>$validated['ville'],
                    'quatier'=>$validated['quatier'], 
                    'numero_pharmacie'=>$validated['numero_pharmacie'],
                    'latitude'=>$validated['latitude'],
                    'longitude'=>$validated['longitude'],
                ]);
                return compact('user', 'pharmacie');
            });
            return response()->json([
                'success'=> true,
                'message'=> 'Pharmacie et compte pharmacien créés avec succès.',
                'data'=> [
                    'user'=> [
                        'id'=>$result['user']->id,
                        'name'=>$result['user']->name,
                        'prenom'=>$result['user']->prenom,
                        'email'=>$result['user']->email,
                        'telephone'=>$result['user']->telephone,
                        'role'=>'Pharmacien',
                    ],
                    'pharmacie' => $result['pharmacie'],
                ],
            ], 201);

        }
        catch(Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du compte.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function deletePharmacie(Pharmacie $pharmacie, Request $request)
    {
        try
        {
            if (!$request->user()->hasAnyRole('Admin')) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Accès refusé.'
                ], 403);
            }
            DB::transaction(function() use ($pharmacie){
                $proprietaire = $pharmacie->proprietaire;
                $pharmacie->delete();
                if($proprietaire){
                    $proprietaire->delete();
                }
            });
            return response()->json([
                'success' => true,
                'message' => 'Pharmacie et compte pharmacien supprimés avec succès.',
            ], 200);

        }
        catch(Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du compte.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
   
}
