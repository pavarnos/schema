<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    March 2015
 */

namespace LSS\Schema\Renderer;

use LSS\SchemaInterface;

/**
 * Take the Schema and return a digraph to render in graphviz
 */
class GraphViz
{
    /**
     *
     * @param SchemaInterface $schema
     * @return string graphviz syntax
     */
    public function render(SchemaInterface $schema)
    {
        return '';
    }
}
