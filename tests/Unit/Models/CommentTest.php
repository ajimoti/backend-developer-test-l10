<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the comment class has the correct fillable attributes.
     */
    public function test_comment_class_has_the_correct_fillable_attributes(): void
    {
        $this->assertEquals([
            'body',
            'user_id',
        ], (new Comment())->getFillable());
    }

    /**
     * Test that the comment class has the correct relationships.
     */
    public function test_comment_class_has_the_correct_relationships(): void
    {
        $this->assertInstanceOf(BelongsTo::class, (new Comment())->user());
    }
}
