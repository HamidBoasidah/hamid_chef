<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertChefWorkingHoursRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // sanctum + chef role handled by route
    }

    public function rules(): array
    {
        return [
            'schedule' => ['required', 'array'],
            'schedule.*.day_of_week' => ['required', 'integer', 'min:0', 'max:6'],
            'schedule.*.ranges' => ['required', 'array'],
            'schedule.*.ranges.*.start_time' => ['required', 'date_format:H:i'],
            'schedule.*.ranges.*.end_time' => ['required', 'date_format:H:i'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $schedule = $this->input('schedule', []);
            foreach ($schedule as $idx => $day) {
                $ranges = $day['ranges'] ?? [];
                // Validate end > start and no overlaps
                $normalized = [];
                foreach ($ranges as $rIdx => $r) {
                    $start = $r['start_time'] ?? null;
                    $end = $r['end_time'] ?? null;
                    if ($start && $end && $start >= $end) {
                        $validator->errors()->add("schedule.$idx.ranges.$rIdx.end_time", 'وقت النهاية يجب أن يكون بعد وقت البداية');
                    }
                    if ($start && $end) {
                        $normalized[] = [$start, $end];
                    }
                }
                // Check overlaps by sorting
                usort($normalized, function ($a, $b) {
                    return strcmp($a[0], $b[0]);
                });
                for ($i = 1; $i < count($normalized); $i++) {
                    $prev = $normalized[$i - 1];
                    $cur = $normalized[$i];
                    if ($cur[0] < $prev[1]) {
                        $validator->errors()->add("schedule.$idx.ranges", 'الفترات الزمنية في نفس اليوم لا يجب أن تتقاطع');
                        break;
                    }
                }
            }
        });
    }
}
