<?php

namespace MLM\tests\UnitTest\Tree;


use MLM\Exceptions\Tree\TreeMaxChildrenExceededException;
use MLM\Exceptions\Tree\TreeMaxLeftChildExceededException;
use MLM\Models\Tree;
use MLM\tests\MLMTest;

class BinaryTreeTest extends MLMTest
{
    /**
     * @test
     */
    public function tree_can_add_root()
    {
        $node = Tree::factory()->create();
        $node->saveAsRoot();
        $this->assertTrue($node->isRoot());
    }

    /**
     * @test
     */
    public function adding_node_to_left_and_right_of_root()
    {
        $root = Tree::factory()->create();
        $root->saveAsRoot();

        $left = Tree::factory([
            'position' => 'left'
        ])->create();

        $right = Tree::factory([
            'position' => 'right'
        ])->create();

        $root->appendNode($left);
        $root->appendNode($right);

        $this->assertEquals(2, $root->children()->count());
        $this->assertEquals($left->fresh(), $root->children()->leftChild());
        $this->assertEquals($right->fresh(), $root->children()->rightChild());

    }

    /**
     * @test
     */
    public function cannot_add_more_than_two_children_to_a_node()
    {
        $root = Tree::factory()->create();
        $root->saveAsRoot();

        $left = Tree::factory([
            'position' => 'left'
        ])->create();

        $right = Tree::factory([
            'position' => 'right'
        ])->create();

        $third_half_brother = Tree::factory()->create();
        $root->appendNode($left);
        $root->appendNode($right);
        $this->expectException(TreeMaxChildrenExceededException::class);
        $root->appendNode($third_half_brother);

    }

    /**
     * @test
     */
    public function cannot_add_two_left_children_to_a_node()
    {
        $root = Tree::factory()->create();
        $root->saveAsRoot();

        $left = Tree::factory()->create();

        $second_left_node = Tree::factory()->create();

        $root->appendAsLeftNode($left);
        $this->expectException(TreeMaxLeftChildExceededException::class);
        $root->appendAsLeftNode($second_left_node);

    }

    /**
     * @test
     */
    public function tree_has_method_to_add_node_to_left_or_right_side()
    {
        $root = Tree::factory()->create();
        $root->saveAsRoot();

        $nodes = Tree::factory()->count(2)->create();
        $left = $nodes->pop();

        $this->hasMethod(Tree::class, 'appendAsLeftNode');
        $root->appendAsLeftNode($left);
        $right = $nodes->pop();
        $this->hasMethod(Tree::class, 'appendAsRightNode');
        $root->appendAsRightNode($right);

        $this->assertEquals($left->fresh(), $root->children()->leftChild());

    }

    /**
     * @test
     */
    public function tree_can_count_left_and_right_side_children_count()
    {

        $this->buildBinaryTree();
        $root = Tree::getRoot();
        $this->assertEquals(10, $root->descendants()->count());
        $this->assertEquals(7, $root->leftChildCount());
        $this->assertEquals(3, $root->rightChildCount());

    }

    /**
     * @test
     */
    public function find_sub_set_of_nodes()
    {
        $data = [
            ['id' => 1, 'position' => null, 'parent_id' => null],
            ['id' => 2, 'position' => 'left', 'parent_id' => 1],
            ['id' => 3, 'position' => 'right', 'parent_id' => 1],
            ['id' => 4, 'position' => 'right', 'parent_id' => 2],
            ['id' => 5, 'position' => 'left', 'parent_id' => 2],
            ['id' => 6, 'position' => 'right', 'parent_id' => 3],
            ['id' => 7, 'position' => 'left', 'parent_id' => 3],
            ['id' => 8, 'position' => 'right', 'parent_id' => 4],
            ['id' => 9, 'position' => 'left', 'parent_id' => 4],
            ['id' => 10, 'position' => 'right', 'parent_id' => 5],
            ['id' => 11, 'position' => 'left', 'parent_id' => 5],
            ['id' => 12, 'position' => 'right', 'parent_id' => 6],
            ['id' => 13, 'position' => 'left', 'parent_id' => 6],
            ['id' => 14, 'position' => 'right', 'parent_id' => 7],
            ['id' => 15, 'position' => 'left', 'parent_id' => 7],
            ['id' => 16, 'position' => 'right', 'parent_id' => 8],
            ['id' => 17, 'position' => 'left', 'parent_id' => 8],
        ];
        //  [ left child descendants
        //    0 => 4
        //    1 => 5
        //    2 => 8
        //    3 => 9
        //    4 => 10
        //    5 => 11
        //    6 => 16
        //    7 => 17
        //  ]
        $this->buildBinaryTree($data);
        $root = Tree::getRoot();

        $from_node = 4;
        $chunk_size = 3;
        $set_number = 1;

        $subset_ids = $root->children()->leftChild()->subset($set_number, $chunk_size, $from_node);

        $this->assertEquals( [10, 11,16],$subset_ids);

        $chunk_size = 3;
        $set_number = 2;

        $subset_ids = $root->children()->leftChild()->subset($set_number, $chunk_size);

        $this->assertEquals([9, 10, 11],$subset_ids);

        $chunk_size = 4;
        $set_number = 2;

        $subset_ids = $root->children()->leftChild()->subset($set_number, $chunk_size);

        $this->assertEquals([10, 11, 16, 17],$subset_ids);

    }

    /**
     * @test
     */
    public function it_belongs_to_user()
    {
        $this->hasMethod(Tree::class, 'user');
        $this->assertNotNull(Tree::factory()->create()->user);
    }


}
