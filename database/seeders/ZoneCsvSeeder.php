<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Zone;
use Illuminate\Support\Facades\DB;

class ZoneCsvSeeder extends Seeder
{
    /**
     * Path to CSV. Uses base_path('zones.csv') by default.
     */
    protected string $csvPath;

    /**
     * Whether to also populate `zone_points` table.
     */
    protected bool $insertPoints = true;

    public function __construct()
    {
        $this->csvPath = base_path('zones.csv');
    }

    public function run(): void
    {
        if (! file_exists($this->csvPath)) {
            $this->command->error("zones.csv not found at {$this->csvPath}");
            return;
        }

        $fh = fopen($this->csvPath, 'r');
        if (! $fh) {
            $this->command->error('Failed to open zones.csv');
            return;
        }

        // Read header row
        $header = fgetcsv($fh);
        if (! is_array($header)) {
            $this->command->error('Invalid CSV header');
            fclose($fh);
            return;
        }

        $inserted = 0; $skipped = 0; $errors = [];

        while (($row = fgetcsv($fh)) !== false) {
            $assoc = array_combine($header, $row);
            if ($assoc === false) continue; // skip malformed line

            $zoneName = trim($assoc['ZoneName'] ?? ($assoc['zone_name'] ?? ''));
            if (! $zoneName) {
                $skipped++;
                $errors[] = "Missing zone name on row";
                continue;
            }

            // Skip if zone already exists (idempotent)
            if (Zone::where('zone_name', $zoneName)->exists()) {
                $this->command->info("Skipping existing zone: {$zoneName}");
                $skipped++;
                continue;
            }

            // Try to extract lat/lon list for polygon from any column
            [$polygonCoords, $centroid] = $this->extractPolygonFromRow($assoc);

            if (! $polygonCoords) {
                // fallback: try parsing a single lat/lon pair from columns
                [$lat, $lon] = $this->extractSingleLatLon($assoc);
                if (! is_null($lat) && ! is_null($lon)) {
                    $centroid = [$lat, $lon];
                }
            }

            // Validate UK bbox for polygon / centroid
            if ($polygonCoords) {
                if (! $this->coordsWithinUk($polygonCoords)) {
                    $skipped++;
                    $errors[] = "{$zoneName}: polygon outside UK bbox";
                    continue;
                }
            } elseif (! $centroid) {
                $skipped++;
                $errors[] = "{$zoneName}: no coordinates found";
                continue;
            }

            // Build GeoJSON polygon if coords found
            $geojson = null;
            if ($polygonCoords) {
                // ensure ring closed
                $first = $polygonCoords[0];
                $last = $polygonCoords[count($polygonCoords)-1];
                if ($first[0] !== $last[0] || $first[1] !== $last[1]) {
                    $polygonCoords[] = $first;
                }
                $geojson = ['type' => 'Polygon', 'coordinates' => [ $polygonCoords ]];
            }

            // Overlap check: load existing polygons and test
            try {
                if ($geojson) {
                    $incomingBbox = $this->polygonBbox($geojson);
                    $zones = Zone::whereNotNull('meta')->get()->filter(function($z){ return isset($z->meta['polygon']) && is_array($z->meta['polygon']); });
                    $conflict = false;
                    foreach ($zones as $existing) {
                        $existingPoly = $existing->meta['polygon'] ?? null;
                        if (! $existingPoly) continue;
                        $existingBbox = $this->polygonBbox($existingPoly);
                        if ($this->bboxesIntersect($incomingBbox, $existingBbox) && $this->polygonsIntersect($geojson, $existingPoly)) {
                            $errors[] = "{$zoneName}: overlaps existing zone {$existing->zone_name}";
                            $conflict = true;
                            break;
                        }
                    }
                    if ($conflict) { $skipped++; continue; }
                }

                // Save zone in DB
                DB::beginTransaction();
                $zone = Zone::create([
                    'zone_name' => $zoneName,
                    'latitude' => $centroid[0] ?? null,
                    'longitude' => $centroid[1] ?? null,
                    'meta' => $geojson ? ['polygon' => $geojson] : null,
                    'status' => 'active'
                ]);

                // optionally insert zone_points
                if ($this->insertPoints && $polygonCoords) {
                    $points = [];
                    foreach ($polygonCoords as $i => $pt) {
                        // pt is [lng,lat] — zone_points stores latitude/longitude
                        $points[] = [
                            'zone_id' => $zone->id,
                            'latitude' => (float)$pt[1],
                            'longitude' => (float)$pt[0],
                            'point_order' => $i + 1,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                    if (! empty($points)) {
                        DB::table('zone_points')->insert($points);
                    }
                }

                DB::commit();
                $inserted++;
                $this->command->info("Inserted zone: {$zoneName}");
            } catch (\Exception $e) {
                DB::rollBack();
                $skipped++;
                $errors[] = "{$zoneName}: failed to insert ({$e->getMessage()})";
                continue;
            }
        }

        fclose($fh);

        $this->command->info("Seeding complete. Inserted: {$inserted}, Skipped: {$skipped}");
        if (! empty($errors)) {
            $this->command->warn('Some rows were skipped or had errors:');
            foreach ($errors as $err) $this->command->line(' - ' . $err);
        }
    }

    // Attempt to find polygon coordinate list in the CSV row
    protected function extractPolygonFromRow(array $row): array
    {
        $candidateLat = null; $candidateLon = null;

        foreach ($row as $k => $v) {
            if (! is_string($v) || trim($v) === '') continue;
            // find all numbers in this field
            preg_match_all('/-?\d+\.\d+|-?\d+/', $v, $m);
            $nums = array_map(fn($s) => (float)$s, $m[0] ?? []);
            if (count($nums) < 2) continue;

            // heuristics: if median is between 49..61 -> lat list
            $avg = array_sum($nums) / count($nums);
            if ($avg >= 49.0 && $avg <= 61.0 && count($nums) >= 3) {
                // split as lat list
                $candidateLat = $nums;
                continue;
            }
            // if avg between -9 and 3 -> lon list
            if ($avg >= -9.0 && $avg <= 3.5 && count($nums) >= 3) {
                $candidateLon = $nums;
                continue;
            }

            // sometimes the field contains an alternating lat,lon sequence
            if ($avg >= -90 && $avg <= 90 && count($nums) >= 6) {
                // try to pair as lat/lon alternating
                $pairs = [];
                for ($i = 0; $i+1 < count($nums); $i += 2) {
                    $lat = $nums[$i]; $lon = $nums[$i+1];
                    // if lat is in lat range and lon is in lon range
                    if ($lat >= 49 && $lat <= 61 && $lon >= -9 && $lon <= 4) {
                        $pairs[] = [$lon, $lat];
                    } elseif ($nums[$i] >= -9 && $nums[$i] <= 4 && $nums[$i+1] >= 49 && $nums[$i+1] <= 61) {
                        $pairs[] = [$nums[$i], $nums[$i+1]]; // (lon,lat)
                    }
                }
                if (count($pairs) >= 3) {
                    return [$pairs, [ array_sum(array_column($pairs,1)) / count($pairs), array_sum(array_column($pairs,0)) / count($pairs) ]];
                }
            }
        }

        // if we found separate lat and lon candidate arrays of same length, pair them
        if ($candidateLat && $candidateLon && count($candidateLat) === count($candidateLon)) {
            $coords = [];
            for ($i=0;$i<count($candidateLat);$i++) {
                $coords[] = [ (float)$candidateLon[$i], (float)$candidateLat[$i] ];
            }
            return [$coords, [ array_sum($candidateLat)/count($candidateLat), array_sum($candidateLon)/count($candidateLon) ]];
        }

        return [null, null];
    }

    // try to find a single latitude & longitude pair in row
    protected function extractSingleLatLon(array $row): array
    {
        foreach ($row as $v) {
            if (! is_string($v)) continue;
            preg_match_all('/-?\d+\.\d+|-?\d+/', $v, $m);
            $nums = array_map(fn($s)=>(float)$s, $m[0] ?? []);
            if (count($nums) === 2) {
                $a = $nums[0]; $b = $nums[1];
                if ($a >= 49 && $a <= 61 && $b >= -9 && $b <= 4) return [$a,$b];
                if ($b >= 49 && $b <= 61 && $a >= -9 && $a <= 4) return [$b,$a];
            }
            if (count($nums) === 1) {
                // sometimes lat and lon are in different fields nearby; skip here
                continue;
            }
        }

        // try scanning for adjacent fields that look like lat then lon
        $values = array_values($row);
        for ($i=0;$i<count($values)-1;$i++) {
            $a = $values[$i]; $b = $values[$i+1];
            preg_match_all('/-?\d+\.\d+|-?\d+/', $a, $ma); preg_match_all('/-?\d+\.\d+|-?\d+/', $b, $mb);
            if (count($ma[0])===1 && count($mb[0])===1) {
                $na=(float)$ma[0][0]; $nb=(float)$mb[0][0];
                if ($na>=49 && $na<=61 && $nb>=-9 && $nb<=4) return [$na,$nb];
                if ($nb>=49 && $nb<=61 && $na>=-9 && $na<=4) return [$nb,$na];
            }
        }

        return [null,null];
    }

    protected function coordsWithinUk(array $coords): bool
    {
        foreach ($coords as $pt) {
            $lng = $pt[0]; $lat = $pt[1];
            if ($lat < 49.5 || $lat > 61.0 || $lng < -8.6 || $lng > 2.1) return false;
        }
        return true;
    }

    // --- Reuse the controller geometry helpers (copied) ---
    protected function polygonBbox(array $poly)
    {
        if (!isset($poly['coordinates'][0]) || !is_array($poly['coordinates'][0])) return null;
        $minX = $minY = PHP_FLOAT_MAX; $maxX = $maxY = -PHP_FLOAT_MAX;
        foreach ($poly['coordinates'][0] as $pt) {
            $lng = (float) ($pt[0] ?? 0);
            $lat = (float) ($pt[1] ?? 0);
            if ($lng < $minX) $minX = $lng;
            if ($lng > $maxX) $maxX = $lng;
            if ($lat < $minY) $minY = $lat;
            if ($lat > $maxY) $maxY = $lat;
        }
        return [$minX, $minY, $maxX, $maxY];
    }

    protected function bboxesIntersect($a, $b)
    {
        if (!$a || !$b) return false;
        return !($a[2] < $b[0] || $a[0] > $b[2] || $a[3] < $b[1] || $a[1] > $b[3]);
    }

    protected function polygonsIntersect(array $polyA, array $polyB)
    {
        try {
            $coordsA = $polyA['coordinates'][0] ?? [];
            $coordsB = $polyB['coordinates'][0] ?? [];
            if (!$coordsA || !$coordsB) return false;
            $closeRing = function(&$coords){ if (count($coords) > 0) { $first = $coords[0]; $last = $coords[count($coords)-1]; if ($first[0] !== $last[0] || $first[1] !== $last[1]) $coords[] = $first; } };
            $closeRing($coordsA); $closeRing($coordsB);
            for ($i = 0; $i < count($coordsA)-1; $i++) {
                $p1 = $coordsA[$i]; $p2 = $coordsA[$i+1];
                for ($j = 0; $j < count($coordsB)-1; $j++) {
                    $q1 = $coordsB[$j]; $q2 = $coordsB[$j+1];
                    if ($this->segmentsIntersect($p1, $p2, $q1, $q2)) return true;
                }
            }
            foreach ($coordsA as $pt) { if ($this->pointInPolygon($pt, $coordsB)) return true; }
            foreach ($coordsB as $pt) { if ($this->pointInPolygon($pt, $coordsA)) return true; }
            return false;
        } catch (\Exception $e) { return true; }
    }

    protected function pointInPolygon(array $point, array $ring)
    {
        $x = $point[0]; $y = $point[1];
        $inside = false;
        $j = count($ring) - 1;
        for ($i = 0; $i < count($ring); $i++) {
            $xi = $ring[$i][0]; $yi = $ring[$i][1];
            $xj = $ring[$j][0]; $yj = $ring[$j][1];
            $intersect = (($yi > $y) != ($yj > $y)) && ($x < ($xj - $xi) * ($y - $yi) / (($yj - $yi) ?: 1e-12) + $xi);
            if ($intersect) $inside = !$inside;
            $j = $i;
        }
        return $inside;
    }

    protected function segmentsIntersect(array $p1, array $p2, array $q1, array $q2)
    {
        $orient = function($a, $b, $c){ return ($b[1] - $a[1]) * ($c[0] - $b[0]) - ($b[0] - $a[0]) * ($c[1] - $b[1]); };
        $onSegment = function($a, $b, $c){ return ($c[0] <= max($a[0], $b[0]) && $c[0] >= min($a[0], $b[0]) && $c[1] <= max($a[1], $b[1]) && $c[1] >= min($a[1], $b[1])); };
        $o1 = $orient($p1, $p2, $q1); $o2 = $orient($p1, $p2, $q2); $o3 = $orient($q1, $q2, $p1); $o4 = $orient($q1, $q2, $p2);
        if (($o1 > 0 && $o2 < 0 || $o1 < 0 && $o2 > 0) && ($o3 > 0 && $o4 < 0 || $o3 < 0 && $o4 > 0)) return true;
        if (abs($o1) < 1e-12 && $onSegment($p1, $p2, $q1)) return true;
        if (abs($o2) < 1e-12 && $onSegment($p1, $p2, $q2)) return true;
        if (abs($o3) < 1e-12 && $onSegment($q1, $q2, $p1)) return true;
        if (abs($o4) < 1e-12 && $onSegment($q1, $q2, $p2)) return true;
        return false;
    }
}
