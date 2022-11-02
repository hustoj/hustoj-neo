<?php

namespace App;

class Status
{
    public const PENDING = 0;
    public const PENDING_REJUDGE = 1;
    public const COMPILING = 2;
    public const RUNNING = 3;
    public const ACCEPT = 4;
    public const PRESENTATION_ERROR = 5;
    public const WRONG_ANSWER = 6;
    public const TIME_LIMIT = 7;
    public const MEMORY_LIMIT = 8;
    public const OUTPUT_LIMIT = 9;
    public const RUNTIME_ERROR = 10;
    public const COMPILE_ERROR = 11;
    public const COMPILE_OK = 12;
    public const TEST_RUN = 13;
}
