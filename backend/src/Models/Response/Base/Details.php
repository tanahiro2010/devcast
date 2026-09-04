<?php
namespace App\Models\Response\Base;

class Details
{
    public string $instance;
    public string $date;
    public int $status;

    public function __construct(string $instance, string | null $date = null, int $status = 400)
    {
        if ($date === null) {
            $date = date('Y-m-d H:i:s');
        }
        $this->instance = $instance;
        $this->date = $date;
        $this->status = $status;
    }

    /**
     * @return array{instance: string, date: string, status: int}
     */
    public function toArray(): array
    {
        return array(
            'instance' => $this->instance,
            'date' => $this->date,
            'status' => $this->status
        );
    }
}