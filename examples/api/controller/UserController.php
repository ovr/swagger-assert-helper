<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

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

    }


    /**
     * @SWG\Delete(
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
     * 			@SWG\Schema(ref="#/definitions/UserResponse")
     * 		),
     * 		@SWG\Response(
     * 			response=404,
     * 			description="Not found"
     * 		)
     * )
     */
    public function friendsAction()
    {

    }
}
