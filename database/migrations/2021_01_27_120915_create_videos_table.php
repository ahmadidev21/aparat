<?php

use App\Models\Video;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users', 'id')->onDelete('SET NULL')->onUpdate('CASCADE');
            $table->foreignId('category_id')->constrained('categories', 'id')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreignId('channel_category_id')->nullable()->constrained('categories', 'id')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->string('slug', 50);
            $table->string('title');
            $table->text('info')->nullable();
            $table->integer('duration');
            $table->string('banner')->nullable();
            $table->boolean('enable_comments')->default(true);
            $table->timestamp('publish_at')->nullable();
            $table->enum('state',Video::STATES)->default(Video::STATE_PENDING);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('videos');
    }
}
