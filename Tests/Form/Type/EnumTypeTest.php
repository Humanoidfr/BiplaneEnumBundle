<?php

namespace Biplane\EnumBundle\Tests\Form\Type;

use Biplane\EnumBundle\Enumeration\FlaggedEnum;
use Biplane\EnumBundle\Form\EnumExtension;
use Biplane\EnumBundle\Form\Type\EnumType;
use Biplane\EnumBundle\Tests\Fixtures\FlagsEnum;
use Biplane\EnumBundle\Tests\Fixtures\SimpleEnum;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Test\FormIntegrationTestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class EnumTypeTest extends FormIntegrationTestCase
{
    public function testThrowExceptionWhenOptionEnumClassIsMissing(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->factory->create($this->getType());
    }

    public function testThrowExceptionWhenSpecifiedEnumClassNotImplementEnumInterface(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Enum class "Biplane\EnumBundle\Tests\Form\Type\EnumTypeTest" must be implements of Biplane\EnumBundle\Enumeration\EnumInterface');

        $this->factory->create($this->getType(), null, [
            'enum_class' => __CLASS__,
        ]);
    }

    public function testThrowExceptionWhenSpecifiedEnumClassDoesNotExists(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The "enum_class" (InvalidClass) does not exist.');

        $this->factory->create($this->getType(), null, [
            'enum_class' => 'InvalidClass',
        ]);
    }

    public function testThrowExceptionWhenAppDataNotArrayForMultipleChoices(): void
    {
        $field = $this->factory->create($this->getType(), null, [
            'multiple' => true,
            'enum_class' => SimpleEnum::class,
        ]);

        $this->expectException(UnexpectedTypeException::class);

        $field->setData('1');
    }

    public function testThrowExcetionWhenAppDataIsInvalidForMultipleChoices(): void
    {
        $field = $this->factory->create($this->getType(), null, [
            'multiple' => true,
            'enum_class' => SimpleEnum::class,
        ]);

        $this->expectException(TransformationFailedException::class);

        $field->setData([
            SimpleEnum::create(1),
            2,
        ]);
    }

    public function testThrowExcetionWhenAppDataIsInvalidForSingleChoice(): void
    {
        $field = $this->factory->create($this->getType(), null, [
            'enum_class' => SimpleEnum::class,
        ]);

        $this->expectException(UnexpectedTypeException::class);

        $field->setData(1);
    }

    public function testBindSingleNull(): void
    {
        $field = $this->factory->create($this->getType(), null, [
            'enum_class' => SimpleEnum::class,
        ]);

        $field->submit(null);

        self::assertTrue($field->isSynchronized());
        self::assertNull($field->getData());
        self::assertSame('', $field->getViewData());
    }

    public function testBindSingle(): void
    {
        $field = $this->factory->create($this->getType(), null, [
            'enum_class' => SimpleEnum::class,
        ]);

        $field->submit('1');

        self::assertTrue($field->isSynchronized());
        self::assertEquals(SimpleEnum::create(1), $field->getData());
        self::assertSame('1', $field->getViewData());
    }

    public function testBindMultipleNull(): void
    {
        $field = $this->factory->create($this->getType(), null, [
            'multiple' => true,
            'enum_class' => SimpleEnum::class,
        ]);

        $field->submit(null);

        self::assertEquals([], $field->getData());
        self::assertEquals([], $field->getViewData());
    }

    public function testBindMultipleNullFlagEnum(): void
    {
        $field = $this->factory->create($this->getType(), null, [
            'multiple' => true,
            'enum_class' => FlagsEnum::class,
        ]);

        $field->submit(null);

        self::assertInstanceOf(FlagsEnum::class, $field->getData());
        self::assertEquals(FlaggedEnum::NONE, $field->getData()->getValue());
        self::assertEquals([], $field->getNormData());
        self::assertEquals([], $field->getViewData());
    }

    public function testBindMultipleExpanded(): void
    {
        $field = $this->factory->create($this->getType(), null, [
            'multiple' => true,
            'expanded' => true,
            'enum_class' => SimpleEnum::class,
        ]);

        $field->submit(['1' => '1']);

        $data = [SimpleEnum::create(1)];

        self::assertTrue($field->isSynchronized());
        self::assertEquals($data, $field->getData());
        self::assertEquals([1], $field->getNormData());
        self::assertTrue($field['1']->getData());
        self::assertFalse($field['2']->getData());
        self::assertSame('1', $field['1']->getViewData());
        self::assertNull($field['2']->getViewData());
    }

    public function testBindMultipleExpandedFlagEnum(): void
    {
        $field = $this->factory->create($this->getType(), null, [
            'multiple' => true,
            'expanded' => true,
            'enum_class' => FlagsEnum::class,
        ]);

        $field->submit(['0' => '1', '1' => '2']);

        self::assertTrue($field->isSynchronized());
        self::assertEquals(FlagsEnum::create(1 | 2), $field->getData());
        self::assertEquals([1, 2], $field->getNormData());
        self::assertTrue($field['0']->getData());
        self::assertTrue($field['1']->getData());
        self::assertFalse($field['2']->getData());
        self::assertFalse($field['3']->getData());
        self::assertSame('1', $field['0']->getViewData());
        self::assertSame('2', $field['1']->getViewData());
        self::assertNull($field['2']->getViewData());
        self::assertNull($field['3']->getViewData());
    }

    public function testSetDataSingleNull(): void
    {
        $field = $this->factory->create($this->getType(), null, [
            'enum_class' => SimpleEnum::class,
        ]);

        $field->setData(null);

        self::assertNull($field->getData());
        self::assertEquals('', $field->getViewData());
    }

    public function testSetDataMultipleExpandedNull(): void
    {
        $field = $this->factory->create($this->getType(), null, [
            'multiple' => true,
            'expanded' => true,
            'enum_class' => SimpleEnum::class,
        ]);

        $field->setData(null);

        self::assertNull($field->getData());
        self::assertEquals([], $field->getViewData());

        foreach ($field->all() as $child) {
            self::assertSubForm($child, false, null);
        }
    }

    public function testSetDataMultipleNonExpandedNull(): void
    {
        $field = $this->factory->create($this->getType(), null, [
            'multiple' => true,
            'expanded' => false,
            'enum_class' => SimpleEnum::class,
        ]);

        $field->setData(null);

        self::assertNull($field->getData());
        self::assertEquals([], $field->getViewData());
    }

    public function testSetDataSingle(): void
    {
        $data = SimpleEnum::create(1);
        $field = $this->factory->create($this->getType(), null, [
            'enum_class' => SimpleEnum::class,
        ]);

        $field->setData($data);

        self::assertEquals($data, $field->getData());
        self::assertEquals('1', $field->getViewData());
    }

    public function testSetDataMultipleExpanded(): void
    {
        $data = [
            SimpleEnum::create(SimpleEnum::FIRST),
            SimpleEnum::create(SimpleEnum::ZERO),
        ];
        $field = $this->factory->create($this->getType(), null, [
            'multiple' => true,
            'expanded' => true,
            'enum_class' => SimpleEnum::class,
        ]);

        $field->setData($data);

        self::assertEquals($data, $field->getData());
        self::assertSame([
            0 => '1',
            1 => '0',
        ], $field->getViewData());

        self::assertSubForm($field->get('0'), true, '0');
        self::assertSubForm($field->get('1'), true, '1');
        self::assertSubForm($field->get('2'), false, null);
    }

    public function testSetDataExpanded(): void
    {
        $data = SimpleEnum::create(1);
        $field = $this->factory->create($this->getType(), null, [
            'multiple' => false,
            'expanded' => true,
            'enum_class' => SimpleEnum::class,
        ]);

        $field->setData($data);

        self::assertEquals($data, $field->getData());
        self::assertSame('1', $field->getNormData());
        self::assertSame('1', $field->getViewData());

        self::assertSubForm($field->get('0'), false, null);
        self::assertSubForm($field->get('1'), true, '1');
        self::assertSubForm($field->get('2'), false, null);
    }

    public function testSetDataMultipleExpandedFlagEnum(): void
    {
        $data = FlagsEnum::create(1 | 4);
        $field = $this->factory->create($this->getType(), null, [
            'expanded' => true,
            'enum_class' => FlagsEnum::class,
        ]);

        $field->setData($data);

        self::assertEquals($data, $field->getData());
        self::assertEquals([1, 4], $field->getNormData());
        self::assertEquals([0 => 1, 1 => 4], $field->getViewData());

        self::assertSubForm($field->get('0'), true, '1');
        self::assertSubForm($field->get('1'), false, null);
        self::assertSubForm($field->get('2'), true, '4');
        self::assertSubForm($field->get('3'), false, null);
    }

    protected function getExtensions(): array
    {
        return array_merge(parent::getExtensions(), [
            new EnumExtension(),
        ]);
    }

    private function getType(): string
    {
        return EnumType::class;
    }

    private static function assertSubForm(FormInterface $form, $data, $viewData)
    {
        self::assertSame($data, $form->getData(), '->getData() of sub form #' . $form->getName());
        self::assertSame($viewData, $form->getViewData(), '->getViewData() of sub form #' . $form->getName());
    }
}
