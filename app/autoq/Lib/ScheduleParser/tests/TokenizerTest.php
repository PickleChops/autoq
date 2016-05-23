<?php

namespace Tests;

use Autoq\Lib\ScheduleParser\Tokenizer;

require_once __DIR__ . '/../Tokenizer.php';

/**
 * Class TokenizerTest Basic tests around simple Tokenizer class
 * @package Tests
 */
class TokenizerTest extends \PHPUnit_Framework_TestCase
{

    public function testTokenizer()
    {

        $tokenizer = new Tokenizer("The quick brown fox jumped over the lazy dog");

        // Number of tokens
        $this->assertEquals(9, $tokenizer->getCount());

        //First token
        $this->assertEquals("The", $tokenizer->next());

        //Current token
        $this->assertEquals("quick", $tokenizer->current());

        //Current token
        $this->assertEquals("brown", $tokenizer->peek());


        //No prev
        $tokenizer->rewind();
        $tokenizer->prev();
        $this->assertEquals(false, $tokenizer->prev());

        //Test as iterator
        $pos = 0;
        foreach ($tokenizer as $token) {
            if ($pos == 5) {
                $this->assertEquals("over", $token);
            }
            $pos++;
            echo $pos;
        }

        //All tokens consumed after foreach
        $this->assertEquals(false, $tokenizer->current());

        //Prev to get last token
        $this->assertEquals("dog", $tokenizer->prev());


        //Peek when no more token
        $this->assertEquals(false, $tokenizer->peek());



    }
}
