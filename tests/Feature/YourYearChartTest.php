<?php

declare(strict_types=1);

use App\Enums\MoodType;
use App\Models\Mood;
use App\Models\User;
use Livewire\Volt\Volt;

test('guests are redirected to login', function (): void {
    $this->get(route('your-year'))
        ->assertRedirect(route('login'));
});

test('authenticated users can visit your year page', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('your-year'))
        ->assertSuccessful();
});

test('chart data returns 12 months with zero counts when no moods exist', function (): void {
    $user = User::factory()->create();

    $component = Volt::actingAs($user)->test('your-year');

    $chartData = $component->get('chartData');

    expect($chartData)->toHaveCount(12);
    expect($chartData[0])->toMatchArray(['month' => 'Jan', 'pleasant' => 0, 'unpleasant' => 0]);
    expect($chartData[11])->toMatchArray(['month' => 'Dec', 'pleasant' => 0, 'unpleasant' => 0]);
});

test('chart data counts pleasant mood types correctly', function (): void {
    $user = User::factory()->create();

    Mood::factory()->for($user)->create([
        'types' => [MoodType::Good, MoodType::Joyful],
        'created_at' => now()->startOfYear(),
    ]);

    $component = Volt::actingAs($user)->test('your-year');
    $chartData = $component->get('chartData');

    expect($chartData[0]['pleasant'])->toBe(2);
    expect($chartData[0]['unpleasant'])->toBe(0);
});

test('chart data counts unpleasant mood types correctly', function (): void {
    $user = User::factory()->create();

    Mood::factory()->for($user)->create([
        'types' => [MoodType::Sad, MoodType::Stressful],
        'created_at' => now()->startOfYear(),
    ]);

    $component = Volt::actingAs($user)->test('your-year');
    $chartData = $component->get('chartData');

    expect($chartData[0]['pleasant'])->toBe(0);
    expect($chartData[0]['unpleasant'])->toBe(2);
});

test('chart data counts mixed mood types correctly', function (): void {
    $user = User::factory()->create();

    Mood::factory()->for($user)->create([
        'types' => [MoodType::Good, MoodType::Stressful],
        'created_at' => now()->startOfYear(),
    ]);

    $component = Volt::actingAs($user)->test('your-year');
    $chartData = $component->get('chartData');

    expect($chartData[0]['pleasant'])->toBe(1);
    expect($chartData[0]['unpleasant'])->toBe(1);
});

test('chart data groups moods by month', function (): void {
    $user = User::factory()->create();

    Mood::factory()->for($user)->create([
        'types' => [MoodType::Good],
        'created_at' => now()->setMonth(3)->setDay(15),
    ]);

    Mood::factory()->for($user)->create([
        'types' => [MoodType::Sad],
        'created_at' => now()->setMonth(7)->setDay(10),
    ]);

    $component = Volt::actingAs($user)->test('your-year');
    $chartData = $component->get('chartData');

    expect($chartData[2])->toMatchArray(['month' => 'Mar', 'pleasant' => 1, 'unpleasant' => 0]);
    expect($chartData[6])->toMatchArray(['month' => 'Jul', 'pleasant' => 0, 'unpleasant' => 1]);
});

test('chart data only includes current year moods', function (): void {
    $user = User::factory()->create();

    Mood::factory()->for($user)->create([
        'types' => [MoodType::Good],
        'created_at' => now()->subYear(),
    ]);

    Mood::factory()->for($user)->create([
        'types' => [MoodType::Great],
        'created_at' => now(),
    ]);

    $component = Volt::actingAs($user)->test('your-year');
    $chartData = $component->get('chartData');

    $totalPleasant = collect($chartData)->sum('pleasant');
    $totalUnpleasant = collect($chartData)->sum('unpleasant');

    expect($totalPleasant)->toBe(1);
    expect($totalUnpleasant)->toBe(0);
});

test('chart data only includes authenticated user moods', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Mood::factory()->for($otherUser)->create([
        'types' => [MoodType::Sad, MoodType::Stressful],
        'created_at' => now(),
    ]);

    Mood::factory()->for($user)->create([
        'types' => [MoodType::Good],
        'created_at' => now(),
    ]);

    $component = Volt::actingAs($user)->test('your-year');
    $chartData = $component->get('chartData');

    $totalPleasant = collect($chartData)->sum('pleasant');
    $totalUnpleasant = collect($chartData)->sum('unpleasant');

    expect($totalPleasant)->toBe(1);
    expect($totalUnpleasant)->toBe(0);
});

