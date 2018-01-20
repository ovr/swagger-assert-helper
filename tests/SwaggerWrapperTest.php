<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Tests;

class SwaggerWrapperTest extends \PHPUnit\Framework\TestCase
{
    public function testGetSecurityByNameSuccess()
    {
        $expectedSecurityName = 'jwt';
        $swaggerWrapper = $this->getSwaggerWrapper();

        $security = $swaggerWrapper->getSecurityByName($expectedSecurityName);

        parent::assertSame(\Swagger\Annotations\SecurityScheme::class, get_class($security));
        parent::assertSame($expectedSecurityName, $security->securityDefinition);
    }

    public function testGetSecurityByNameFail()
    {
        $securityName = 'unknown-1232131231231231231';

        parent::assertSame(null, $this->getSwaggerWrapper()->getSecurityByName($securityName));
    }

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
     * @expectedException \PHPUnit\Framework\ExpectationFailedException
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
     * @return SwaggerWrapperMock
     */
    protected function getSwaggerWrapper()
    {
        return new SwaggerWrapperMock(
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

    public function testIntPropertyInclusiveMinimumSuccess()
    {
        $property = $this->getMockProperty('test', 'integer');
        $property->minimum = 0;

        $swaggerWrapper = $this->getSwaggerWrapper();
        $swaggerWrapper->validateProperty($property, 1);
        $swaggerWrapper->validateProperty($property, 25);
    }

    public function testIntPropertyInclusiveMaximumSuccess()
    {
        $property = $this->getMockProperty('test', 'integer');
        $property->maximum = 26;

        $swaggerWrapper = $this->getSwaggerWrapper();
        $swaggerWrapper->validateProperty($property, 1);
        $swaggerWrapper->validateProperty($property, 25);
    }

    public function testIntPropertyExclusiveMinimumSuccess()
    {
        $property = $this->getMockProperty('test', 'integer');
        $property->minimum = 0;
        $property->exclusiveMinimum = true;

        $swaggerWrapper = $this->getSwaggerWrapper();
        $swaggerWrapper->validateProperty($property, 0);
        $swaggerWrapper->validateProperty($property, 1);
        $swaggerWrapper->validateProperty($property, 25);
    }

    public function testIntPropertyExclusiveMaximumSuccess()
    {
        $property = $this->getMockProperty('test', 'integer');
        $property->maximum = 25;
        $property->exclusiveMaximum = true;

        $swaggerWrapper = $this->getSwaggerWrapper();
        $swaggerWrapper->validateProperty($property, 1);
        $swaggerWrapper->validateProperty($property, 25);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Property "test" (value "24") <= 25 (minimum)
     */
    public function testIntPropertyInclusiveMinimumLessFail()
    {
        $property = $this->getMockProperty('test', 'integer');
        $property->minimum = 25;

        $swaggerWrapper = $this->getSwaggerWrapper();
        $swaggerWrapper->validateProperty($property, 24);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Property "test" (value "25") <= 25 (minimum)
     */
    public function testIntPropertyInclusiveMinimumEqualsFail()
    {
        $property = $this->getMockProperty('test', 'integer');
        $property->minimum = 25;

        $swaggerWrapper = $this->getSwaggerWrapper();
        $swaggerWrapper->validateProperty($property, 25);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Property "test" (value "24") < 25 (exclusive minimum)
     */
    public function testIntPropertyExclusiveMinimumEqualsFail()
    {
        $property = $this->getMockProperty('test', 'integer');
        $property->minimum = 25;
        $property->exclusiveMinimum = true;

        $swaggerWrapper = $this->getSwaggerWrapper();
        $swaggerWrapper->validateProperty($property, 24);
    }
}
