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
}
