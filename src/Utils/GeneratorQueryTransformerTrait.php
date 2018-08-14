<?php
namespace App\Utils;

use Doctrine\ORM\Query;

trait GeneratorQueryTransformerTrait
{
    /**
     * @param Query $query
     * @param null $parameters
     * @param int $hydrationMode
     * @return \Generator
     */
    protected function toGenerator(Query $query, $parameters = null, $hydrationMode = Query::HYDRATE_OBJECT)
    {
        foreach ($query->iterate($parameters, $hydrationMode) as $row) {
            yield $row[0];
        }
    }
}
