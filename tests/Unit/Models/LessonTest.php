<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Lesson;

class LessonTest extends TestCase
{
    /**
     * Test that the lesson class has the correct fillable attributes.
     */
    public function test_lesson_class_has_the_correct_fillable_attributes(): void
    {
        $this->assertEquals([
            'title',
        ], (new Lesson())->getFillable());
    }
}
