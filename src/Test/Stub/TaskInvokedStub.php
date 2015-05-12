<?php

namespace AndreasWeber\Runner\Test\Stub;

class TaskInvokedStub
{
    private $invoked = false;

    public function trigger()
    {
        $this->invoked = true;
    }

    public function wasInvoked()
    {
        return $this->invoked;
    }
}
