<?php

namespace Tests\Unit;

use App\Models\MessageReceiverContact;
use App\Observers\MessageReceiverContactObserver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;

class MessageReceiverContactObserverTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function created_event_should_prepare_and_send_messages()
    {
        // Create a mocked MessageReceiverContact object.
        $messageReceiverContact = $this->mock(MessageReceiverContact::class);

        // Set up the expected results.
        $expectedMessageBodies = [
            [
                'message_body' => 'This is the message body.',
                'whatsapps' => ['+15555555555', '+15555555556'],
            ],
        ];

        // Expect the following methods to be called on the mocked object.
        $messageReceiverContact->expects($this->once())->method('prepareMessageBodies')->willReturn($expectedMessageBodies);
        $messageReceiverContact->expects($this->once())->method('sendMessageBodies')->with($expectedMessageBodies);

        // Create an observer instance and dispatch the created event.
        $observer = new MessageReceiverContactObserver();
        $observer->created($messageReceiverContact);
    }
}