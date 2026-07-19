<?php

namespace Tests\Feature;

use App\Models\CourseType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CourseListTest extends TestCase
{
    use DatabaseTransactions;

    public function test_list_returns_active_non_deleted_course_types()
    {
        $active = CourseType::query()->create([
            'category' => CourseType::COURSE,
            'is_active' => true,
            'is_delete' => false,
            'vi' => ['name' => 'Beginner Japanese', 'description' => 'N5 course'],
        ]);
        CourseType::query()->create([
            'category' => CourseType::COURSE,
            'is_active' => false,
            'is_delete' => false,
            'vi' => ['name' => 'Inactive course', 'description' => ''],
        ]);
        CourseType::query()->create([
            'category' => CourseType::COURSE,
            'is_active' => true,
            'is_delete' => true,
            'vi' => ['name' => 'Deleted course', 'description' => ''],
        ]);

        $response = $this->getJson('/api/pure-route/course');

        $response->assertStatus(200);
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertTrue($ids->contains($active->id));
        $this->assertCount(1, $ids);
    }

    public function test_list_filters_by_category()
    {
        CourseType::query()->create([
            'category' => CourseType::COURSE,
            'is_active' => true,
            'is_delete' => false,
            'vi' => ['name' => 'Course type', 'description' => ''],
        ]);
        $exam = CourseType::query()->create([
            'category' => CourseType::EXAM,
            'is_active' => true,
            'is_delete' => false,
            'vi' => ['name' => 'Exam type', 'description' => ''],
        ]);

        $response = $this->getJson('/api/pure-route/course?category=' . CourseType::EXAM);

        $response->assertStatus(200);
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertEquals([$exam->id], $ids->all());
    }
}
