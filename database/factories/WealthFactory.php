<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Models\Tag;
use Models\Indicator;
use Models\Action;
use Models\Wealth;
use Models\Unit;
use Illuminate\Support\Facades\Cache;
use Models\QualityLabel;

class WealthFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Wealth::class;

    public function appendToJsonFile($data, $filename)
    {
        $json = json_encode($data);
        $file = storage_path('app/' . $filename);

        $current_data = file_get_contents($file);
        $current_data = rtrim($current_data, "]") . "," . ltrim($json, "[") . "]";

        file_put_contents($file, $current_data);
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        parent::configure();

        return $this->afterCreating(function (Wealth $w) {
            //indicators, tags, actions, files after creation (many to many)
            $ql = QualityLabel::inRandomOrder()->limit(1)->get()[0];

            $indicators = Indicator::where('quality_label_id', '=', $ql->id)
                ->inRandomOrder()->limit($this->faker->numberBetween(1, 5))->get();
            $w->indicators()->saveMany($indicators);

            $tags = Tag::inRandomOrder()->limit($this->faker->numberBetween(1, 5))->get();
            $w->tags()->saveMany($tags);

            $proc = $w->unit;
            $actions = Action::with(['unit'])->whereHas('unit', function ($query) use ($proc) {
                $query->where('unit_id', '=', $proc->id);
            })->inRandomOrder()->limit(1)->get();
            $w->actions()->saveMany($actions);
            if (isset($w->attachment['file'])) {
                $w->files()->sync($this->faker->numberBetween(1, 50));
            }
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $groups = Cache::get('groups_seeder');

        $formationId = $this->faker->randomElement([
            "602409", //"TITRE PRO CUISINIER"
            "3154", //CAP PEINTRE EN CARROSSERIE
            "217168" //MODULE LA COMMERCIALISATION
        ]);

        $unit = Unit::inRandomOrder()->first();
        $granularity = $this->faker->randomElement([
            ["type" => "global", "id" => null],
            ["type" => "formation", "id" => $formationId],
            ["type" => "group", "id" => $groups[$this->faker->numberBetween(0, (count($groups) - 1))]->id],
            ["type" => "student", "id" => null],
        ]);
        // if ($granularity["type"] === "formation" || $granularity["type"] === "group") {
        //     $this->appendToJsonFile($granularity, "test_granularities.json");
        // }

        $attachment = $this->faker->randomElement([
            ["typeId" => 1, "value" => ["file" => ["type" => "drive"]]],
            ["typeId" => 2, "value" => ["link" => ["type" => "web", "url" => "https://citeformations.ymag.cloud/index.php/qualite/questionnaire/previsualiser/82"]]],
            ["typeId" => 3, "value" => ["ypareo" => ["type" => "process", "process" => "<p>voici comment acc\u00e9der aux preuves<\/p><p><br><\/p><p>apprenants&gt;fiche apprenants<\/p>"]]]
        ]);

        $w = [
            'name' => $this->faker->sentence($this->faker->numberBetween(1, 4)),
            'wealth_type_id' => $attachment["typeId"],
            'unit_id' => $unit->id,
            'validity_date' => now()->addYear(1),
            'description' => $this->faker->sentence(50),
            'granularity' => $granularity,
            'attachment' => $attachment["value"],
            'conformity_level' => 100
        ];

        // var_dump($w);

        return $w;
    }
}
