<?php

namespace App\Model\Response;

use App\Model\Created\CreatedResponse;

class UpdatedResponse extends CreatedResponse
{
    protected string $message = 'Successfully updated.';
}