test('month selector displays all 12 months', function (): void {
    $user = User::factory()->create();

    Volt::actingAs($user)
        ->test('your-year')
        ->assertSee('January')
        ->assertSee('February')
        ->assertSee('March')
        ->assertSee('April')
        ->assertSee('May')
        ->assertSee('June')
        ->assertSee('July')
        ->assertSee('August')
        ->assertSee('September')
        ->assertSee('October')
        ->assertSee('November')
        ->assertSee('December');
});

test('x axis field is month when no month is selected', function (): void {
    $user = User::factory()->create();

    $component = Volt::actingAs($user)->test('your-year');

    expect($component->get('xAxisField'))->toBe('month');
});

test('x axis field is day when a month is selected', function (): void {
    $user = User::factory()->create();

    $component = Volt::actingAs($user)
        ->test('your-year')
        ->set('selectedMonth', 3);

    expect($component->get('xAxisField'))->toBe('day');
});

test('selecting a month shows daily data for that month', function (): void {
    $user = User::factory()->create();

    $component = Volt::actingAs($user)
        ->test('your-year')
        ->set('selectedMonth', 1);

    $chartData = $component->get('chartData');

    expect($chartData)->toHaveCount(31);
    expect($chartData[0])->toHaveKeys(['day', 'pleasant', 'unpleasant']);
    expect($chartData[0]['day'])->toBe('1');
    expect($chartData[30]['day'])->toBe('31');
});

test('february has correct number of days', function (): void {
    $user = User::factory()->create();

    $component = Volt::actingAs($user)
        ->test('your-year')
        ->set('selectedMonth', 2);

    $chartData = $component->get('chartData');
    $expectedDays = now()->setMonth(2)->daysInMonth;

    expect($chartData)->toHaveCount($expectedDays);
});

test('daily chart data counts moods correctly', function (): void {
    $user = User::factory()->create();

    Mood::factory()->for($user)->create([
        'types' => [MoodType::Good, MoodType::Joyful],
        'created_at' => now()->setMonth(3)->setDay(15),
    ]);

    Mood::factory()->for($user)->create([
        'types' => [MoodType::Sad],
        'created_at' => now()->setMonth(3)->setDay(20),
    ]);

    $component = Volt::actingAs($user)
        ->test('your-year')
        ->set('selectedMonth', 3);

    $chartData = $component->get('chartData');

    expect($chartData[14])->toMatchArray(['day' => '15', 'pleasant' => 2, 'unpleasant' => 0]);
    expect($chartData[19])->toMatchArray(['day' => '20', 'pleasant' => 0, 'unpleasant' => 1]);
});

test('daily chart data only includes moods from selected month', function (): void {
    $user = User::factory()->create();

    Mood::factory()->for($user)->create([
        'types' => [MoodType::Good],
        'created_at' => now()->setMonth(3)->setDay(10),
    ]);

    Mood::factory()->for($user)->create([
        'types' => [MoodType::Sad],
        'created_at' => now()->setMonth(5)->setDay(10),
    ]);

    $component = Volt::actingAs($user)
        ->test('your-year')
        ->set('selectedMonth', 3);

    $chartData = $component->get('chartData');

    $totalPleasant = collect($chartData)->sum('pleasant');
    $totalUnpleasant = collect($chartData)->sum('unpleasant');

    expect($totalPleasant)->toBe(1);
    expect($totalUnpleasant)->toBe(0);
});

test('clearing month selection returns to yearly view', function (): void {
    $user = User::factory()->create();

    $component = Volt::actingAs($user)
        ->test('your-year')
        ->set('selectedMonth', 3)
        ->set('selectedMonth');

    $chartData = $component->get('chartData');

    expect($chartData)->toHaveCount(12);
    expect($chartData[0])->toHaveKeys(['month', 'pleasant', 'unpleasant']);
    expect($component->get('xAxisField'))->toBe('month');
});

test('default view mode is categorised', function (): void {
    $user = User::factory()->create();

    $component = Volt::actingAs($user)->test('your-year');

    expect($component->get('viewMode'))->toBe('categorised');
});

test('switching to detailed view mode changes chart data structure', function (): void {
    $user = User::factory()->create();

    Mood::factory()->for($user)->create([
        'types' => [MoodType::Good, MoodType::Sad],
        'created_at' => now()->startOfYear(),
    ]);

    $component = Volt::actingAs($user)
        ->test('your-year')
        ->set('viewMode', 'detailed');

    $chartData = $component->get('chartData');

    expect($chartData)->toHaveCount(12);
    expect($chartData[0])->toHaveKey('month');
    expect($chartData[0])->toHaveKey('good');
    expect($chartData[0])->toHaveKey('sad');
    expect($chartData[0])->toHaveKey('joyful');
    expect($chartData[0]['good'])->toBe(1);
    expect($chartData[0]['sad'])->toBe(1);
});

