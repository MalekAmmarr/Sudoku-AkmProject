public function test_user_can_update_profile()
{
    $user = User::factory()->create();
    $response = $this->actingAs($user)->putJson('/api/profile', ['name' => 'Updated Name']);
    $response->assertStatus(200)->assertJson(['message' => 'Profile updated']);
}
