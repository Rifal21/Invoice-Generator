<?php

namespace App\Services;

class GeolocationService
{
    /**
     * Calculate distance between two GPS coordinates using Haversine formula
     * Returns distance in meters
     */
    public static function calculateDistance(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $earthRadius = 6371000; // Earth's radius in meters

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($latFrom) * cos($latTo) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Check if coordinates are within allowed radius
     */
    public static function isWithinRadius(
        float $userLat,
        float $userLon,
        float $officeLat,
        float $officeLon,
        int $allowedRadius
    ): bool {
        $distance = self::calculateDistance($userLat, $userLon, $officeLat, $officeLon);
        return $distance <= $allowedRadius;
    }

    /**
     * Get human-readable distance
     */
    public static function formatDistance(float $meters): string
    {
        if ($meters < 1000) {
            return round($meters) . ' meter';
        }

        return round($meters / 1000, 2) . ' km';
    }
}
