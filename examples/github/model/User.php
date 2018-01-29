<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Api\Model;

/**
 * @SWG\Definition(
 *  definition = "UserResponse",
 *  required={"id", "login"},
 *  @SWG\Property(property="id", type="integer", format="int64"),
 *  @SWG\Property(property="login", type="string")
 * )
 */
class User
{
}
