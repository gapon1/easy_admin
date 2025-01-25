<?php

namespace App\Factory;

use App\Entity\Attachment;
use App\Repository\AttachmentRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Attachment>
 *
 * @method        Attachment|Proxy create(array|callable $attributes = [])
 * @method static Attachment|Proxy createOne(array $attributes = [])
 * @method static Attachment|Proxy find(object|array|mixed $criteria)
 * @method static Attachment|Proxy findOrCreate(array $attributes)
 * @method static Attachment|Proxy first(string $sortedField = 'id')
 * @method static Attachment|Proxy last(string $sortedField = 'id')
 * @method static Attachment|Proxy random(array $attributes = [])
 * @method static Attachment|Proxy randomOrCreate(array $attributes = [])
 * @method static AttachmentRepository|RepositoryProxy repository()
 * @method static Attachment[]|Proxy[] all()
 * @method static Attachment[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Attachment[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Attachment[]|Proxy[] findBy(array $attributes)
 * @method static Attachment[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Attachment[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class AttachmentFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'attachment' => self::faker()->text(255),
            'createdAt' => self::faker()->dateTime(),
            'question' => QuestionFactory::new(),
            'updatedAt' => self::faker()->dateTime(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Attachment $attachment): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Attachment::class;
    }
}
