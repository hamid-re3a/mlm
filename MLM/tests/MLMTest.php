<?php


namespace MLM\tests;


use MLM\Models\Tree;
use Illuminate\Support\Facades\Artisan;
use Tests\CreatesApplication;
use Tests\TestCase;

class MLMTest extends TestCase
{
    use CreatesApplication;
//    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();
        Artisan::call('migrate:fresh');
//        MLMConfigure::seed();
        $this->app->setLocale('en');
    }

    public function hasMethod($class, $method)
    {
        $this->assertTrue(
            method_exists($class, $method),
            "$class must have method $method"
        );
    }

    public function buildBinaryTree($data = null)
    {
        if (is_null($data))
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
            ];
        foreach ($data as $item) {
            Tree::factory()->create($item);
            Tree::fixTree();
        }
    }
}
