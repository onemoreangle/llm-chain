<?php

declare(strict_types=1);

namespace PhpLlm\LlmChain\Tests\Model\Message\Content;

use PhpLlm\LlmChain\Model\Message\Content\Text;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Text::class)]
#[Small]
final class TextTest extends TestCase
{
    #[Test]
    public function constructionIsPossible(): void
    {
        $obj = new Text('foo');

        self::assertSame('foo', $obj->text);
    }

    #[Test]
    public function jsonConversionIsWorkingAsExpected(): void
    {
        $obj = new Text('foo');

        self::assertSame(['type' => 'text', 'text' => 'foo'], $obj->jsonSerialize());
    }
}
