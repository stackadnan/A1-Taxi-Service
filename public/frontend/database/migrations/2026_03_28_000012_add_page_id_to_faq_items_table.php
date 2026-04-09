<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('faq_items', 'page_id')) {
            Schema::table('faq_items', function (Blueprint $table) {
                $table->unsignedBigInteger('page_id')->nullable()->after('id');
                $table->index('page_id');
            });
        }

        if (Schema::hasColumn('faq_items', 'page_id')) {
            $hasPageSpecificFaqs = DB::table('faq_items')->whereNotNull('page_id')->exists();
            if ($hasPageSpecificFaqs) {
                return;
            }

            $templateFaqs = DB::table('faq_items')
                ->whereNull('page_id')
                ->orderBy('order')
                ->get(['question', 'answer', 'order']);

            if ($templateFaqs->isEmpty()) {
                return;
            }

            $pages = DB::table('pages')->orderBy('id')->get(['id']);
            if ($pages->isEmpty()) {
                return;
            }

            $insertRows = [];
            foreach ($pages as $page) {
                foreach ($templateFaqs as $faq) {
                    $insertRows[] = [
                        'page_id' => $page->id,
                        'question' => $faq->question,
                        'answer' => $faq->answer,
                        'order' => $faq->order,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($insertRows)) {
                DB::table('faq_items')->insert($insertRows);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('faq_items', 'page_id')) {
            Schema::table('faq_items', function (Blueprint $table) {
                $table->dropIndex(['page_id']);
                $table->dropColumn('page_id');
            });
        }
    }
};
