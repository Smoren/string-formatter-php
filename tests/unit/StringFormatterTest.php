<?php

namespace Smoren\StringFormatter\Tests\Unit;

use Smoren\StringFormatter\StringFormatter;
use Smoren\StringFormatter\StringFormatterException;

class StringFormatterTest extends \Codeception\Test\Unit
{
    /**
     * @return void
     * @throws StringFormatterException
     */
    public function testNormal()
    {
        $input = 'Hello, {name}! Your position is {position}.';
        $params = ['name' => 'Anna', 'position' => 'programmer'];
        $result = StringFormatter::format($input, $params);
        $this->assertEquals('Hello, Anna! Your position is programmer.', $result);

        $input = 'Hello, {name}! Your work_position is {work_position}.';
        $params = ['name' => 'Anna', 'work_position' => 'programmer', 'extra' => 123];
        $result = StringFormatter::format($input, $params);
        $this->assertEquals('Hello, Anna! Your work_position is programmer.', $result);
    }

    public function testUtf()
    {
        $input = 'Привет, {name}! Твоя должность {work_position}.';
        $params = ['name' => 'Анна', 'work_position' => 'программист'];
        $result = StringFormatter::format($input, $params);
        $this->assertEquals('Привет, Анна! Твоя должность программист.', $result);

        $input = 'Привет, %имя%! Твоя должность %должность%.';
        $params = ['имя' => 'Анна', 'должность' => 'программист'];
        $result = StringFormatter::format($input, $params, false, '/%([а-яё]+)%/u');
        $this->assertEquals('Привет, Анна! Твоя должность программист.', $result);
    }

    /**
     * @return void
     */
    public function testErrors()
    {
        $input = 'Hello, {name}! Your work_position is {work_position}.';
        $params = ['name' => 'Anna'];
        try {
            StringFormatter::format($input, $params);
            $this->expectError();
        } catch(StringFormatterException $e) {
            $this->assertEquals(StringFormatterException::ERROR_KEYS_NOT_FOUND, $e->getCode());
            $this->assertEquals(['work_position'], $e->getData());
        }

        $input = 'Hello, {name}! Your work_position is {work_position}.';
        $params = ['bad' => 'kay'];
        try {
            StringFormatter::format($input, $params);
            $this->expectError();
        } catch(StringFormatterException $e) {
            $this->assertEquals(StringFormatterException::ERROR_KEYS_NOT_FOUND, $e->getCode());
            $this->assertEquals(['name', 'work_position'], $e->getData());
        }
    }

    /**
     * @return void
     * @throws StringFormatterException
     */
    public function testSilent()
    {
        $input = 'Hello, {name}! Your work_position is {work_position}.';
        $params = ['name' => 'Anna', 'some' => 'bad key'];

        $result = StringFormatter::format($input, $params, true);
        $this->assertEquals(
            'Hello, Anna! Your work_position is {work_position}.',
            $result
        );

        $result = StringFormatter::formatSilent($input, $params);
        $this->assertEquals(
            'Hello, Anna! Your work_position is {work_position}.',
            $result
        );
    }
}