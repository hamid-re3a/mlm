<?php

namespace MLM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;
use MLM\database\factories\ReferralTreeFactory;
use User\Models\User;

/**
 * App\Models\ReferralTree
 *
 * @property-read \Kalnoy\Nestedset\Collection|ReferralTree[] $children
 * @property-read int|null $children_count
 * @property-read ReferralTree $parent
 * @property-write mixed $parent_id
 * @method static \Kalnoy\Nestedset\Collection|static[] all($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralTree d()
 * @method static \Kalnoy\Nestedset\Collection|static[] get($columns = ['*'])
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree newModelQuery()
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree newQuery()
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree query()
 * @mixin \Eloquent
 * @property-read \User\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralTree getRoot()
 * @property int $id
 * @property int $_lft
 * @property int $_rgt
 * @property int $user_id
 * @property int $is_visible
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree ancestorsAndSelf($id, array $columns = [])
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree ancestorsOf($id, array $columns = [])
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree applyNestedSetScope(?string $table = null)
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree countErrors()
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree defaultOrder(string $dir = 'asc')
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree descendantsAndSelf($id, array $columns = [])
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree descendantsOf($id, array $columns = [], $andSelf = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree fixSubtree($root)
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree fixTree($root = null)
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree getNodeData($id, $required = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree getPlainNodeData($id, $required = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree getTotalErrors()
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree hasChildren()
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree hasParent()
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree isBroken()
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree leaves(array $columns = [])
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree makeGap(int $cut, int $height)
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree moveNode($key, $position)
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree orWhereAncestorOf(bool $id, bool $andSelf = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree orWhereDescendantOf($id)
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree orWhereNodeBetween($values)
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree orWhereNotDescendantOf($id)
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree rebuildSubtree($root, array $data, $delete = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree rebuildTree(array $data, $delete = false, $root = null)
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree reversed()
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree root(array $columns = [])
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree whereAncestorOf($id, $andSelf = false, $boolean = 'and')
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree whereAncestorOrSelf($id)
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree whereCreatedAt($value)
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree whereDescendantOf($id, $boolean = 'and', $not = false, $andSelf = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree whereDescendantOrSelf(string $id, string $boolean = 'and', string $not = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree whereId($value)
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree whereIsAfter($id, $boolean = 'and')
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree whereIsBefore($id, $boolean = 'and')
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree whereIsLeaf()
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree whereIsRoot()
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree whereIsVisible($value)
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree whereLft($value)
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree whereNodeBetween($values, $boolean = 'and', $not = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree whereNotDescendantOf($id)
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree whereParentId($value)
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree whereRgt($value)
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree whereUpdatedAt($value)
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree whereUserId($value)
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree withDepth(string $as = 'depth')
 * @method static \Kalnoy\Nestedset\QueryBuilder|ReferralTree withoutRoot()
 */
class ReferralTree extends Model
{
    use HasFactory;
    use NodeTrait;
    protected $guarded = [];


    protected static function newFactory()
    {
        return ReferralTreeFactory::new();
    }
    /**
     * scopes
     */
    public function scopeGetRoot($query)
    {
        return $query->whereNull('parent_id')->oldest('id')->first();
    }

    /**
     * relations
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSponsorUser() : ?User
    {
        $parent = $this->parent()->first();
        if(!is_null($parent))
           return $parent->user;
        return null;
    }

    public function childrenUserIds() : array
    {
        return $this->children()->pluck('user_id')->toArray();
    }
    public function descendantsUserIds() : array
    {
        return $this->descendants()->pluck('user_id')->toArray();
    }

}
