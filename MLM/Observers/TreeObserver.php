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
        $parent = $node->parent;
        if ($parent)
            if ($parent->children()->where('id','!=',$node->id)->count() > 1) {
                throw new TreeMaxChildrenExceededException();
            } else if ($parent->children()->left()->where('id','!=',$node->id)->count() >= 1 && $node->isLeftChild()){
                throw new TreeMaxLeftChildExceededException();
            } else if ($parent->children()->right()->where('id','!=',$node->id)->count() >= 1 && $node->isRightChild()){
                throw new TreeMaxRightChildExceededException();
            }
    }

}
