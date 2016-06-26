<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Api\Controller;

use Api\Model\User;

class UserController
{
    /**
     * @SWG\Get(
     * 		tags={"User"},
     * 		path="/user/{id}",
     * 		operationId="getUserById",
     * 		summary="Find user by $id",
     * 		@SWG\Parameter(
     * 			name="id",
     * 			description="$id of the specified",
     * 			in="path",
     * 			required=true,
     * 			type="string"
     * 		),
     * 		@SWG\Response(
     * 			response=200,
     * 			description="success",
     * 			@SWG\Schema(ref="#/definitions/UserResponse")
     * 		),
     * 		@SWG\Response(
     * 			response=404,
     * 			description="Not found"
     * 		)
     * )
     */
    public function getAction()
    {
        return User::generateFake()->toApi();
    }

    /**
     * @SWG\Post(
     * 		tags={"User"},
     * 		path="/user",
     * 		operationId="createUser",
     * 		summary="Find user by $id",
     * 		@SWG\Response(
     * 			response=200,
     * 			description="success",
     * 			@SWG\Schema(ref="#/definitions/UserResponse")
     * 		),
     * 		@SWG\Response(
     * 			response=404,
     * 			description="Not found"
     * 		)
     * )
     */
    public function createAction()
    {
        return true;
    }

    /**
     * @SWG\Put(
     * 		tags={"User"},
     * 		path="/user/{id}",
     * 		operationId="updateUserById",
     * 		summary="Find user by $id",
     * 		@SWG\Parameter(
     * 			name="id",
     * 			description="$id of the specified",
     * 			in="path",
     * 			required=true,
     * 			type="string"
     * 		),
     * 		@SWG\Response(
     * 			response=200,
     * 			description="success",
     * 			@SWG\Schema(ref="#/definitions/UserResponse")
     * 		),
     * 		@SWG\Response(
     * 			response=404,
     * 			description="Not found"
     * 		)
     * )
     */
    public function updateAction()
    {
        return true;
    }

    /**
     * @SWG\Delete(
     * 		tags={"User"},
     * 		path="/user/{id}",
     * 		operationId="deleteUserById",
     * 		summary="Find user by $id",
     * 		@SWG\Parameter(
     * 			name="id",
     * 			description="$id of the specified",
     * 			in="path",
     * 			required=true,
     * 			type="string"
     * 		),
     * 		@SWG\Response(
     * 			response=200,
     * 			description="success",
     * 			@SWG\Schema(ref="#/definitions/UserResponse")
     * 		),
     * 		@SWG\Response(
     * 			response=404,
     * 			description="Not found"
     * 		)
     * )
     */
    public function deleteAction()
    {
        return true;
    }


    /**
     * @SWG\Definition(
     *  definition = "UserFriendsResponse",
     *  required={"data"},
     *  @SWG\Property(property="data", type="array", @SWG\Items(ref="#/definitions/UserResponse"))
     * )
     *
     * @SWG\Get(
     * 		tags={"User"},
     * 		path="/user/{id}/friends",
     * 		operationId="getUserFriendsById",
     * 		summary="Get user friends by $id",
     * 		@SWG\Parameter(
     * 			name="id",
     * 			description="$id of the specified",
     * 			in="path",
     * 			required=true,
     * 			type="string"
     * 		),
     * 		@SWG\Parameter(
     * 			name="limit",
     * 			description="Limit",
     * 			in="query",
     * 			required=false,
     * 			type="integer"
     * 		),
     * 		@SWG\Parameter(
     * 			name="offset",
     * 			description="Offset",
     * 			in="query",
     * 			required=false,
     * 			type="integer"
     * 		),
     * 		@SWG\Response(
     * 			response=200,
     * 			description="success",
     * 			@SWG\Schema(ref="#/definitions/UserFriendsResponse")
     * 		),
     * 		@SWG\Response(
     * 			response=404,
     * 			description="Not found"
     * 		)
     * )
     */
    public function getFriendsAction()
    {
        $friends = [];

        for ($i = 0; $i < 25; $i++) {
            $friends[] = User::generateFake()->toApi();
        }

        return [
            'data' => $friends
        ];
    }
}
