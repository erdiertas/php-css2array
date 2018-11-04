<?php
/**
 * Created by PhpStorm.
 * User: erdiertas
 * Date: 5.11.2018
 * Time: 00:29
 */

class Css2Array
{
    /**
     * @param $filename //URL or file
     * @return array
     */
    public function fileStyle($filename)
    {
        $css = file_get_contents($filename);
        return $this->getArray($css);
    }

    /**
     * @param $css
     * @return array
     */
    public function style($css)
    {
        return $this->getArray($css);
    }

    /**
     * @param $css
     * @return array
     */
    private function getArray($css)
    {
        $regex = array(
            "`^([\t\s]+)`ism" => '',
            "`^\/\*(.+?)\*\/`ism" => "",
            "`([\n\A;]+)\/\*(.+?)\*\/`ism" => "$1",
            "`([\n\A;\s]+)//(.+?)[\n\r]`ism" => "$1\n",
            "`(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+`ism" => "\n",
            '#/\*(?:.(?!/)|[^\*](?=/)|(?<!\*)/)*\*/#s' => "$1"
        );
        $css = preg_replace(array_keys($regex), $regex, $css);

        $css = str_replace("}", "{", $css);
        $css = str_replace("}", "{", $css);
        $css = explode("{", $css);

        $array = [];
        $lastMedia = "";
        $lastElement = "";
        foreach ($css AS $value) {
            $value = str_replace(["\n", "  "], " ", $value);
            $value = trim($value);
            if ((strpos($value, "@media") !== false) && $lastMedia === "") {
                $lastMedia = count($array);
                $array[$lastMedia] = [
                    "media" => $value,
                    "elements" => []
                ];
            } elseif ($lastElement === "" && $value !== "") {
                if ($lastMedia === "") {
                    $lastMedia = count($array);
                    $array[$lastMedia] = [
                        "media" => "__GLOBAL__",
                        "elements" => []
                    ];
                }
                $lastElement = count($array[$lastMedia]["elements"]);
                $elements = explode(",", $value);
                $elementsArray = [];
                foreach ($elements AS $value_element) {
                    $value_element_split = preg_split('/( |>)/', $value_element);
                    $elementsArray[] = [
                        "selector" => $value_element,
                        "level" => count($value_element_split),
                        "extent" => count(preg_split('/(\.|#)/', $value_element))
                    ];
                }
                $array[$lastMedia]["elements"][$lastElement] = [
                    "selectors" => $elementsArray,
                    "css" => ["erd"]
                ];
            } elseif ($value !== "") {
                $value = rtrim($value, ";");
                $css = explode(";", $value);
                $cssArray = [];
                foreach ($css AS $value_css) {
                    $value_css = explode(":", $value_css);
                    $cssKey = trim($value_css[0]);
                    unset($value_css[0]);
                    $cssArray[$cssKey] = implode(":", $value_css);

                }
                $array[$lastMedia]["elements"][$lastElement]["css"] = $cssArray;
                $lastElement = "";
                if ($array[$lastMedia]["media"] == "__GLOBAL__") {
                    $lastMedia = "";
                }
            } else {
                $lastElement = "";
                $lastMedia = "";
            }
        }
        return $array;
    }
}