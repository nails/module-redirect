<?php

namespace Tests\FindReplace\Resolver;

use Nails\FindReplace\Factory\Hit;
use Nails\FindReplace\Factory\Matched;
use Nails\Redirect\FindReplace\Resolver\Redirect;
use PHPUnit\Framework\TestCase;

class RedirectTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!defined('NAILS_DB_PREFIX')) {
            define('NAILS_DB_PREFIX', 'nails_');
        }
    }

    public function testTables(): void
    {
        self::assertSame(
            [NAILS_DB_PREFIX . 'redirect'],
            (new Redirect())->tables()
        );
    }

    public function testResolvesEachRedirectOnce(): void
    {
        $table = NAILS_DB_PREFIX . 'redirect';
        $requested = new \ArrayObject();
        $resolver = $this->resolver([
            (object) ['id' => 7, 'old_url' => '/old', 'new_url' => 'https://example.com/new'],
        ], $requested);

        $hits = $resolver->resolve($table, [
            $this->match($table, '7', 'new_url'),
            $this->match($table, '7', 'old_url'),
            $this->match($table, '8', 'new_url'),
        ]);

        self::assertCount(1, $hits);
        self::assertArrayHasKey('7', $hits);
        self::assertSame([['7', '8']], $requested->getArrayCopy());

        $hit = $hits['7'];
        self::assertInstanceOf(Hit::class, $hit);
        self::assertSame('Redirect', $hit->getEntityLabel());
        self::assertSame('7', $hit->getEntityId());
        self::assertSame('/old → https://example.com/new', $hit->getTitle());
        self::assertCount(1, $hit->getActions());
        self::assertSame('Edit', $hit->getActions()[0]->getLabel());
        self::assertSame('https://example.com/admin/redirect/redirect/edit/7', $hit->getActions()[0]->getUrl());
    }

    public function testNoMatchesResolvesNothing(): void
    {
        $requested = new \ArrayObject();
        $resolver = $this->resolver([], $requested);

        self::assertSame([], $resolver->resolve(NAILS_DB_PREFIX . 'redirect', []));
        self::assertCount(0, $requested);
    }

    private function match(string $table, string $id, string $column): Matched
    {
        return new Matched($table, $column, 'plaintext', 'example', $id, 'https://example.com');
    }

    /**
     * @param list<\stdClass> $rows
     * @param \ArrayObject<int, list<string>> $requested Collects the IDs passed to each row load.
     */
    private function resolver(array $rows, \ArrayObject $requested): Redirect
    {
        return new class ($rows, $requested) extends Redirect {
            /**
             * @param list<\stdClass> $stub
             * @param \ArrayObject<int, list<string>> $requested
             */
            public function __construct(
                private readonly array $stub,
                private readonly \ArrayObject $requested,
            ) {
            }

            protected function rows(string $table, string $columns, array $ids): array
            {
                $this->requested->append($ids);

                return $this->stub;
            }

            protected function editUrl(): string
            {
                return 'https://example.com/admin/redirect/redirect/edit';
            }
        };
    }
}
