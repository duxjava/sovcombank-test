<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * @return void
     */
    public function test_category_index()
    {
        Category::factory()->count(3)->create();
        $response = $this->getJson('/api/categories');

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('data')
                    ->has('data.0', fn ($json2) =>
                        $json2->has('id')
                            ->has('slug')
                            ->has('name')
                            ->has('description')
                            ->has('active'),
                    )
                    ->etc()
            );
    }

    /**
     * @return void
     */
    public function test_category_store()
    {
        $response = $this->postJson('/api/categories', [
            'slug' => fake()->slug(),
            'name' => fake()->text(),
            'description' => fake()->text(),
            'active' => fake()->boolean(),
        ]);

        $response
            ->assertStatus(201)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('data')
                    ->has('data.id')
                    ->has('data.slug')
                    ->has('data.name')
                    ->has('data.description')
                    ->has('data.active')
        );
    }

    /**
     * @return void
     */
    public function test_category_show_id()
    {
        $categories = Category::factory()->count(1)->create();

        $response = $this->getJson("/api/categories/{$categories[0]->id}");
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('data')
                    ->where('data.id', $categories[0]->id)
                    ->where('data.slug', $categories[0]->slug)
                    ->where('data.name', $categories[0]->name)
                    ->where('data.description', $categories[0]->description)
                    ->where('data.active', $categories[0]->active)
        );
    }

    /**
     * @return void
     */
    public function test_category_show_slug()
    {
        $categories = Category::factory()->count(1)->create();

        $response = $this->getJson("/api/categories/{$categories[0]->slug}");
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('data')
                ->where('data.id', $categories[0]->id)
                ->where('data.slug', $categories[0]->slug)
                ->where('data.name', $categories[0]->name)
                ->where('data.description', $categories[0]->description)
                ->where('data.active', $categories[0]->active)
        );
    }

    /**
     * @return void
     */
    public function test_category_update()
    {
        $categories = Category::factory()->count(1)->create();

        $newSlug = fake()->slug();
        $newName = fake()->text();

        $response = $this->putJson("/api/categories/{$categories[0]->id}", [
            'slug' => $newSlug,
            'name' => $newName,
        ]);

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('data')
                ->where('data.id', $categories[0]->id)
                ->where('data.slug', $newSlug)
                ->where('data.name', $newName)
                ->where('data.description', $categories[0]->description)
                ->where('data.active', $categories[0]->active)
        );
    }

    /**
     * @return void
     */
    public function test_category_delete()
    {
        $categories = Category::factory()->count(1)->create();

        $response = $this->deleteJson("/api/categories/{$categories[0]->id}");

        $response->assertStatus(204);
    }

    /**
     * @return void
     */
    public function test_category_index_filter_name()
    {
        $categories = Category::factory()->state([
            'name' => 'ёёё',
        ])->count(1)->create();
        $response = $this->getJson("/api/categories?name=ЕЕЕ");
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('data')
                    ->has('data.0', fn ($json2) =>
                        $json2->where('id', $categories[0]->id)
                            ->where('slug', $categories[0]->slug)
                            ->where('name', $categories[0]->name)
                            ->where('description', $categories[0]->description)
                            ->where('active', $categories[0]->active)
                    )
                    ->etc()
            );
    }

    /**
     * @return void
     */
    public function test_category_index_filter_description()
    {
        $categories = Category::factory()->state([
            'description' => 'ёёё',
        ])->count(1)->create();
        $response = $this->getJson("/api/categories?description=ЕЕЕ");

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('data')
                    ->has('data.0', fn ($json2) =>
                        $json2->where('id', $categories[0]->id)
                            ->where('slug', $categories[0]->slug)
                            ->where('name', $categories[0]->name)
                            ->where('description', $categories[0]->description)
                            ->where('active', $categories[0]->active)
                    )
                    ->etc()
            );
    }

    /**
     * @return void
     */
    public function test_category_index_filter_active_true()
    {
        $categories = Category::factory()->state([
            'active' => true,
        ])->count(1)->create();
        
        $response = $this->getJson("/api/categories?active=1");
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('data')
                    ->has('data.0', fn ($json2) =>
                        $json2->where('id', $categories[0]->id)
                            ->where('slug', $categories[0]->slug)
                            ->where('name', $categories[0]->name)
                            ->where('description', $categories[0]->description)
                            ->where('active', $categories[0]->active)
                    )
                    ->etc()
            );

        $response = $this->getJson("/api/categories?active=true");
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('data')
                    ->has('data.0', fn ($json2) =>
                        $json2->where('id', $categories[0]->id)
                            ->where('slug', $categories[0]->slug)
                            ->where('name', $categories[0]->name)
                            ->where('description', $categories[0]->description)
                            ->where('active', $categories[0]->active)
                    )
                    ->etc()
            );
    }

    /**
     * @return void
     */
    public function test_category_index_filter_active_false()
    {
        $categories = Category::factory()->state([
            'active' => false,
        ])->count(1)->create();
        
        $response = $this->getJson("/api/categories?active=0");
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('data')
                    ->has('data.0', fn ($json2) =>
                        $json2->where('id', $categories[0]->id)
                            ->where('slug', $categories[0]->slug)
                            ->where('name', $categories[0]->name)
                            ->where('description', $categories[0]->description)
                            ->where('active', $categories[0]->active)
                    )
                    ->etc()
            );

        $response = $this->getJson("/api/categories?active=false");
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('data')
                    ->has('data.0', fn ($json2) =>
                        $json2->where('id', $categories[0]->id)
                            ->where('slug', $categories[0]->slug)
                            ->where('name', $categories[0]->name)
                            ->where('description', $categories[0]->description)
                            ->where('active', $categories[0]->active)
                    )
                    ->etc()
            );
    }

    /**
     * @return void
     */
    public function test_category_index_filter_search()
    {
        $categories = Category::factory()->count(3)->create();
        
        $response = $this->getJson("/api/categories?name=asd&description=asd&search={$categories[1]->name}");
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('data')
                    ->has('data.0', fn ($json2) =>
                        $json2->where('id', $categories[1]->id)
                            ->where('slug', $categories[1]->slug)
                            ->where('name', $categories[1]->name)
                            ->where('description', $categories[1]->description)
                            ->where('active', $categories[1]->active)
                    )
                    ->etc()
            );
    }

    /**
     * @return void
     */
    public function test_category_index_filter_sort()
    {
        $categories = Category::factory()->count(3)->create();
        
        $response = $this->getJson("/api/categories?sort=id");
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('data')
                    ->has('data.0', fn ($json2) =>
                        $json2->where('id', $categories[0]->id)
                            ->where('slug', $categories[0]->slug)
                            ->where('name', $categories[0]->name)
                            ->where('description', $categories[0]->description)
                            ->where('active', $categories[0]->active)
                    )
                    ->etc()
            );

        $response = $this->getJson("/api/categories?sort=-id");
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('data')
                    ->has('data.0', fn ($json2) =>
                        $json2->where('id', $categories[2]->id)
                            ->where('slug', $categories[2]->slug)
                            ->where('name', $categories[2]->name)
                            ->where('description', $categories[2]->description)
                            ->where('active', $categories[2]->active)
                    )
                    ->etc()
            );
    }
}
