<?php

namespace InetStudio\Hashtags\Http\Controllers\Front;

use App\Http\Controllers\Controller;

class TagsController extends Controller
{
    /*
    public function info(Request $request)
    {
        $tagId = trim($request->get('id'));
        $tagName = trim($request->get('tag'));

        $tag = TagModel::where('id', '=', $tagId)->get();

        if ($tag->count() == 0) {
            $tag = TagModel::where('name', '=', $tagName)->get();
        }



        if ($tag->count() > 0) {
            $tag = $tag->first();

            $points = (! empty($city)) ? $city->points : 0;
            $rating = TagModel::orderBy('points', 'desc')->where('id', '<>', $cityId)->get()->take(3)->reverse();
        } else {
            $city = null;
            $points = 0;
            $rating = TagModel::orderBy('points', 'desc')->get()->take(3)->reverse();
        }

        $delta = 0;
        foreach ($rating as $top) {
            if ($points < $top->points) {
                $delta = ($top->points - $points);
                break;
            }
        }

        if ($city) {
            if ($city->id != TagModel::orderBy('points', 'desc')->get()->take(1)->first()->id) {
                $delta++;
            }
        } else {
            $delta++;
        }

        $data = [];
        $data[] = [
            'id' => ($city) ? $city->id : $request->get('id'),
            'city' => ($city) ? $city->name : $request->get('city'),
            'rating' => $delta,
            'rating_word' => $this->getPointsWord($delta),
        ];

        return response()->json($data);
    }

    private function get_correct_str($num, $str1, $str2, $str3) {
        $val = $num % 100;

        if ($val > 10 && $val < 20) return $str3;
        else {
            $val = $num % 10;
            if ($val == 1) return $str1;
            elseif ($val > 1 && $val < 5) return $str2;
            else return $str3;
        }
    }

    private function getPointsWord($num) {
        $num = (!$num) ? 0 : $num;
        return $this->get_correct_str($num, 'балл', 'балла', 'баллов');
    }

    public function vote(Request $request)
    {
        if (Auth::guest()) {
            return;
        }

        $user = Auth::user();

        $tagId = trim($request->get('id'));
        $tagName = trim($request->get('tag'));

        $tag = TagModel::where('id', '=', $tagId)->get();

        if ($tag->count() == 0) {
            $tag = TagModel::where('name', '=', $tagName)->get();
        }

        if ($tag->count() == 0) {
            return;
        } else {
            $tag = $tag->first();
        }

        if (ContestByCityTagPointModel::where('user_id', '=', $user->id)->count() > 0) {
            $points = ContestByCityTagPointModel::where('user_id', '=', $user->id)->get()->first();
            $address = TagModel::where('id', $points->address_id)->get();

            $data = [];
            $data[] = [
                'id' => ($address->count() > 0) ? $address->first()->id : '',
                'city' => ($address->count() > 0) ? $address->first()->name : '',
                'gerlNamber' => ''
            ];

            return response()->json($data);
        }


        HistoryModel::create([
            'post_id' => 0,
            'tag_id' => $tag->id,
            'user_id' => $user->id,
            'event' => 'Голосование на сайте',
            'points' => 1,
            'datetime' => time(),
        ]);

        $data = [];
        $data[] = [
            'id' => ($tag) ? $tag->id : $tagId,
            'city' => ($tag) ? $tag->name : $tagName,
            'count' => 0,
        ];

        return response()->json($data);
    }
    */
}
