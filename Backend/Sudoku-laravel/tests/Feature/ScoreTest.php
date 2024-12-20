public function test_top_scores_are_retrieved()
{
    Score::factory()->count(5)->create();
    $response = $this->getJson('/api/scores/top');
    $response->assertStatus(200)->assertJsonCount(3);
}
