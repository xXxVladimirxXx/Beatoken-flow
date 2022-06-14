<?php

namespace App\Http\Controllers\Api;

use App\Models\{Setting, DropLine, Drop};
use Carbon\Carbon;
use Auth;

class DropLineController extends Controller
{
    public function storeByDrop(Drop $drop) {
        if (DropLine::where(['drop_id' => $drop->id, 'user_id' => Auth::id()])->first()) {return response()->json(['message' => 'drop line already exist'], 200);}

        $dropLinesCount = DropLine::where('drop_id', $drop->id)->count();
        $releaseStart = Carbon::parse($drop->release_start);

        $dropListData = [
            'drop_id' => $drop->id,
            'user_id' => Auth::id(),
        ];

        if ($dropLinesCount && Carbon::now() > $releaseStart) {
            $lastLine = DropLine::where(['drop_id' => $drop->id])->latest('number_line')->first();
            $keys_number = Setting::where('key', 'keys_number')->pluck('value')->first();
            $time_drop_line = Setting::where('key', 'default_time_drop_line')->pluck('value')->first();
            $time_drop_payment = Setting::where('key', 'default_time_drop_payment')->pluck('value')->first();

            $timeToPayLastLine = Carbon::parse($lastLine->time_line)->addSeconds($time_drop_payment);

            if (Carbon::now() >= $timeToPayLastLine) {
                DropLine::where('drop_id', $drop->id)->delete();
                $dropListData['time_line'] = Carbon::now();
                $dropListData['number_line'] = 1;
            } else {
                $waitingTime = floor($dropLinesCount / $keys_number) * $time_drop_line;
                $dropListData['time_line'] = Carbon::now()->addSeconds($waitingTime);
                $dropListData['number_line'] = $lastLine->number_line + 1;
            }
        } else {
            $dropListData['time_line'] = (Carbon::now() >= $releaseStart) ? Carbon::now() : $releaseStart;
            $dropListData['number_line'] = 1;
        }

        DropLine::create($dropListData);

        return response()->json(['message' => 'drop line created']);
    }

    public function myTimeToStartBuyDrop($drop_id) {
        if (!$myDropLine = DropLine::where(['drop_id' => $drop_id, 'user_id' => Auth::id()])->first())
            return response()->json(['redirectToDrop' => true]);

        if ($now = Carbon::now() >= $timeLine = Carbon::parse($myDropLine->time_line))
            return response()->json(['redirectToBuy' => true]);

        $timeToStartBuy = $timeLine->diffInSeconds($now);

        return response()->json([
            'dropLine' => $myDropLine,
            'timeToStartBuy' => $timeToStartBuy,
            'dropLinesCount' => Drop::find($drop_id)->dropLines()->count() - 1
        ], 200);
    }

    public function myTimeToBuyDrop($drop_id) {
        if (!$myDropLine = DropLine::where(['drop_id' => $drop_id, 'user_id' => Auth::id()])->first())
            return response()->json(['redirectToDrop' => true]);

        $defaultTimeDropPayment = Setting::where('key', 'default_time_drop_payment')->pluck('value')->first();

        if ($now = Carbon::now() >= $timeLine = Carbon::parse($myDropLine->time_line)->addSeconds($defaultTimeDropPayment)) {
            $myDropLine->delete();
            return response()->json(['redirectToLine' => true]);
        }

        $timeToBuy = $timeLine->diffInSeconds($now);

        return response()->json([
            'dropLine' => $myDropLine,
            'timeToBuy' => $timeToBuy
        ]);
    }

    public function destroyByDrop($drop_id) {
        return response()->json(DropLine::where(['drop_id' => $drop_id, 'user_id' => Auth::id()])->delete());
    }
}
