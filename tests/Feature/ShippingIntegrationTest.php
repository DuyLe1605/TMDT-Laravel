<?php

namespace Tests\Feature;

use Tests\TestCase;

class ShippingIntegrationTest extends TestCase
{
    public function test_can_fetch_provinces_from_shipping_api(): void
    {
        $response = $this->getJson(route('shipping.provinces'));

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['ProvinceID', 'ProvinceName'],
                ],
            ]);

        $data = $response->json('data');
        $this->assertNotEmpty($data);
    }

    public function test_can_fetch_districts_by_province_id(): void
    {
        // 201 is Ha Noi in GHN
        $response = $this->getJson(route('shipping.districts', ['province_id' => 201]));

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['DistrictID', 'DistrictName'],
                ],
            ]);

        $data = $response->json('data');
        $this->assertNotEmpty($data);
    }

    public function test_can_fetch_wards_by_district_id(): void
    {
        // 1492 is Tay Ho in GHN
        $response = $this->getJson(route('shipping.wards', ['district_id' => 1492]));

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['WardCode', 'WardName'],
                ],
            ]);

        $data = $response->json('data');
        $this->assertNotEmpty($data);
    }

    public function test_can_calculate_shipping_fee_with_codes(): void
    {
        $response = $this->postJson(route('shipping.calculate_fee'), [
            'district_id' => 1442, // Q1, HCM
            'ward_code' => '20101', // Ben Nghe
            'service_type_id' => 2,
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'shipping_fee',
                'formatted_shipping_fee',
                'leadtime_text',
            ]);

        $fee = $response->json('shipping_fee');
        $this->assertGreaterThan(0, $fee);
    }

    public function test_can_calculate_shipping_fee_with_resolved_names(): void
    {
        $response = $this->postJson(route('shipping.calculate_fee'), [
            'province' => 'Hà Nội',
            'district' => 'Quận Cầu Giấy',
            'ward' => 'Phường Dịch Vọng',
            'service_type_id' => 2,
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'shipping_fee',
                'formatted_shipping_fee',
            ]);

        $fee = $response->json('shipping_fee');
        $this->assertGreaterThan(0, $fee);
    }
}
