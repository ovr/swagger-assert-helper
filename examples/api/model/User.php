<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

/**
 * @SWG\Definition(
 *  definition = "UserResponse",
 *  required={"id", "name"},
 *  @SWG\Property(property="id", type="integer", format="int64"),
 *  @SWG\Property(property="name", type="string"),
 *  @SWG\Property(property="is_admin", type="boolean"),
 *  @SWG\Property(property="created", type="string", format="date"),
 *  @SWG\Property(property="last_login", type="string", format="date-time"),
 * )
 */
class User
{
    protected $id;

    protected $name;

    protected $is_admin;

    protected $created;

    protected $last_login;

    /**
     * @return User
     */
    public static function generateFake()
    {
        $user = new User();
        $user->id = mt_rand(0, mt_getrandmax());
        $user->is_admin = mt_rand(0, 100) < 10;
        
        return $user;
    }

    /**
     * @return array
     */
    public function toApi()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_admin' => $this->is_admin,
            'created' => $this->is_admin,
            'last_login' => $this->is_admin,
        ];
    }
}
