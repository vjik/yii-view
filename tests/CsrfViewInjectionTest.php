<?php

declare(strict_types=1);

namespace Yiisoft\Yii\View\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\View\CsrfViewInjection;
use Yiisoft\Yii\View\Tests\Support\FakeCsrfToken;

final class CsrfViewInjectionTest extends TestCase
{
    public function testGetContentPatameters(): void
    {
        $token = '123';

        $parameters = $this->getInjection($token)->getContentParameters();

        $this->assertCount(1, $parameters);
        $this->assertSame('csrf', key($parameters));
        $this->assertSame($token, current($parameters));
    }

    public function testGetLayoutPatameters(): void
    {
        $token = '123';

        $parameters = $this->getInjection($token)->getLayoutParameters();

        $this->assertCount(1, $parameters);
        $this->assertSame('csrf', key($parameters));
        $this->assertSame($token, current($parameters));
    }

    public function testGetMetaTags(): void
    {
        $token = '123';

        $metaTags = $this->getInjection($token)->getMetaTags();

        $this->assertCount(1, $metaTags);

        $metaTag = reset($metaTags);

        $this->assertArrayHasKey('content', $metaTag);

        $this->assertSame(
            ['__key' => 'csrf_meta_tags', 'name' => 'csrf', 'content' => $token],
            $metaTag
        );
    }

    public function testWithParameterName(): void
    {
        $injection = $this->getInjection('123')->withParameterName('kitty');

        $contentParameters = $injection->getContentParameters();
        $layoutParameters = $injection->getLayoutParameters();

        $this->assertSame('kitty', key($contentParameters));
        $this->assertSame('kitty', key($layoutParameters));
    }

    public function testWithMetaAttributeName(): void
    {
        $metaTags = $this->getInjection('123')
            ->withMetaAttributeName('kitty')
            ->getMetaTags();

        $this->assertSame('kitty', $metaTags[0]['name']);
    }

    public function testImmutability(): void
    {
        $original = $this->getInjection();
        $this->assertNotSame($original, $original->withMetaAttributeName('kitty'));
        $this->assertNotSame($original, $original->withParameterName('kitty'));
    }

    private function getInjection(string $token = null): CsrfViewInjection
    {
        return new CsrfViewInjection(
            new FakeCsrfToken($token)
        );
    }
}
