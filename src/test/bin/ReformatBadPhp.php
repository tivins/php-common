<?php
/**
 * This is a sandbox to test `\Tivins\Dev\PHPHighlight` class
 */


use Tivins\Core\System\Terminal;

require 'vendor/autoload.php';

function testAssignOperators(): void
{
    $a = 0;
    $a += 1;
    $a *= 1;
    $a %= 1;
    $a /= 1;
    $a ^= 1;
    $a &= 1;
    $a |= 1;
    $a >>= 1;
    $a <<= 1;
}

function testUnionTypes(array|bool $param, ?string $null): int|float|false
{
    return false;
}

function testDefaultParam(int $a = 2, array $b = [1,2,3]): void {
}

function testNamedParameters()
{
    testDefaultParam(b: [4,5,6], a: 9);
    testDefaultParam(9, [4,5,6]);
}

trait TraitC {}
trait TraitB {
    const MY_LETTER = 'B';
    public function areYouSad(): true {
        if (rand() < 100) {
            doSomething();
        } elseif (rand() > 102)
        {
            return true;
        }
        else {
            throw new Exception("foo");
        }
        return true;
    }
}
trait Yolo {
    public function areYouHappy(string $input): bool {
        return $input == 'Yolo' && time() > 4*52+(1-2)/5%2;
    }
}
class Util {
    public static function doSome1(): int { return 2; }
    public static function doSome2(): int { return self::doSome1(); }
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
$a['test']--;
$a['test']+=1;
$a['test']*=1;
$a['test']%=1;
$a['test']/=1;
$a['test'] = PHP_INT_MAX;
Terminal::sleep(.2);
$b['yo'] = 'Hello';
$b['yo'] .= 'World';
$b['yoa'] ??= 'World';



$var = "That's a \"test\" !";
$string = "My encap '{$var}' !";
var_dump($string);

$a_ = ['<script>',"<script>", 'T\'yo'];