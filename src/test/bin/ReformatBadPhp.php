<?php
/**
 * This is a sandbox to test `\Tivins\Dev\PHPHighlight` class
 */


use Tivins\Core\System\Terminal;

require 'vendor/autoload.php';

trait TraitC {}
trait TraitB {
    const MY_LETTER = 'B';
    public function areYouSad(): true {
        return true;
    }
}
trait Yolo {
    public function areYouHappy(string $input): bool {
        return $input == 'Yolo' && time() > 4*52+(1-2)/5%2;
    }
}
class Util {
    public static function doSome1(): void {}
}
class Emoclass {
    use TraitC;
    use Yolo, TraitB;
    public const YO = "LO";
    protected const LO = 'YO';

    /**
     * Allow to use this or that.
     */
    private const YL = 'OO';

    /**
     * @param string $input a string
     * @return string another string
     */
    public function goGo(string $input): string {
        return 'gadget';
    }
}

$ec = new Emoclass();

$a = array(
    'test'
    => 2
);

var_dump($a);
Terminal::sleep(1);
$a['test']++;
var_dump($a);
Terminal::sleep(.2);


$var = "That's a \"test\" !";
$string = "My encap '{$var}' !";
var_dump($string);

$a_ = ['<script>',"<script>", 'T\'yo'];