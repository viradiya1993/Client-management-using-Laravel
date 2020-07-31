<?php

use App\FrontDetail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSocialLinksColumnInFrontDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('front_details', 'social_links')) {
            Schema::table('front_details', function (Blueprint $table) {
                $table->text('social_links')->after('email')->nullable();
            });
        }

        $front_details = FrontDetail::first();

        if ($front_details) {
            $front_details->social_links = json_encode([
                ['name' => 'facebook', 'link' => 'https://facebook.com'],
                ['name' => 'twitter', 'link' => 'https://twitter.com'],
                ['name' => 'instagram', 'link' => 'https://instagram.com'],
                ['name' => 'dribbble', 'link' => 'https://dribbble.com']
            ]);

            $front_details->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('front_details', function (Blueprint $table) {
            $table->dropColumn('social_links');
        });
    }
}
