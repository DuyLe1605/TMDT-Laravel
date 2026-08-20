<?php

namespace Tests\Unit;

use App\Constants\AppConstants;
use App\Models\Category;
use App\Services\CategoryService;
use PHPUnit\Framework\TestCase;

class CategoryServiceTest extends TestCase
{
    /**
     * Test Category Service instance.
     */
    public function test_category_service_can_be_instantiated(): void
    {
        $service = new CategoryService();
        $this->assertInstanceOf(CategoryService::class, $service);
    }

    /**
     * Test Constants Integrity.
     */
    public function test_app_constants_are_valid(): void
    {
        $this->assertSame(10, AppConstants::DEFAULT_PAGINATION_LIMIT);
        $this->assertSame(255, AppConstants::MAX_STRING_LENGTH);
        $this->assertSame('success', AppConstants::FLASH_SUCCESS);
    }
}
