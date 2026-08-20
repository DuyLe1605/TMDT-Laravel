<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryCrudTest extends TestCase
{
    /**
     * Test load categories index page successfully.
     */
    public function test_categories_index_page_is_accessible(): void
    {
        $response = $this->get(route('categories.index'));
        $response->assertStatus(200);
        $response->assertSee('Quản lý Danh mục');
    }

    /**
     * Test create page is accessible.
     */
    public function test_categories_create_page_is_accessible(): void
    {
        $response = $this->get(route('categories.create'));
        $response->assertStatus(200);
        $response->assertSee('Thêm Danh Mục Mới');
    }
}
