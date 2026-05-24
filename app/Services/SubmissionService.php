<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\Student;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SubmissionService
{
    /**
     * Upload a file and create a submission record.
     * Stores file via Storage::disk('local') only — no contents in DB.
     * Auto-sets the submittable's status to 'submitted'.
     *
     * @param  \App\Models\Task|\App\Models\SubTask  $submittable
     */
    public function upload(object $submittable, Student $student, UploadedFile $file): Submission
    {
        // Store file under submissions/{submittable_type}/{submittable_id}/
        $typePart = class_basename($submittable);
        $path     = $file->store(
            "submissions/{$typePart}/{$submittable->id}",
            'local'
        );

        $submission = Submission::create([
            'submittable_id'   => $submittable->id,
            'submittable_type' => get_class($submittable),
            'submitted_by'     => $student->id,
            'file_path'        => $path,
            'file_name'        => $file->getClientOriginalName(),
            'file_type'        => $file->getMimeType(),
            'status'           => 'pending',
        ]);

        // Auto-change parent status to 'submitted'
        $submittable->update(['status' => 'submitted']);

        return $submission;
    }

    /**
     * Approve a submission and update status.
     * $reviewer is either a User (doctor) or Student (leader).
     */
    public function approve(Submission $submission, object $reviewer): void
    {
        $submission->update([
            'status'      => 'approved',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ]);

        // Update parent status to approved
        $submission->submittable->update(['status' => 'approved']);
    }

    /**
     * Reject a submission with a reason.
     * $reviewer is either a User (doctor) or Student (leader).
     */
    public function reject(Submission $submission, object $reviewer, ?string $reason = null): void
    {
        $submission->update([
            'status'           => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_by'      => $reviewer->id,
            'reviewed_at'      => now(),
        ]);

        // Revert parent status back to in_progress for re-submission
        $submission->submittable->update(['status' => 'in_progress']);
    }
}
