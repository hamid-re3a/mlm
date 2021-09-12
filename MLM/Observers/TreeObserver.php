<?php

namespace MLM\Observers;

use MLM\Exceptions\Tree\TreeMaxChildrenExceededException;
use MLM\Exceptions\Tree\TreeMaxLeftChildExceededException;
use MLM\Exceptions\Tree\TreeMaxRightChildExceededException;
use MLM\Models\Tree;

class TreeObserver
{
    public function creating(Tree $node)
    {
        $this->checkSiblingCount($node);
    }

    public function updating(Tree $node)
    {
        $this->checkSiblingCount($node);
    }


    public function checkSiblingCount(Tree $node): void
    {
        $parent = $node->parent()->first();
        if ($parent)
            if ($parent->children()->count() > 1) {
                throw new TreeMaxChildrenExceededException();
            } else if ($parent->children()->left()->count() >= 1 && $node->isLeftChild()){
                throw new TreeMaxLeftChildExceededException();
            } else if ($parent->children()->right()->count() >= 1 && $node->isRightChild()){
                throw new TreeMaxRightChildExceededException();
            }
    }

}
