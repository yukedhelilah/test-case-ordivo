<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    function sum_deep($tree, $character) {
        $sum = 0;
        $queue = [[1, $tree]]; // [level, node]

        while (!empty($queue)) {
            [$level, $node] = array_shift($queue);

            if (is_array($node)) {
                foreach ($node as $child) {
                    array_push($queue, [$level + 1, $child]);
                }
            } elseif (strpos($node, $character) !== false) {
                $sum += $level;
            }
        }

        return $sum;
    }

    function sum_deep_challenge($tree, $characters) {
        $sum = 0;
        $queue = [[1, $tree]]; // [level, node]

        while (!empty($queue)) {
            [$level, $node] = array_shift($queue);

            if (is_array($node)) {
                foreach ($node as $child) {
                    array_push($queue, [$level + 1, $child]);
                }
            } else {
                foreach (str_split($characters) as $i => $char) {
                    if (strpos($node, $char) !== false) {
                        $sum += ($level * ($i + 1));
                    }
                }
            }
        }

        return $sum;
    }

    public function questionOne(){
        $tree1 = ["AB", ["XY"], ["YP"]];
        $character1 = 'Y';
        echo $this->sum_deep($tree1, $character1) . "<br><br>";

        $tree2 = ["", ["", ["XXXXX"]]];
        $character2 = 'X';
        echo $this->sum_deep($tree2, $character2) . "<br><br>";

        $tree3 = ["X"];
        $character3 = 'X';
        echo $this->sum_deep($tree3, $character3) . "<br><br>";

        $tree4 = [""];
        $character4 = 'X';
        echo $this->sum_deep($tree4, $character4) . "<br><br>";

        $tree5 = ["X", ["", ["", ["Y"], ["X"]]], ["X", ["", ["Y"], ["Z"]]]];
        $character5 = 'X';
        echo $this->sum_deep($tree5, $character5) . "<br><br>";

        $tree6 = ["X", [""], ["X"], ["X"], ["Y", [""]], ["X"]];
        $character6 = 'X';
        echo $this->sum_deep($tree6, $character6) . "<br><br>";

        $tree_challenge = ["ABAH", ["CIRCA"], ["CRUMP", ["IRA"]], ["ALI"]];
        $characters_challenge = "ACI";
        echo $this->sum_deep_challenge($tree_challenge, $characters_challenge) . "<br><br>";
    }

    function get_scheme($html) {
        $schemeArray = [];
        $pattern = '/<(\w+)([^>]*)>/'; // Regex pattern to match HTML tags

        preg_match_all($pattern, $html, $matches);

        foreach ($matches[2] as $attributes) {
            preg_match_all('/\b(?:sc-)(\w+)/', $attributes, $schemeMatches);
            $schemeArray = array_merge($schemeArray, $schemeMatches[1]);
        }

        return array_values(array_unique($schemeArray));
    }

    function get_scheme_challenge($html) {
        $schemeArray = [];
        $pattern = '/<(\w+)([^>]*)>(.*?)<\/\1>/s'; // Regex pattern to match HTML tags with content

        preg_match_all($pattern, $html, $matches);

        foreach ($matches[2] as $index => $attributes) {
            preg_match_all('/\b(?:sc-)(\w+)/', $attributes, $schemeMatches);
            $schemeArray[$index]['schemes'] = array_values($schemeMatches[1]);
            $schemeArray[$index]['children'] = $this->get_scheme_challenge($matches[3][$index] ?? '');
        }

        return $schemeArray;
    }

    public function questionTwo(){
        $html1 = "<i sc-root>Hello</i>";
        echo json_encode($this->get_scheme($html1)) . '<br><br>'; // Output: ["root"]

        $html2 = "<div><div sc-rootbear title='Oh My'>Hello <i sc-org>World</i></div></div>";
        echo json_encode($this->get_scheme($html2)) . '<br><br>'; // Output: ["rootbear", "org"]

        $html1 = "<i sc-root=\"Hello\">World</i>";
        echo json_encode($this->get_scheme_challenge($html1), JSON_PRETTY_PRINT) . "<br><br>";
        // Output: [{"schemes":["root"],"children":"Hello"}]

        $html2 = "<div sc-prop sc-alias=\"\" sc-type=\"Organization\"><div sc-name=\"Alice\">Hello <i sc-name=\"Wonderland\">World</i></div></div>";
        echo json_encode($this->get_scheme_challenge($html2), JSON_PRETTY_PRINT) . "<br><br>";
    }

    function pattern_count($text, $pattern) {
        $textLength = strlen($text);
        $patternLength = strlen($pattern);
        $count = 0;

        for ($i = 0; $i <= $textLength - $patternLength; $i++) {
            $matched = true;

            for ($j = 0; $j < $patternLength; $j++) {
                if ($text[$i + $j] !== $pattern[$j]) {
                    $matched = false;
                    break;
                }
            }

            if ($matched) {
                $count++;
            }
        }

        return $count;
    }

    public function questionThree(){
        echo $this->pattern_count("palindrom", "ind") . "<br><br>";
        echo $this->pattern_count("abakadabra", "ab") . "<br><br>";
        echo $this->pattern_count("hello", "") . "<br><br>";
        echo $this->pattern_count("ababab", "aba") . "<br><br>";
        echo $this->pattern_count("aaaaaa", "aa") . "<br><br>";
        echo $this->pattern_count("hell", "hello") . "<br><br>";
    }

    public function questionFour(){
        $motorBoat = new MotorBoat("Speedy", 10, "Outboard");
        echo $motorBoat->getInfo() . "<br><br>";

        $sailboat = new Sailboat("Sailor", 12, 3);
        echo $sailboat->getInfo() . "<br><br>";

        $yacht = new Yacht("Dreamliner", 20, "High");
        echo $yacht->getInfo() . "<br><br>";
    }
}

abstract class Ship {
    protected $name;
    protected $length;

    public function __construct($name, $length) {
        $this->name = $name;
        $this->length = $length;
    }

    public function getName() {
        return $this->name;
    }

    public function getLength() {
        return $this->length;
    }

    abstract public function getInfo();
}

// MotorBoat class (subclass of Ship)
class MotorBoat extends Ship {
    private $engineType;

    public function __construct($name, $length, $engineType) {
        parent::__construct($name, $length);
        $this->engineType = $engineType;
    }

    public function getEngineType() {
        return $this->engineType;
    }

    public function getInfo() {
        return "Motor Boat: {$this->getName()}, Length: {$this->getLength()} meters, Engine Type: {$this->engineType}";
    }
}

// Sailboat class (subclass of Ship)
class Sailboat extends Ship {
    private $sailCount;

    public function __construct($name, $length, $sailCount) {
        parent::__construct($name, $length);
        $this->sailCount = $sailCount;
    }

    public function getSailCount() {
        return $this->sailCount;
    }

    public function getInfo() {
        return "Sailboat: {$this->getName()}, Length: {$this->getLength()} meters, Sail Count: {$this->sailCount}";
    }
}

// Yacht class (subclass of Ship)
class Yacht extends Ship {
    private $luxuryLevel;

    public function __construct($name, $length, $luxuryLevel) {
        parent::__construct($name, $length);
        $this->luxuryLevel = $luxuryLevel;
    }

    public function getLuxuryLevel() {
        return $this->luxuryLevel;
    }

    public function getInfo() {
        return "Yacht: {$this->getName()}, Length: {$this->getLength()} meters, Luxury Level: {$this->luxuryLevel}";
    }
}