test('detailed view mode includes all mood types with zero counts when no data', function (): void {
    $user = User::factory()->create();

    $component = Volt::actingAs($user)
        ->test('your-year')
        ->set('viewMode', 'detailed');

    $chartData = $component->get('chartData');

    foreach (MoodType::cases() as $type) {
        expect($chartData[0])->toHaveKey($type->value);
        expect($chartData[0][$type->value])->toBe(0);
    }
});

test('detailed daily view returns correct data structure', function (): void {
    $user = User::factory()->create();

    Mood::factory()->for($user)->create([
        'types' => [MoodType::Peaceful],
        'created_at' => now()->setMonth(1)->setDay(5),
    ]);

    $component = Volt::actingAs($user)
        ->test('your-year')
        ->set('viewMode', 'detailed')
        ->set('selectedMonth', 1);

    $chartData = $component->get('chartData');

    expect($chartData)->toHaveCount(31);
    expect($chartData[4])->toHaveKey('day');
    expect($chartData[4]['day'])->toBe('5');
    expect($chartData[4]['peaceful'])->toBe(1);
});

test('moodTypesWithData returns correct values for detailed mode', function (): void {
    $user = User::factory()->create();

    Mood::factory()->for($user)->create([
        'types' => [MoodType::Good, MoodType::Sad],
        'created_at' => now()->startOfYear(),
    ]);

    $component = Volt::actingAs($user)
        ->test('your-year')
        ->set('viewMode', 'detailed');

    $moodTypesWithData = $component->get('moodTypesWithData');

    expect($moodTypesWithData['good'])->toBeTrue();
    expect($moodTypesWithData['sad'])->toBeTrue();
    expect($moodTypesWithData['joyful'])->toBeFalse();
    expect($moodTypesWithData['peaceful'])->toBeFalse();
});

test('view mode selector is displayed', function (): void {
    $user = User::factory()->create();

    Volt::actingAs($user)
        ->test('your-year')
        ->assertSee('Categorised')
        ->assertSee('Detailed');
});

test('yesterday computed property returns correct month and day', function (): void {
    $user = User::factory()->create();
    $yesterday = now()->subDay();

    $component = Volt::actingAs($user)->test('your-year');

    expect($component->get('yesterday'))->toBe([
        'month' => $yesterday->month,
        'day' => $yesterday->day,
    ]);
});

test('yesterdayHasMood returns false when no mood exists for yesterday', function (): void {
    $user = User::factory()->create();

    $component = Volt::actingAs($user)->test('your-year');

    expect($component->get('yesterdayHasMood'))->toBeFalse();
});

test('yesterdayHasMood returns true when mood exists for yesterday', function (): void {
    $user = User::factory()->create();
    Mood::factory()->for($user)->create(['created_at' => now()->subDay()]);

    $component = Volt::actingAs($user)->test('your-year');

    expect($component->get('yesterdayHasMood'))->toBeTrue();
});

test('add yesterday mood button is shown when yesterday has no mood', function (): void {
    $user = User::factory()->create();

    Volt::actingAs($user)
        ->test('your-year')
        ->assertSee(__("Add yesterday's mood"));
});

test('add yesterday mood button is hidden when yesterday has mood', function (): void {
    $user = User::factory()->create();
    Mood::factory()->for($user)->create(['created_at' => now()->subDay()]);

    Volt::actingAs($user)
        ->test('your-year')
        ->assertDontSee(__("Add yesterday's mood"));
});

test('today computed property returns correct month and day', function (): void {
    $user = User::factory()->create();

    $component = Volt::actingAs($user)->test('your-year');

    expect($component->get('today'))->toBe([
        'month' => now()->month,
        'day' => now()->day,
    ]);
});

test('todayHasMood returns false when no mood exists for today', function (): void {
    $user = User::factory()->create();

    $component = Volt::actingAs($user)->test('your-year');

    expect($component->get('todayHasMood'))->toBeFalse();
});

test('todayHasMood returns true when mood exists for today', function (): void {
    $user = User::factory()->create();
    Mood::factory()->for($user)->create();

    $component = Volt::actingAs($user)->test('your-year');

    expect($component->get('todayHasMood'))->toBeTrue();
});

test('add today mood button is shown when today has no mood', function (): void {
    $user = User::factory()->create();

    Volt::actingAs($user)
        ->test('your-year')
        ->assertSee(__("Add today's mood"));
});

test('add today mood button is hidden when today has mood', function (): void {
    $user = User::factory()->create();
    Mood::factory()->for($user)->create();

    Volt::actingAs($user)
        ->test('your-year')
        ->assertDontSee(__("Add today's mood"));
});
