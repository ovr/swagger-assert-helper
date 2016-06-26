<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Tests;

class SwaggerWrapperTest extends \PHPUnit_Framework_TestCase
{
    public function testFlagPropertyAsRequiredFromDefinitionSuccess()
    {
        /** @var \Swagger\Annotations\Definition $definition */
        $definition = $this->getMockBuilder(\Swagger\Annotations\Definition::class)
            ->disableOriginalConstructor()
            ->getMock();

        $definition->definition = 'TestScheme';
        $definition->required = [
            'property1',
            'property4',
        ];

        $definition->properties = [
            $property1 = $this->getMockProperty('property1', 'string'),
            $property2 = $this->getMockProperty('property2', 'string'),
            $property3 = $this->getMockProperty('property3', 'string'),
            $property4 = $this->getMockProperty('property4', 'string'),
            $property5 = $this->getMockProperty('property5', 'string'),
            $property6 = $this->getMockProperty('property6', 'string'),
        ];

        $swaggerWrapper = $this->getSwaggerWrapper();
        $swaggerWrapper->flagPropertyAsRequiredFromDefinition($definition);
        
        parent::assertTrue($property1->required);
        parent::assertTrue($property4->required);

        parent::assertEquals(false, $property2->required);
        parent::assertEquals(false, $property3->required);
        parent::assertEquals(false, $property5->required);
        parent::assertEquals(false, $property6->required);
    }

    /**
     * @expectedException \PHPUnit_Framework_ExpectationFailedException
     * @expectedExceptionMessage Cannot find property with name property_wrong_name to mark it as required, on scheme TestScheme
     */
    public function testFlagPropertyAsRequiredFromDefinitionFail()
    {
        /** @var \Swagger\Annotations\Definition $definition */
        $definition = $this->getMockBuilder(\Swagger\Annotations\Definition::class)
            ->disableOriginalConstructor()
            ->getMock();

        $definition->definition = 'TestScheme';
        $definition->required = [
            'property1',
            'property_wrong_name',
        ];

        $definition->properties = [
            $property1 = $this->getMockProperty('property1', 'string'),
            $property2 = $this->getMockProperty('property2', 'string'),
            $property3 = $this->getMockProperty('property3', 'string'),
        ];

        $swaggerWrapper = $this->getSwaggerWrapper();
        $swaggerWrapper->flagPropertyAsRequiredFromDefinition($definition);
    }
    
    /**
     * @return \Ovr\Swagger\SwaggerWrapper
     */
    protected function getSwaggerWrapper()
    {
        return new \Ovr\Swagger\SwaggerWrapper(
            \Swagger\scan(
                __DIR__ . '/../examples'
            )
        );
    }

    /**
     * @param string $name
     * @param string $type
     * @return \Swagger\Annotations\Property
     */
    protected function getMockProperty($name, $type)
    {
        /** @var \Swagger\Annotations\Property $property */
        $property = $this->getMockBuilder(\Swagger\Annotations\Property::class)
            ->disableOriginalConstructor()
            ->getMock();

        $property->property = $name;
        $property->type = $type;

        return $property;
    }
}
