<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $buildContent = static function (string $headingTag, string $title, string $description): string {
            $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

            return '<div class="about-content pt-4">'
                .'<div class="section-title-content">'
                ."<{$headingTag} class=\"wow fadeInUp\" data-wow-delay=\".4s\">{$safeTitle}</{$headingTag}>"
                .'</div>'
                ."<div class=\"mt-1 mt-md-0 wow fadeInUp\" data-wow-delay=\".6s\">{$description}</div>"
                .'</div>';
        };

        $buildOneColumn = static function (string $title, string $description) use ($buildContent): string {
            return '<div class="row"><div class="col-12">'
                .$buildContent('h3', $title, $description)
                .'</div></div>';
        };

        $buildTwoColumn = static function (string $leftTitle, string $leftDescription, string $rightTitle, string $rightDescription) use ($buildContent): string {
            return '<div class="row g-4">'
                .'<div class="col-md-6">'.$buildContent('h4', $leftTitle, $leftDescription).'</div>'
                .'<div class="col-md-6">'.$buildContent('h4', $rightTitle, $rightDescription).'</div>'
                .'</div>';
        };

        $buildThreeColumn = static function (string $firstTitle, string $firstDescription, string $secondTitle, string $secondDescription, string $thirdTitle, string $thirdDescription) use ($buildContent): string {
            return '<div class="row g-4">'
                .'<div class="col-lg-4 col-md-6">'.$buildContent('h4', $firstTitle, $firstDescription).'</div>'
                .'<div class="col-lg-4 col-md-6">'.$buildContent('h4', $secondTitle, $secondDescription).'</div>'
                .'<div class="col-lg-4 col-md-12">'.$buildContent('h4', $thirdTitle, $thirdDescription).'</div>'
                .'</div>';
        };

        $isPlaceholderContent = static function (?string $value): bool {
            if (!is_string($value) || trim($value) === '') {
                return true;
            }

            return str_contains($value, '{{') || str_contains($value, '}}');
        };

        $rows = DB::table('pages')->select(['id', 'name', 'number_of_rows', 'one_column', 'two_column', 'three_column'])->get();

        foreach ($rows as $row) {
            $name = is_string($row->name) && trim($row->name) !== '' ? trim($row->name) : 'Airport';

            $mainTitle = "Reliable {$name} Transfers";
            $mainDescription = "<strong>A1 Airport Cars</strong> provides professional and reliable {$name} transfer services. Whether you are arriving in the city or heading to catch a flight, our service ensures a smooth and comfortable journey.";
            $leftTitle = "Professional {$name} Drivers";
            $leftDescription = "Our experienced drivers are fully licensed and highly familiar with {$name} routes, pickup points, and surrounding areas. They monitor traffic and journeys to ensure timely pickups.";
            $rightTitle = "Comfortable Vehicles for {$name} Transfers";
            $rightDescription = 'From affordable saloon cars for individuals to executive vehicles and spacious MPVs for families and groups, our fleet is maintained to high standards with ample luggage space.';
            $bottomTitle = "Simple Booking for {$name} Transfers";
            $bottomDescription = "Booking your {$name} transfer with A1 Airport Cars is quick and easy. Reserve online in minutes or contact our support team for assistance.";

            $updates = [];

            if ($isPlaceholderContent($row->one_column)) {
                $updates['one_column'] = $buildOneColumn($mainTitle, $mainDescription);
            }

            if ($isPlaceholderContent($row->two_column)) {
                $updates['two_column'] = $buildTwoColumn($leftTitle, $leftDescription, $rightTitle, $rightDescription);
            }

            if ($isPlaceholderContent($row->three_column)) {
                $updates['three_column'] = $buildThreeColumn($leftTitle, $leftDescription, $rightTitle, $rightDescription, $bottomTitle, $bottomDescription);
            }

            $numberOfRows = is_string($row->number_of_rows) ? trim($row->number_of_rows) : '';
            if ($numberOfRows === '' || !preg_match('/^[123](\s*,\s*[123])*$/', $numberOfRows)) {
                $updates['number_of_rows'] = '1,2,1';
            }

            if ($updates !== []) {
                DB::table('pages')->where('id', $row->id)->update($updates);
            }
        }
    }

    public function down(): void
    {
        // This data-fix migration is intentionally not reverted.
    }
};
