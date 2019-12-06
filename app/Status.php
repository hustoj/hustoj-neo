<?php

namespace App;

class Status
{
    const PENDING = 0;
    const PENDING_REJUDGE = 1;
    const COMPILING = 2;
    const RUNNING = 3;
    const ACCEPT = 4;
    const PRESENTATION_ERROR = 5;
    const WRONG_ANSWER = 6;
    const TIME_LIMIT = 7;
    const MEMORY_LIMIT = 8;
    const OUTPUT_LIMIT = 9;
    const RUNTIME_ERROR = 10;
    const COMPILE_ERROR = 11;
    const COMPILE_OK = 12;
    const TEST_RUN = 13;
}
