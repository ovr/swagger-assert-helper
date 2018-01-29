<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Api\Controller;

/**
 * @SWG\Swagger(
 *      host="api.github.com",
 *      schemes={"https"},
 *      produces={"application/json"},
 *      consumes={"application/json"},
 * 		@SWG\Info(
 * 			title="Example API",
 * 			description="REST API",
 * 			version="1",
 * 			termsOfService="terms",
 * 			@SWG\License(name="proprietary")
 * 		),
 *      @SWG\SecurityScheme(
 *          securityDefinition="jwt",
 *          description="JWT token created from POST /token",
 *          type="apiKey",
 *          in="header",
 *          name="X-AUTH-TOKEN"
 *      )
 * )
 */
class AbstractController
{

}
