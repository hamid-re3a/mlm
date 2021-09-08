<?php

namespace MLM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;
use phpDocumentor\Reflection\Types\Integer;
use User\Models\User;

/**
 * App\Models\Tree
 *
 * @property-read \Kalnoy\Nestedset\Collection|Tree[] ع$children
 * @property-read int|null $children_count
 * @property-read Tree $parent
 * @property-write mixed $parent_id
 * @method static \Kalnoy\Nestedset\Collection|static[] all($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|Tree d()
 * @method static \Kalnoy\Nestedset\Collection|static[] get($columns = ['*'])
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree newModelQuery()
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree newQuery()
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree query()
 * @mixin \Eloquent
 * @property int $id
 * @property int $_lft
 * @property int $_rgt
 * @property int $user_id
 * @property string|null $position
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \User\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Tree getRoot()
 * @method static \Illuminate\Database\Eloquent\Builder|Tree left()
 * @method static \Illuminate\Database\Eloquent\Builder|Tree leftChild()
 * @method static \Illuminate\Database\Eloquent\Builder|Tree right()
 * @method static \Illuminate\Database\Eloquent\Builder|Tree rightChild()
 * @method static \Illuminate\Database\Eloquent\Builder|Tree whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tree whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tree whereLft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tree whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tree wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tree whereRgt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tree whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tree whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tree hasChild()
 * @property-read User $User
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree ancestorsAndSelf($id, array $columns = [])
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree ancestorsOf($id, array $columns = [])
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree applyNestedSetScope(?string $table = null)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree countErrors()
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree defaultOrder(string $dir = 'asc')
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree descendantsAndSelf($id, array $columns = [])
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree descendantsOf($id, array $columns = [], $andSelf = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree fixSubtree($root)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree fixTree($root = null)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree getNodeData($id, $required = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree getPlainNodeData($id, $required = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree getTotalErrors()
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree hasChildren()
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree hasParent()
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree isBroken()
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree leaves(array $columns = [])
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree makeGap(int $cut, int $height)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree moveNode($key, $position)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree orWhereAncestorOf(bool $id, bool $andSelf = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree orWhereDescendantOf($id)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree orWhereNodeBetween($values)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree orWhereNotDescendantOf($id)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree rebuildSubtree($root, array $data, $delete = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree rebuildTree(array $data, $delete = false, $root = null)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree reversed()
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree root(array $columns = [])
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree whereAncestorOf($id, $andSelf = false, $boolean = 'and')
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree whereAncestorOrSelf($id)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree whereDescendantOf($id, $boolean = 'and', $not = false, $andSelf = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree whereDescendantOrSelf(string $id, string $boolean = 'and', string $not = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree whereIsAfter($id, $boolean = 'and')
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree whereIsBefore($id, $boolean = 'and')
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree whereIsLeaf()
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree whereIsRoot()
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree whereNodeBetween($values, $boolean = 'and', $not = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree whereNotDescendantOf($id)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree withDepth(string $as = 'depth')
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree withoutRoot()
 * @property-read \Kalnoy\Nestedset\Collection|Tree[] $children
 * @property int $converted_points
 * @property int $packages_price
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree whereAllPackagesPrice($value)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree whereConvertedPoints($value)
 * @property int $is_active
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree whereIsActive($value)
 * @method static \Kalnoy\Nestedset\QueryBuilder|Tree wherePackagesPrice($value)
 */
class Tree extends Model
{
    use HasFactory;
    use NodeTrait;
    const LEFT = "left";
    const RIGHT = "right";
    protected $guarded = [];

    /**
     * Scopes
     */


    public function scopeLeftChild($query)
    {
        return $query->where('position', self::LEFT)->first();
    }

    public function scopeRightChild($query)
    {
        return $query->where('position', self::RIGHT)->first();

    }

    public function scopeHasChild($query)
    {
        return $query->where('position', self::LEFT)->first();
    }

    public function scopeGetRoot($query)
    {
        return $query->whereNull('position')->whereNull('parent_id')->first();
    }

    public function scopeLeft($query)
    {
        return $query->where('position', self::LEFT);
    }

    public function scopeRight($query)
    {
        return $query->where('position', self::RIGHT);

    }


    /**
     * relations
     */

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Methods
     */

    public function subset($set_number = 1, $chunk_size = 3, $from_node = 0)
    {
        return subset($this->getDescendants()->pluck('id')->sort()->values(), $set_number, $chunk_size, $from_node);
    }

    public function appendAsLeftNode($node)
    {
        $node->position = self::LEFT;
        return $this->appendNode($node);
    }

    public function appendAsRightNode($node)
    {
        $node->position = self::RIGHT;
        return $this->appendNode($node);
    }

    public function isLeftChild()
    {
        return $this->position == self::LEFT;
    }

    public function isRightChild()
    {
        return $this->position == self::RIGHT;

    }

    public function descendantsAndSelfCount()
    {
        return $this->descendants()->count() + 1;

    }

    public function hasLeftChild()
    {
        $left_child = $this->children()->left()->first();
        if(is_null($left_child))
            return false;
        return true;
    }


    public function leftChildCount()
    {
        $left_child = $this->children()->left()->first();
        if(is_null($left_child))
            return 0;
        return $left_child->descendantsAndSelfCount();
    }

    public function updatePackagesPrice()
    {
        $this->packages_price =  $this->user->packages()->sum('price');
        $this->save();
    }

    public function leftSideChildrenPackagePrice() : Integer
    {
        /** @var  $left_child Tree*/
        $left_child = $this->children()->left()->first();
        if(is_null($left_child))
            return 0;

        return $left_child->descendants()->sum('packages_price');
    }

    public function leftSideChildrenIds() : array
    {
        /** @var  $left_child Tree*/
        $left_child = $this->children()->left()->first();
        if(is_null($left_child))
            return [];

        $children = $left_child->descendants()->pluck('user_id')->toArray();
        return  array_merge([$left_child->id],$children);
    }

    public function hasRightChild()
    {
        $right_child = $this->children()->right()->first();
        if(is_null($right_child))
            return false;
        return true;
    }
    public function rightChildCount()
    {
        /** @var  $right_child Tree*/
        $right_child = $this->children()->right()->first();
        if(is_null($right_child))
            return 0;
        return $right_child->descendantsAndSelfCount();
    }


    public function rightSideChildrenIds() : array
    {
        /** @var  $right_child Tree*/
        $right_child = $this->children()->right()->first();
        if(is_null($right_child))
            return [];

        $children = $right_child->descendants()->pluck('user_id')->toArray();
        return  array_merge([$right_child->id],$children);
    }

    public function rightSideChildrenPackagePrice() : Integer
    {
        /** @var  $right_child Tree*/
        $right_child = $this->children()->right()->first();
        if(is_null($right_child))
            return 0;

        return $right_child->descendants()->sum('packages_price');
    }
}
