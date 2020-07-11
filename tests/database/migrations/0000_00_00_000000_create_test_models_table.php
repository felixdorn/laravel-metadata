<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestModelsTable extends Migration
{
    public function up()
    {
        Schema::create('test_models', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->metadata();
            $blueprint->timestamps();
        });
    }
}
