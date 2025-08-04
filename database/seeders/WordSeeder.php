<?php

namespace Database\Seeders;

use App\Models\Word;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $words = [
            'blink', 'lance', 'table', 'balmy', 'blowy', 'close', 'crisp', 'dirty',
            'foggy', 'fresh', 'humid', 'misty', 'moist', 'apple', 'anger', 'bingo',
            'radar', 'disco', 'kitty', 'cruel', 'roast', 'study', 'poker', 'fever'
        ];

        $currentDate = Carbon::today();

        foreach ($words as $word) {
            Word::updateOrCreate([
                'word' => $word,
                'active_on' => $currentDate->toDateString()
            ]);

            $currentDate->addDay();
        }
    }
}
