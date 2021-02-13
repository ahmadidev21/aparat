<?php

use App\Models\Comment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreignId('video_id')->constrained('videos', 'id')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreignId('parent_id')->nullable()->constrained('comments', 'id')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->text('body');
            $table->enum('state',Comment::STATES)->default(Comment::STATE_PENDING);
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
        Schema::dropIfExists('comments');
    }
}
