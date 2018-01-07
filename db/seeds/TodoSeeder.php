<?php


use Phinx\Seed\AbstractSeed;
use Tuupola\Base62;

class TodoSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data = [
            [
                "uid" => (new Base62)->encode(random_bytes(9)),
                "title" => "Make coffee",
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ],
            [
                "uid" => (new Base62)->encode(random_bytes(9)),
                "title" => "Walk the spiderpig",
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ],
        ];

        $todos = $this->table("todos");
        $todos
            ->insert($data)
            ->save();
    }
}
