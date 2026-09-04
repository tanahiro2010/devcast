<?php
namespace App\Models;

class Plan
{
    public string $id;
    public string $name;
    public int $priceMonthly;
    public ?int $priceYearly;
    /** @var string[] */
    public array $features;
    /** @var array<string, int|string|bool|null> */
    public array $limits;

    /**
     * @param string[] $features
     * @param array<string, int|string|bool|null> $limits
     */
    public function __construct(
        string $id,
        string $name,
        int $priceMonthly,
        ?int $priceYearly,
        array $features,
        array $limits = []
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->priceMonthly = $priceMonthly;
        $this->priceYearly = $priceYearly;
        $this->features = $features;
        $this->limits = $limits;
    }
}