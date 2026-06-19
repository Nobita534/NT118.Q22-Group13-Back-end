<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;

class ArticlesTest extends TestCase
{
    public function test_articles_list_returns_paginated_payload(): void
    {
        $response = $this->getJson('/api/techbyte/articles?page=1&per_page=10');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => ['id', 'title', 'summary', 'stats'],
                ],
                'pagination' => ['total', 'per_page', 'current_page', 'last_page', 'has_next_page', 'next_page'],
                'meta' => ['request_id', 'timestamp'],
            ]);
    }

    public function test_article_detail_returns_mock_content(): void
    {
        $response = $this->getJson('/api/techbyte/articles/101');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', 101)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'title', 'summary', 'content', 'source', 'categories', 'tags', 'products', 'stats'],
                'meta' => ['request_id', 'timestamp'],
            ]);
    }

    public function test_article_detail_returns_summary_voice_link(): void
    {
        $response = $this->getJson('/api/techbyte/articles/101');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.sum_voice_link', 'https://cdn.techbyte.vn/articles/101/summary.mp3');
    }
}
