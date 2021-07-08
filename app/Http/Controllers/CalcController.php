<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use phpQuery;

class CalcController extends Controller
{
    public function marks()
    {
        $marks = DB::table('cars')
            ->pluck('mark')
            ->unique()
            ->values();

        return $marks;
    }

    public function models(Request $request)
    {

        $models = DB::table('cars')
            ->where('mark', $request->model)
            ->pluck('model');
        return $models;

    }

    public function calc(Request $request)
    {
        $valid = $request->validate([
            'mark' => ['required', 'string'],
            'model' => ['required', 'string'],
            'service' => ['required', 'int'],
            'fuel' => ['required', 'int'],
            'fuel_type' => ['required', 'string'],
            'tax' => ['required', 'int']
        ]);
        $mark = DB::table('cars')
            ->where('mark', $valid['mark'])
            ->take(1)
            ->pluck('url_name');
        $model = DB::table('cars')
            ->where('model', $valid['model'])
            ->take(1)
            ->pluck('url_model_name');
        $fuel_price = [
            "95" => 48.5,
            "92" => 45.8,
            "98" => 54.2,
            "Газ" => 24.9,
            "ДТ" => 49.2
        ];
        $siteName = file_get_contents("https://auto.ru/stats/cars/" . $mark[0] . '/' . $model[0]);
        $dom = phpQuery::newDocument($siteName);
        $data = json_decode($dom->find('script[id="initial-state"]')->text(), true);//price_percentage_diff
        $prices = $data["priceStatsPublicApi"]["data"]["deprecation"]["price_percentage_diff"];
        for ($i = 0; $i < count($prices); $i++) {
            $prices[$i]['price'] += ($valid['service'] + $valid['fuel'] * $fuel_price[$valid['fuel_type']] + $valid['tax']) * ($i + 1);
            unset($prices[$i]['price_percentage_diff']);
        }
        return $prices;
    }

    public function take_cars()
    {
        set_time_limit(0);
        $mark = "vaz";
        $siteName = file_get_contents("https://auto.ru/stats/cars/" . $mark);
        $dom = phpQuery::newDocument($siteName);
        $data_marks = json_decode($dom->find('script[id="initial-state"]')->text(), true);
        $marks_keys = array_keys($data_marks["breadcrumbs"]["data"]);
        for ($i = 0; $i < count($marks_keys); $i++) {//
            sleep(15);
            $mark = $marks_keys[$i];
            $siteName = file_get_contents("https://auto.ru/stats/cars/" . $mark);
            $dom = phpQuery::newDocument($siteName);
            $data = json_decode($dom->find('script[id="initial-state"]')->text(), true);
            $models_keys = array_keys($data["breadcrumbs"]["data"][$marks_keys[$i]]["models"]);
            for ($j = 0; $j < count($models_keys); $j++) {//
                Car::factory()->create([
                    'url_name' => $mark,
                    'url_model_name' => $data["breadcrumbs"]["data"][$mark]["models"][$models_keys[$j]]["id"],
                    'mark' => $data_marks["breadcrumbs"]["data"][$mark]["name"],
                    'model' => $data["breadcrumbs"]["data"][$mark]["models"][$models_keys[$j]]["name"],
                ]);
            }
        }
        return "ready";
    }
}
