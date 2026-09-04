<?php
namespace App\Models\Response\Base;

class Details
{
    public $instance;
    public $date;
    public $status;

    public function __construct(string $instance, string | null $date = null, int $status = 400)
    {
        if ($date === null) {
            $date = date('Y-m-d H:i:s');
        }
        $this->instance = $instance;
        $this->date = $date;
        $this->status = $status;
    }

    public function toArray()
    {
        return array(
            'instance' => $this->instance,
            'date' => $this->date,
            'status' => $this->status
        );
    }
}