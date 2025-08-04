<?php

namespace App\Http\Controllers;

use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class WordGuessController extends Controller
{
    /**
     * Submit a guess and get the result.
     */
    public function submit(Request $request) {
        try {
            $request->validate([
                'guess' => 'required|string|min:5|max:5',
            ]);

            $guess = strtolower($request->input('guess'));
            $todayWord = Word::whereDate('active_on', Carbon::today())->first();

            if (!$todayWord) {
                return response()->json(['message' => 'No word found for today'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        $solution = strtolower($todayWord->word);
        $result = [];

        $solutionLetterCounts = count_chars($solution, 1);

        // first pass: correct positions
        for ($i = 0; $i < 5; $i++) {
            if ($guess[$i] === $solution[$i]) {
                $result[$i] = ['letter' => $guess[$i], 'status' => 'green'];
                $solutionLetterCounts[ord($guess[$i])]--;
            }
        }

        // second pass: misplaced (yellow) and incorrect (gray)
        for ($i = 0; $i < 5; $i++) {
            if (!isset($result[$i])) {
                $letter = $guess[$i];
                $ascii = ord($letter);

                if (!empty($solutionLetterCounts[$ascii])) {
                    $result[$i] = ['letter' => $letter, 'status' => 'yellow'];
                    $solutionLetterCounts[$ascii]--;
                } else {
                    $result[$i] = ['letter' => $letter, 'status' => 'gray'];
                }
            }
        }

        return response()->json([
            'result' => $result,
            'solution' => $solution
        ]);
    }
}
