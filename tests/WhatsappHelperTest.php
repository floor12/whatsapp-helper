<?php


use floor12\whatsapp\WhatsappHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class WhatsappHelperTest extends TestCase
{


    public function testGetUnreadCount()
    {
        $successResponse = [
            'status' => 'success',
            'unread_chats_count' => 10,
        ];
        $helper = new WhatsappHelper('some_token', 'some_url',
            self::makeClient(200, $successResponse));
        self::assertEquals(10, $helper->getUnreadCount());
    }

    public function testGetUnreadCountError()
    {
        $successResponse = [
            'status' => 'error',
        ];
        $helper = new WhatsappHelper('some_token', 'some_url',
            self::makeClient(200, $successResponse));
        self::assertEquals(0, $helper->getUnreadCount());
    }

    public function testCheckNumber()
    {
        $successResponse = [
            'status' => 'success',
            'phone_has_whatsapp' => true,
        ];
        $helper = new WhatsappHelper('some_token', 'some_url',
            self::makeClient(200, $successResponse));
        self::assertTrue($helper->checkNumber());
    }

    public function testCheckNumberError()
    {
        $successResponse = [
            'status' => 'error',
        ];
        $helper = new WhatsappHelper('some_token', 'some_url',
            self::makeClient(200, $successResponse));
        self::assertFalse($helper->checkNumber());
    }

    public function testHttpError()
    {
        self::expectException(floor12\whatsapp\WhatsappHelperException::class);
        $successResponse = ['status' => 'error'];
        $helper = new WhatsappHelper('some_token', 'some_url',
            self::makeClient(403, $successResponse));
        $helper->getUnreadCount();
    }

    private static function makeClient(int $code, array $response)
    {

        $response = json_encode($response);
        $mock = new MockHandler([new Response($code, [], $response),]);
        $handlerStack = HandlerStack::create($mock);
        return new Client(['handler' => $handlerStack]);
    }
}
