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
            $table->unsignedTinyInteger('content_layout_columns')
                ->nullable()
                ->after('why_us_heading');

            $table->string('center_title')->nullable()->after('right_title');
            $table->text('center_description')->nullable()->after('right_description');

            $table->longText('one_column_html_template')->nullable()->after('bottom_description');
            $table->longText('one_column_css_template')->nullable()->after('one_column_html_template');
            $table->longText('two_column_html_template')->nullable()->after('one_column_css_template');
            $table->longText('two_column_css_template')->nullable()->after('two_column_html_template');
            $table->longText('three_column_html_template')->nullable()->after('two_column_css_template');
            $table->longText('three_column_css_template')->nullable()->after('three_column_html_template');
        });

        $oneColumnHtml = <<<'HTML'
<div class="about-content pt-4">
    <div class="section-title-content">
        <h3 class="wow fadeInUp" data-wow-delay=".4s">{{main_title}}</h3>
    </div>
    <div class="mt-1 mt-md-0 wow fadeInUp" data-wow-delay=".6s">{{main_description_html}}</div>
</div>
HTML;

        $twoColumnHtml = <<<'HTML'
<div class="row g-4">
    <div class="col-md-6">
        <div class="about-content pt-4">
            <div class="section-title-content">
                <h4 class="wow fadeInUp" data-wow-delay=".4s">{{left_title}}</h4>
            </div>
            <div class="mt-1 mt-md-0 wow fadeInUp" data-wow-delay=".6s">{{left_description_html}}</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="about-content pt-4">
            <div class="section-title-content">
                <h4 class="wow fadeInUp" data-wow-delay=".4s">{{right_title}}</h4>
            </div>
            <div class="mt-1 mt-md-0 wow fadeInUp" data-wow-delay=".6s">{{right_description_html}}</div>
        </div>
    </div>
</div>
HTML;

        $threeColumnHtml = <<<'HTML'
<div class="row g-4">
    <div class="col-lg-4 col-md-6">
        <div class="about-content pt-4">
            <div class="section-title-content">
                <h4 class="wow fadeInUp" data-wow-delay=".4s">{{left_title}}</h4>
            </div>
            <div class="mt-1 mt-md-0 wow fadeInUp" data-wow-delay=".6s">{{left_description_html}}</div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="about-content pt-4">
            <div class="section-title-content">
                <h4 class="wow fadeInUp" data-wow-delay=".4s">{{center_title}}</h4>
            </div>
            <div class="mt-1 mt-md-0 wow fadeInUp" data-wow-delay=".6s">{{center_description_html}}</div>
        </div>
    </div>
    <div class="col-lg-4 col-md-12">
        <div class="about-content pt-4">
            <div class="section-title-content">
                <h4 class="wow fadeInUp" data-wow-delay=".4s">{{right_title}}</h4>
            </div>
            <div class="mt-1 mt-md-0 wow fadeInUp" data-wow-delay=".6s">{{right_description_html}}</div>
        </div>
    </div>
</div>
HTML;

        DB::table('pages')->update([
            'one_column_html_template' => $oneColumnHtml,
            'one_column_css_template' => null,
            'two_column_html_template' => $twoColumnHtml,
            'two_column_css_template' => null,
            'three_column_html_template' => $threeColumnHtml,
            'three_column_css_template' => null,
        ]);
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn([
                'content_layout_columns',
                'center_title',
                'center_description',
                'one_column_html_template',
                'one_column_css_template',
                'two_column_html_template',
                'two_column_css_template',
                'three_column_html_template',
                'three_column_css_template',
            ]);
        });
    }
};
