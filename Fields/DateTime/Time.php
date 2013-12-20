<?php

namespace X937\Fields\DateTime;

/**
 * Field containing a time. HHMM format, 24 hour clock.
 *
 * @author Austin Stanley <maxtmahem@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GNU Public Licneses v3
 * @copyright Copyright (c) 2013, Austin Stanley <maxtmahem@gmail.com>
 */
class Time extends \X937\Fields\DateTime
{
    const X937_DATETIME_FORMAT   = 'Hi';
    const OUTPUT_DATETIME_FORMAT = 'H:i';
    
    const FIELD_SIZE = 4;
    
    const FIELD_NAME_SUFIX = 'Time';
}