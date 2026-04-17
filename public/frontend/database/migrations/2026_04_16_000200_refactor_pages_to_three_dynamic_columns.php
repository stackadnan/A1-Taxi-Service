<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (!Schema::hasColumn('pages', 'one_column')) {
                $table->longText('one_column')->nullable()->after('content_layout_columns');
            }

            if (!Schema::hasColumn('pages', 'two_column')) {
                $table->longText('two_column')->nullable()->after('one_column');
            }

            if (!Schema::hasColumn('pages', 'three_column')) {
                $table->longText('three_column')->nullable()->after('two_column');
            }
        });

        if (Schema::hasColumn('pages', 'one_column_html_template')) {
            DB::statement('UPDATE pages SET one_column = COALESCE(one_column, one_column_html_template)');
        }

        if (Schema::hasColumn('pages', 'two_column_html_template')) {
            DB::statement('UPDATE pages SET two_column = COALESCE(two_column, two_column_html_template)');
        }

        if (Schema::hasColumn('pages', 'three_column_html_template')) {
            DB::statement('UPDATE pages SET three_column = COALESCE(three_column, three_column_html_template)');
        }

        $buildContent = static function (string $headingTag, ?string $title, ?string $description): string {
            $safeTitle = htmlspecialchars((string) ($title ?? ''), ENT_QUOTES, 'UTF-8');
            $htmlDescription = (string) ($description ?? '');

            return '<div class="about-content pt-4">'
                .'<div class="section-title-content">'
                ."<{$headingTag} class=\"wow fadeInUp\" data-wow-delay=\".4s\">{$safeTitle}</{$headingTag}>"
                .'</div>'
                ."<div class=\"mt-1 mt-md-0 wow fadeInUp\" data-wow-delay=\".6s\">{$htmlDescription}</div>"
                .'</div>';
        };

        $buildOneColumn = static function (?string $title, ?string $description) use ($buildContent): string {
            return '<div class="row"><div class="col-12">'
                .$buildContent('h3', $title, $description)
                .'</div></div>';
        };

        $buildTwoColumn = static function (?string $leftTitle, ?string $leftDescription, ?string $rightTitle, ?string $rightDescription) use ($buildContent): string {
            return '<div class="row g-4">'
                .'<div class="col-md-6">'.$buildContent('h4', $leftTitle, $leftDescription).'</div>'
                .'<div class="col-md-6">'.$buildContent('h4', $rightTitle, $rightDescription).'</div>'
                .'</div>';
        };

        $buildThreeColumn = static function (?string $firstTitle, ?string $firstDescription, ?string $secondTitle, ?string $secondDescription, ?string $thirdTitle, ?string $thirdDescription) use ($buildContent): string {
            return '<div class="row g-4">'
                .'<div class="col-lg-4 col-md-6">'.$buildContent('h4', $firstTitle, $firstDescription).'</div>'
                .'<div class="col-lg-4 col-md-6">'.$buildContent('h4', $secondTitle, $secondDescription).'</div>'
                .'<div class="col-lg-4 col-md-12">'.$buildContent('h4', $thirdTitle, $thirdDescription).'</div>'
                .'</div>';
        };

        $pageRows = DB::table('pages')
            ->select([
                'id',
                'content_layout_columns',
                'one_column',
                'two_column',
                'three_column',
                'main_title',
                'main_description',
                'left_title',
                'left_description',
                'center_title',
                'center_description',
                'right_title',
                'right_description',
                'bottom_title',
                'bottom_description',
            ])
            ->get();

        foreach ($pageRows as $page) {
            $updates = [];

            if (!is_string($page->one_column) || trim($page->one_column) === '') {
                $updates['one_column'] = $buildOneColumn($page->main_title, $page->main_description);
            }

            if (!is_string($page->two_column) || trim($page->two_column) === '') {
                $updates['two_column'] = $buildTwoColumn(
                    $page->left_title,
                    $page->left_description,
                    $page->right_title,
                    $page->right_description
                );
            }

            if (!is_string($page->three_column) || trim($page->three_column) === '') {
                $secondTitle = is_string($page->center_title) && trim($page->center_title) !== ''
                    ? $page->center_title
                    : $page->right_title;
                $secondDescription = is_string($page->center_description) && trim($page->center_description) !== ''
                    ? $page->center_description
                    : $page->right_description;

                $thirdTitle = is_string($page->bottom_title) && trim($page->bottom_title) !== ''
                    ? $page->bottom_title
                    : $page->right_title;
                $thirdDescription = is_string($page->bottom_description) && trim($page->bottom_description) !== ''
                    ? $page->bottom_description
                    : $page->right_description;

                $updates['three_column'] = $buildThreeColumn(
                    $page->left_title,
                    $page->left_description,
                    $secondTitle,
                    $secondDescription,
                    $thirdTitle,
                    $thirdDescription
                );
            }

            if (!in_array((int) $page->content_layout_columns, [1, 2, 3], true)) {
                $updates['content_layout_columns'] = 1;
            }

            if ($updates !== []) {
                DB::table('pages')->where('id', $page->id)->update($updates);
            }
        }

        $columnsToDrop = [
            'main_title',
            'main_description',
            'left_title',
            'left_description',
            'center_title',
            'center_description',
            'right_title',
            'right_description',
            'bottom_title',
            'bottom_description',
            'one_column_html_template',
            'one_column_css_template',
            'two_column_html_template',
            'two_column_css_template',
            'three_column_html_template',
            'three_column_css_template',
        ];

        $existingColumns = array_values(array_filter(
            $columnsToDrop,
            static fn (string $column): bool => Schema::hasColumn('pages', $column)
        ));

        if ($existingColumns !== []) {
            Schema::table('pages', function (Blueprint $table) use ($existingColumns) {
                $table->dropColumn($existingColumns);
            });
        }
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (!Schema::hasColumn('pages', 'main_title')) {
                $table->string('main_title')->nullable();
            }
            if (!Schema::hasColumn('pages', 'main_description')) {
                $table->text('main_description')->nullable();
            }
            if (!Schema::hasColumn('pages', 'left_title')) {
                $table->string('left_title')->nullable();
            }
            if (!Schema::hasColumn('pages', 'left_description')) {
                $table->text('left_description')->nullable();
            }
            if (!Schema::hasColumn('pages', 'center_title')) {
                $table->string('center_title')->nullable();
            }
            if (!Schema::hasColumn('pages', 'center_description')) {
                $table->text('center_description')->nullable();
            }
            if (!Schema::hasColumn('pages', 'right_title')) {
                $table->string('right_title')->nullable();
            }
            if (!Schema::hasColumn('pages', 'right_description')) {
                $table->text('right_description')->nullable();
            }
            if (!Schema::hasColumn('pages', 'bottom_title')) {
                $table->string('bottom_title')->nullable();
            }
            if (!Schema::hasColumn('pages', 'bottom_description')) {
                $table->text('bottom_description')->nullable();
            }
            if (!Schema::hasColumn('pages', 'one_column_html_template')) {
                $table->longText('one_column_html_template')->nullable();
            }
            if (!Schema::hasColumn('pages', 'one_column_css_template')) {
                $table->longText('one_column_css_template')->nullable();
            }
            if (!Schema::hasColumn('pages', 'two_column_html_template')) {
                $table->longText('two_column_html_template')->nullable();
            }
            if (!Schema::hasColumn('pages', 'two_column_css_template')) {
                $table->longText('two_column_css_template')->nullable();
            }
            if (!Schema::hasColumn('pages', 'three_column_html_template')) {
                $table->longText('three_column_html_template')->nullable();
            }
            if (!Schema::hasColumn('pages', 'three_column_css_template')) {
                $table->longText('three_column_css_template')->nullable();
            }
        });

        if (Schema::hasColumn('pages', 'one_column') && Schema::hasColumn('pages', 'one_column_html_template')) {
            DB::statement('UPDATE pages SET one_column_html_template = COALESCE(one_column_html_template, one_column)');
        }

        if (Schema::hasColumn('pages', 'two_column') && Schema::hasColumn('pages', 'two_column_html_template')) {
            DB::statement('UPDATE pages SET two_column_html_template = COALESCE(two_column_html_template, two_column)');
        }

        if (Schema::hasColumn('pages', 'three_column') && Schema::hasColumn('pages', 'three_column_html_template')) {
            DB::statement('UPDATE pages SET three_column_html_template = COALESCE(three_column_html_template, three_column)');
        }

        $newColumns = ['one_column', 'two_column', 'three_column'];

        $existingColumns = array_values(array_filter(
            $newColumns,
            static fn (string $column): bool => Schema::hasColumn('pages', $column)
        ));

        if ($existingColumns !== []) {
            Schema::table('pages', function (Blueprint $table) use ($existingColumns) {
                $table->dropColumn($existingColumns);
            });
        }
    }
};
