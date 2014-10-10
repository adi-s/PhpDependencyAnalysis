<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 Marco Muths
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace PhpDA\Parser\Visitor;

use PhpParser\Node;

class UnsupportedVarCollector extends AbstractVisitor
{
    public function leaveNode(Node $node)
    {
        if ($this->unsupports($node)) {
            $this->collect($node);
        }
    }

    /**
     * @param Node $node
     * @return bool
     */
    private function unsupports(Node $node)
    {
        if ($node instanceof Node\Expr\New_) {
            return $this->unsupportsInstantiationFor($node);
        } else {
            return $this->unsupportsDynamicCallFor($node);
        }
    }

    /**
     * @param Node\Expr\New_ $node
     * @return bool
     */
    private function unsupportsInstantiationFor(Node\Expr\New_ $node)
    {
        return $node->class instanceof Node\Expr\Variable;
    }

    /**
     * @param Node $node
     * @return bool
     */
    private function unsupportsDynamicCallFor(Node $node)
    {
        $nodeIsInspectable = (
            $node instanceof Node\Expr\Variable
            || $node instanceof Node\Expr\FuncCall
            || $node instanceof Node\Expr\StaticCall
        );

        /** @var Node\Expr\Variable $node */
        return ($nodeIsInspectable && $node->name instanceof Node\Expr\Variable);
    }
}