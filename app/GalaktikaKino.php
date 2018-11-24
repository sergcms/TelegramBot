<?php

namespace App;

class GalaktikaKino
{
    private $content;

    public function __CONSTRUCT()
    {
        $content = file_get_contents('http://www.galaktika-kino.com.ua/main/price.php');
        $this->content = iconv('windows-1251', 'UTF-8', $content);
    }

    private function parseDate(): string
    {
        $regular = '/(Розклад сеансів*.*([0-9.по ]{24}))<\/b>/m';
        preg_match_all($regular, $this->content, $matches, PREG_SET_ORDER, 0);
        return $matches[0][2];
    }

    private function parseShedules(): array
    {
        $schedules= [];
        $regular_zal = '/(Зал № \d{1}).+?<\/table>/ms';
        $regular_tr = '/<tr>.+?<\/tr>/ms';
        $regular_td = '/<td.+"#.+>(.+)<\/td>/m';

        preg_match_all($regular_zal, $this->content, $contents_zal, PREG_SET_ORDER, 0);

        foreach ($contents_zal as $content_zal) {
            $zal = $content_zal[1];
            preg_match_all($regular_tr, $content_zal[0], $contents_tr, PREG_SET_ORDER, 0);


            foreach ($contents_tr as $content_tr) {
                preg_match_all($regular_td, $content_tr[0], $contents_td, PREG_SET_ORDER, 0);
                if (!empty($contents_td)) {
                    $name = str_replace('&nbsp;', '', $contents_td[0][1]);
                    $time = str_replace('&nbsp;', '', $contents_td[1][1]);
                    $price = str_replace('&nbsp;', '', $contents_td[2][1]);
                    if (!empty($name)) {
                        $schedules[$zal][] = ['name' => $name, 'time' => $time, 'price' => $price];
                    }
                }
            }
        }
        return $schedules;
    }

    public function getShedules(): string
    {
        $dateShedule = $this->parseDate();
        $result = 'Расписание сеансов с ' . $dateShedule . PHP_EOL;
        $shedules = $this->parseShedules();

        foreach ($shedules as $zal => $shedule) {
            $result .= '----------------------------' . PHP_EOL;
            $result .= $zal . PHP_EOL;

            foreach ($shedule as $param) {
                $result .= $param['name'] . ' | Начало: ' . $param['time'] . ' | Цена: ' . $param['price'] . PHP_EOL;
            }
        }

        return $result;
    }
}
