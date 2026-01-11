<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ZipCode;
use App\Models\Settlement;
use App\Models\County;
use Illuminate\Http\Request;

class ZipCodeController extends Controller
{
    // --- EZ A METÓDUS HIÁNYZOTT A KLIENSNEK ---
    public function counties()
    {
        return County::orderBy('name')->get();
    }

    // --- A KLIENS EZEKET IS HASZNÁLJA ---
    
    // Városok listázása (szűréssel) - "settlements"
    public function settlements(Request $request)
    {
        $query = Settlement::with('county');

        if ($request->has('county_id')) {
            $query->where('county_id', $request->county_id);
        }

        return $query->get();
    }

    // Keresés (betű és megye alapján) - "zipcodes/search"
    public function search(Request $request) 
    {
        // A kliens 'letter' paramétert küld, de mi itt szűrjük a település nevére
        $query = ZipCode::with(['settlement.county']);
        
        if ($request->has('county_id')) {
            $query->whereHas('settlement', function($q) use ($request) {
                $q->where('county_id', $request->county_id);
                
                if ($request->has('letter')) {
                    $q->where('name', 'like', $request->letter . '%');
                }
            });
        }

        return $query->get();
    }
    
    public function letters($county_id)
    {
        // SQL: SELECT DISTINCT SUBSTRING(name, 1, 1) as letter ...
        $letters = Settlement::where('county_id', $county_id)
            ->selectRaw('DISTINCT SUBSTR(name, 1, 1) as letter')
            ->orderBy('letter')
            ->pluck('letter');
            
        return response()->json($letters);
    }

    // 2. CRUD: Új város mentése
    public function store(Request $request)
    {
        $validated = $request->validate([
            'county_id' => 'required|exists:counties,id',
            'zip_code' => 'required|integer',
            'name' => 'required|string|max:255',
        ]);

        // Tranzakció kezelés javasolt, de most egyszerűsítve:
        // Először létrehozzuk a települést
        $settlement = Settlement::create([
            'county_id' => $validated['county_id'],
            'name' => $validated['name']
        ]);

        // Majd hozzárendeljük az irányítószámot
        $zip = ZipCode::create([
            'settlement_id' => $settlement->id,
            'zip_code' => $validated['zip_code']
        ]);

        return response()->json(['message' => 'Sikeres mentés', 'data' => $settlement], 201);
    }

    // 3. CRUD: Törlés
    public function destroy($id)
    {
        // Itt $id a település ID-ja
        $settlement = Settlement::findOrFail($id);
        $settlement->zipCodes()->delete(); // Kapcsolódó irányítószámok törlése
        $settlement->delete();

        return response()->json(['message' => 'Törölve']);
    }
}
