<?php

namespace Chivincent\LaravelKratos\Notifications;

use BadMethodCallException;

trait MustVerifyEmail
{
    public function hasVerifiedEmail()
    {
        // TODO: Implement hasVerifiedEmail() method.
    }

    public function markEmailAsVerified()
    {
        throw new BadMethodCallException('Unexpected method ['.__FUNCTION__.'] call');
    }

    public function sendEmailVerificationNotification()
    {
        throw new BadMethodCallException('Unexpected method ['.__FUNCTION__.'] call');
    }

    public function getEmailForVerification()
    {
        throw new BadMethodCallException('Unexpected method ['.__FUNCTION__.'] call');
    }
}