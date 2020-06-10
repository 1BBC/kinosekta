<?php

namespace app\common;

use yii\base\BaseObject;

class Csv extends BaseObject
{
    private $_csv_file = null;

    /**
     * @param string $csv_file - путь до csv-файла
     * @param array $config
     */
    public function __construct($csv_file, $config = []) {
        $this->_csv_file = $csv_file; //Записываем путь к файлу в переменную

        parent::__construct($config);
    }

    public function setCSV(Array $csv, $mode = "a") {
        //Открываем csv для до-записи,
        //если указать w, то  ифнормация которая была в csv будет затерта
        $handle = fopen($this->_csv_file, $mode);

        foreach ($csv as $value) { //Проходим массив
            //Записываем, 3-ий параметр - разделитель поля
            fputcsv($handle, $value);
        }
        fclose($handle); //Закрываем
    }

    /**
     * Метод для чтения из csv-файла. Возвращает массив с данными из csv
     * @return array;
     */
    public function getCSV() {
        $handle = fopen($this->_csv_file, "r"); //Открываем csv для чтения

        $array_line_full = array(); //Массив будет хранить данные из csv
        //Проходим весь csv-файл, и читаем построчно. 3-ий параметр разделитель поля
        while (($line = fgetcsv($handle, 0, ";")) !== FALSE) {
            $array_line_full[] = $line; //Записываем строчки в массив
        }
        fclose($handle); //Закрываем файл
        return $array_line_full; //Возвращаем прочтенные данные
    }
}