<?php

declare(strict_types=1);

namespace Pandawa\Component\Validation;

use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\Validator as LaravelValidator;
use Pandawa\Contracts\Validation\Parser\ParserResolverInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class Validator extends LaravelValidator
{
    public function validateUnique($attribute, $value, $parameters): bool
    {
        $this->requireParameterCount(1, $parameters, 'unique');

        [$connection, $table, $idColumn] = $this->parseTable($parameters[0]);

        // The second parameter position holds the name of the column that needs to
        // be verified as unique. If this parameter isn't specified we will just
        // assume that this column to be verified shares the attribute's name.
        $column = $this->getQueryColumn($parameters, $attribute);

        $id = null;

        if (isset($parameters[2])) {
            [$idColumn, $id] = $this->getUniqueIds($idColumn, $parameters);

            if (! is_null($id)) {
                $id = stripslashes($id);
            }
        }

        // The presence verifier is responsible for counting rows within this store
        // mechanism which might be a relational database or any other permanent
        // data store like Redis, etc. We will use it to determine uniqueness.
        $verifier = $this->getPresenceVerifier($connection);

        $extra = $this->getUniqueExtra($parameters);

        if ($this->currentRule instanceof Unique) {
            $extra = array_merge($extra, $this->currentRule->queryCallbacks());
        }

        if (null !== $id) {
            $id = $this->parserResolver()->resolve($id)?->parse($id) ?? $id;
        }

        return 0 === $verifier->getCount($table, $column, $value, $id, $idColumn, $extra);
    }

    protected function parserResolver(): ParserResolverInterface
    {
        return $this->container->get(ParserResolverInterface::class);
    }
}
