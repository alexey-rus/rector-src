<?php

namespace Rector\Tests\Php74\Rector\Property\TypedPropertyRector\Fixture;

final class ClassWithProperty
{
    /**
     * @var int
     */
    private $count;

    /**
     * @var int|null|bool
     */
    private $multiCount;

    /**
     * @var callable
     */
    private $shouldBeSkippedToo;

    /**
     * @var callable|null
     */
    private $shouldBeSkippedAlso;
}

?>
-----
<?php

namespace Rector\Tests\Php74\Rector\Property\TypedPropertyRector\Fixture;

final class ClassWithProperty
{
    private int $count;

    /**
     * @var int|null|bool
     */
    private $multiCount;

    /**
     * @var callable
     */
    private $shouldBeSkippedToo;

    /**
     * @var callable|null
     */
    private $shouldBeSkippedAlso;
}

?>
