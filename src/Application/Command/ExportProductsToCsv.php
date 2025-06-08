<?php

declare(strict_types=1);

namespace App\Application\Command;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
class ExportProductsToCsv
{
}
