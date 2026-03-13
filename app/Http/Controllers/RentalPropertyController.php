<?php

namespace App\Http\Controllers;

use App\Models\RentalProperty;
use Illuminate\Http\Request;

class RentalPropertyController extends Controller
{
    // GET all available properties (with ranking score based on budget)
    public function index(Request $request)
    {
        $budget = $request->query('budget');

        $properties = RentalProperty::where('is_available', true)->get();

        $properties = $properties->map(function ($property) use ($budget) {
            $property->ranking_score = $this->calculateScore($property, $budget);
            $property->tier = $this->getTier($property->ranking_score);
            return $property;
        });

        $properties = $properties->sortByDesc('ranking_score')->values();

        return response()->json($properties);
    }

    // GET single property
    public function show($id)
    {
        $property = RentalProperty::findOrFail($id);
        return response()->json($property);
    }

    // POST create property (landlord only)
    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'address'      => 'required|string',
            'city'         => 'required|string',
            'monthly_rent' => 'required|numeric|min:0',
            'type'         => 'required|in:apartment,house,room,condo,studio',
            'bedrooms'     => 'integer|min:0',
            'bathrooms'    => 'integer|min:1',
        ]);

        $property = RentalProperty::create([
            ...$request->all(),
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message'  => 'Property created successfully!',
            'property' => $property,
        ], 201);
    }

    // PUT update property (landlord only)
    public function update(Request $request, $id)
    {
        $property = RentalProperty::findOrFail($id);

        if ($property->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $property->update($request->all());

        return response()->json([
            'message'  => 'Property updated successfully!',
            'property' => $property,
        ]);
    }

    // DELETE property (landlord only)
    public function destroy(Request $request, $id)
    {
        $property = RentalProperty::findOrFail($id);

        if ($property->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $property->delete();

        return response()->json(['message' => 'Property deleted successfully!']);
    }

    // RANKING ALGORITHM
    private function calculateScore($property, $budget = null)
    {
        $score = 0;

        // Budget fit (40% of score)
        if ($budget) {
            $ratio = $budget / $property->monthly_rent;
            if ($ratio >= 1) {
                $score += min(40, $ratio * 20);
            } else {
                $score += max(0, $ratio * 20);
            }
        } else {
            $score += 20; // neutral if no budget given
        }

        // Amenities (60% of score)
        if ($property->has_wifi)        $score += 10;
        if ($property->has_water)       $score += 10;
        if ($property->has_electricity) $score += 10;
        if ($property->has_aircon)      $score += 8;
        if ($property->is_furnished)    $score += 8;
        if ($property->has_parking)     $score += 7;
        if ($property->is_pet_friendly) $score += 7;

        return round($score, 2);
    }

    // TIER SYSTEM
    private function getTier($score)
    {
        if ($score >= 70) return 'Optimal Fit';
        if ($score >= 40) return 'Budget Stretch';
        return 'High Risk';
    }
}