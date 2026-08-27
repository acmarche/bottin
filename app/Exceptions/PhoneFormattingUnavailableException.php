<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

/**
 * Thrown when the AI phone formatting service cannot be reached or answers with an error.
 */
final class PhoneFormattingUnavailableException extends Exception {}
