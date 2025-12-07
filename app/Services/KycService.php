<?php

namespace App\Services;

use App\Models\Kyc;
use App\Repositories\KycRepository;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KycService
{
    protected KycRepository $kycs;

    public function __construct(KycRepository $kycs)
    {
        $this->kycs = $kycs;
    }

    public function all(array $with = [])
    {
        return $this->kycs->all($with);
    }

    public function paginate(int $perPage = 15, array $with = [])
    {
        return $this->kycs->paginate($perPage, $with);
    }

    public function find($id, array $with = [])
    {
        return $this->kycs->findOrFail($id, $with);
    }

    public function create(array $attributes)
    {
        return $this->kycs->create($attributes);
    }

    public function update($id, array $attributes)
    {
        $kyc = $this->kycs->findOrFail($id);
        return $this->kycs->update($kyc, $attributes);
    }

    public function delete($id)
    {
        return $this->kycs->delete($id);
    }

    public function streamDocument(Kyc $kyc, bool $download = false): StreamedResponse
    {
        if (!$kyc->document_scan_copy) {
            abort(404, __('Document not found.'));
        }

        $disk = Storage::disk('public');
        $path = $kyc->document_scan_copy;

        if (!$disk->exists($path)) {
            abort(404, __('Document not found.'));
        }

        $fileName = basename($path);
        $absolutePath = $disk->path($path);
        $mimeType = mime_content_type($absolutePath) ?: 'application/octet-stream';
        $stream = $disk->readStream($path);

        if ($stream === false) {
            abort(500, __('Unable to read document.'));
        }

        return response()->stream(function () use ($stream) {
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => sprintf('%s; filename="%s"', $download ? 'attachment' : 'inline', $fileName),
        ]);
    }

}
