<?php declare(strict_types=1);
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Logging factory
 *
 * This class supplies an implementation for the locator.
 * The locator will send its output to ist own frame, enabling more flexibility in
 * the design of the desktop.
 *
 * @author Stefan Meyer <smeyer.ilias@gmx.de>
 * @version $Id$
 *
 */
class ilTraceProcessor
{
    private int $level = 0;
    
    public function __construct(int $a_level)
    {
        $this->level = $a_level;
    }

    /**
     * @todo fix shifting calls
     */
    public function __invoke(array $record) : array
    {
        if ($record['level'] < $this->level) {
            return $record;
        }
        
        $trace = debug_backtrace();
        
        // shift current method
        array_shift($trace);
        
        // shift internal monolog calls
        array_shift($trace);
        array_shift($trace);
        array_shift($trace);
        array_shift($trace);

        if (is_array($trace) && count($trace)) {
            $trace_info =
                ($trace[0]['class'] ?? '') . '::' .
                ($trace[0]['function'] ?? '') . ':' .
                ($trace[0]['line'] ?? '');
            $record['extra'] = array_merge(
                $record['extra'],
                array('trace' => $trace_info)
            );
        }
        return $record;
    }
}
