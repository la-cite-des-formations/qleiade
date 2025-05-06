<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Orchid\Support\Testing\ScreenTesting;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Models\Wealth;
use Tests\TestCase;
use Models\User;
use Tests\InteractsWithUsers;

use function PHPUnit\Framework\assertEquals;

class WealthEditTest extends TestCase
{
    use ScreenTesting, InteractsWithUsers;
    // use WithoutMiddleware;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_display_wealth_screen()
    {
        $this->withoutMiddleware();
        $screen = $this->screen('platform.quality.wealths.create');

        $screen->display()
            ->assertStatus(302);
    }

    public function test_create_wealth()
    {
        // $this->app['env'] = 'production';
        $this->setUserQualityTest();

        $parameters = ["wealth" => [
            'name' => 'test auto 1',
            'conformity_level' => 'high',
            'wealth_type' => '2',
            'description' => 'test automatique de création',
            'indicators' => ['1'],
            'unit' => '1',
            'actions' => ['3'],
            'tags' => ['2'],
            'validity_date' => '22-06-2022',
        ]];
        $screen = $this->screen('platform.quality.wealth.create');
        $screen
            ->actingAs($this->user)
            ->method('save', $parameters)
            ->assertStatus(200);
    }

    public function test_update_wealth()
    {
        // $this->app['env'] = 'production';

        // create admin user to test
        $this->setUserQualityTest();

        $id = Wealth::latest()->first()->id;

        $params = ['wealth' => [
            'name' => 'test auto updated',
            'conformity_level' => 'high',
            'wealth_type' => '2',
            'description' => 'test automatique de création',
            'indicators' => ['1'],
            'unit' => '1',
            'actions' => ['3'],
            'tags' => ['2'],
            'validity_date' => '22-06-2022',
        ]];
        $screen = $this->screen('platform.quality.wealth.edit')
            ->parameters(['wealth' => $id]);
        $res = $screen
            ->actingAs($this->user)
            ->method('save', $params);

        $res->assertStatus(200);
    }

    public function test_delete_wealth()
    {
        $this->setUserQualityTest();
        $id = Wealth::latest()->first()->id;
        echo ($id);
        $params = [
            'id' => $id
        ];
        $screen = $this->screen('platform.quality.wealths');
        $screen
            ->actingAs($this->user)
            ->method('remove', $params)
            ->assertStatus(200);
    }
}
