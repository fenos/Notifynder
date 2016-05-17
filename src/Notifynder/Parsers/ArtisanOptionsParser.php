<?php

namespace Fenos\Notifynder\Parsers;

/**
 * Class ArtisanOptionsParser.
 */
class ArtisanOptionsParser
{
    /**
     * Parse a string of fields, like
     * name, name2, name3.
     *
     * @param  string $fields
     * @return array
     */
    public function parse($fields)
    {
        if (! $fields) {
            return [];
        }

        // name:string, age:integer
        // name:string(10,2), age:integer
        $fields = preg_split('/\s?,\s/', $fields);

        $parsed = [];

        foreach ($fields as $index => $field) {
            // Example:
            // name:string:nullable => ['name', 'string', 'nullable']
            // name:string(15):nullable
            $chunks = preg_split('/\s?:\s?/', $field, null);

            // The first item will be our property
            $property = array_shift($chunks);

            $args = null;

            // Finally, anything that remains will
            // be our decorators
            $decorators = $chunks;

            $parsed[$index] = $property;

            if (isset($args)) {
                $parsed[$index]['args'] = $args;
            }
            if ($decorators) {
                $parsed[$index]['decorators'] = $decorators;
            }
        }

        return $parsed;
    }
}
