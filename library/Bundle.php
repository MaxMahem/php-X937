<?php

namespace X937;

/**
 * Description of Bundle
 *
 * @author astanley
 */
class Bundle extends Container
{
    private $records;

    public function __construct(array $records)
    {
        $this->records = $records;
    }

    public function validate(): string
    {
        $error = '';

        foreach ($this->records as $record) {
            $error .= $record->validate();
        }

        return $error;
    }
}
