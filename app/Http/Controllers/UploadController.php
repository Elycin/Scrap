<?php

namespace App\Http\Controllers;

use App\FileResolver;
use App\Library\HostInfo;
use App\Library\NameGenerator;
use App\Upload;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    /**
     * Auth By Request Parameters
     * (is valid check)
     *
     * Authenticates the user by the username and password parameters provided.
     *
     * @param Request $request
     * @return bool
     */
    private function authByRequestValid(Request $request)
    {
        return Auth::attempt(["username" => $request->input("username"), "password" => $request->input("password")]);
    }

    /**
     * Upload
     *
     * The function that handles the request logic and authentication to upload files.
     *
     * @param Request $request
     * @return string
     */
    public function upload(Request $request)
    {
        if ($this->authByRequestValid($request)) {
            if ($request->has("file")) {
                // Attempt to store the file
                $upload_result = $this->storeFile($request->file("file"), $request);

                // Return the URL to the user with a 201 for created
                return response(
                    sprintf("%s%s/%s", HostInfo::getAccessedProtocol(), HostInfo::getAccessedDomain(), $upload_result->alias), 201
                );
            } else {
                // There was no file provided, return a 400 (bad request)
                return (env('APP_ENV', 'production') == "debug") ? response("HTTP 1.1 / 400 - Bad Request", 400) : abort(400);
            }
        } else {
            // The credentials provided were wrong, return a 403 (forbidden)
            return (env('APP_ENV', 'production') == "debug") ? response("HTTP 1.1 / 403 - Forbidden", 403) : abort(403);
        }
    }

    /**
     * Store File
     *
     * The function that will handle request logic to store files on the disk.
     *
     * @param UploadedFile $file
     * @param Request $request
     * @return mixed
     */
    public function storeFile(UploadedFile $file, Request $request)
    {
        // data variable - will be reassigned if encrypted
        $data = file_get_contents($file);

        // get the unencrypted size
        $size = $file->getSize();

        // determine if we should encrypt.
        if ($request->has("encrypt")) {
            $data = Crypt::encrypt($data);
            $encrypted_size = strlen($data);
        } else {
            $encrypted_size = $size;
        }

        // hash the data variable as a unique identifier
        $hash = hash(FileResolver::getDuplicationHashingAlgorithm(), $data);

        // Generate a file path so we don't have to constantly call the same thing
        $path = sprintf("files/%s", $hash);

        // Try to create a database record for the resolver.
        try {
            if (!$resolver_result = FileResolver::where('hash', $hash)->first()) {
                $resolver_result = FileResolver::create([
                    "hash" => $hash,
                    "mime" => $file->getMimeType(),
                    "size" => $file->getSize(),
                    "encrypted_size" => $encrypted_size,
                    "encrypted" => $request->has("encrypt"),
                ]);
            }
        } catch (\Exception $exception) {
            // The record hasn't been made, so we can just return a generic 500.
            return (env('APP_ENV', 'production') == "debug") ? dd($exception) : abort(500);
        }

        // Try to create a database record for the alias.
        try {
            $upload_result = Upload::create([
                "user_id" => Auth::user()->id,
                "resolver_id" => $resolver_result->id,
                "original_filename" => $file->getClientOriginalName(),
                "alias" => $this->filenameGenerator($file->getClientOriginalExtension()),
                "user_expiration" => ($request->has("expires")) ? Carbon::parse($request->input("expires"))->toDateTimeString() : null
            ]);
        } catch (\Exception $exception) {
            // If we've gotten this far, a file has been stored and we should delete it.
            Storage::delete($path);

            // A single record has been made, and we should get rid of it.
            $resolver_result->delete();

            // Return a generic 500 for database error.
            return (env('APP_ENV', 'production') == "debug") ? dd($exception) : abort(500);
        }

        // Try to write the file if it doesn't exist.
        try {
            if (!Storage::has($path)) Storage::put($path, $data);
        } catch (\Exception $exception) {
            //Delete database records, failed to write.
            $resolver_result->delete();
            $upload_result->delete();

            // Return 507 (insufficient storage) to signal there's a disk/permission issue.
            return (env('APP_ENV', 'production') == "debug") ? dd($exception) : abort(507);
        }

        // Return the model result.
        return $upload_result;
    }

    /**
     * Filename Generator
     *
     * Consults with the database to create an alias that is not used.
     *
     * @param string $extension
     * @param int $min_override
     * @param int $max_override
     * @param int $style_override
     * @return string
     * @throws \Exception
     */
    private function filenameGenerator(string $extension = null, int $min_override = 6, int $max_override = 13, $style_override = 0): string
    {
        switch (intval(config('app.file_name_style', $style_override))) {
            case 0: // Random (Traditional)
                return NameGenerator::randomTypeGeneration($extension, $min_override, $max_override);
            case 1: // Dictionary
                return NameGenerator::nameTypeGeneration($extension);
            default: // Error
                return (env('APP_ENV', 'production') == "debug") ? dd("Invalid file_name_style") : abort(500);
        }
    }

    /**
     * Get File
     *
     * The function that gets the file from the database and the disk, or even the redis cache.
     *
     * @param $filename
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function getFile($filename)
    {
        // Get alias data from database, fail if it doesn't exist.
        $file = Upload::getCached($filename);

        // Get resolver data from database.
        $resolver = FileResolver::getCachedFromUpload($file);

        // Check to see if the file has expired.
        if ($file->hasExpirationDate() && $file->isExpired()) {
            // Delete the file.
            $this->deleteByResolver($resolver);

            // Return a fake error.
            return abort(404);
        };

        // Check to make sure the file exists on the disk
        if (Storage::has($resolver->getHashPath())) {

            // Get the data from the getter
            $data = $this->dataGetter($resolver);

            // Compile the headers
            $headers = [
                "Content-Length" => $resolver->getSize(),
                "Content-Type" => $resolver->getMime(),
                "Content-Disposition" => sprintf('inline; filename="%s"', $file->original_filename)
            ];

            // Return to the user.
            return response($data, 200, $headers);
        } else {
            // The file does not exist on the disk, we should delete the database entries.
            $this->deleteByResolver($resolver);

            // Return a 404 to the user.
            return (env('APP_ENV', 'production') == "debug") ? response("HTTP 1.1 / 404 - Forbidden", 404) : abort(404);
        }
    }

    /**
     * Data Getter
     * (Redis Memory Block)
     *
     * What we're doing here is storing an encrypted data stream in memory so we don't have to read the disk.
     * If the file has been marked as an encrypted file, we assume that this file is intended to be protected until it reaches the client computer.
     * We are going to keep the encrypted stream stored in memory and decrypt it for sending as storing decrypted data in memory can be accessed.
     *
     * If the file isn't encrypted, we're just going to continue and read it.
     *
     * @param FileResolver $resolver
     * @return string
     */
    private function dataGetter(FileResolver $resolver)
    {
        if ($resolver->getEncryptedSize() < intval(config('app.file_cache_threshold'))) {
            // The file can be cached.
            return Cache::tags('file_stream')->remember($resolver->hash, intval(config('app.file_cache_threshold', 10)), function () use ($resolver) {
                $data_stream = Storage::get($resolver->getHashPath());

                return ($resolver->isEncrypted()) ? Crypt::decrypt($data_stream) : $data_stream;
            });
        } else {
            // The file is too large for the cache
            $data_stream = Storage::get($resolver->getHashPath());

            return ($resolver->isEncrypted()) ? Crypt::decrypt($data_stream) : $data_stream;
        }
    }

    /**
     * Delete by resolver
     * (assumes the file is deleted on disk)
     *
     * Deletes all aliased filenames and the actual file stored in the resolver.
     *
     * @param FileResolver $resolver
     * @return response
     */
    private function deleteByResolver(FileResolver $resolver)
    {
        // Get the results, going to need the filenames to purge the caches.
        $uploads = Upload::where('resolver_id', $resolver->id)->get();
        foreach ($uploads as $upload) Cache::tags('file_model')->forget(strtolower($upload->filename));

        // Remove the resolver hash if it exists.
        Cache::tags('file_stream')->forget($resolver->hash);

        // Delete the records
        try {
            $uploads->delete();
            $resolver->delete();
        } catch (\Exception $exception) {
            return (env('APP_ENV', 'production') == "debug") ? dd($exception) : abort(500);
        }
    }

    /**
     * Is File Cached
     *
     * Determines if the requested filename's resolver is cached.
     *
     * @param $filename
     */
    public function isFileCached($filename)
    {
        $file = Upload::getCached($filename);
        $resolver = FileResolver::getCachedFromUpload($file);

        return dd(Cache::tags('file_stream')->has($resolver->hash));
    }

    /**
     * Delete File
     *
     * Deletes the file from the database, and even the disk.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function deleteFile(Request $request)
    {
        if ($this->authByRequestValid($request)) {
            // Get the record form the database
            $file = Upload::where('alias', $request->input("filename"))->firstOrFail();

            // Make sure the authenticated user owns the file.
            if ($file->getOwnerId() == Auth::user()->id) {
                // Remove the file from the cache
                $file->removeFromCache();

                // Delete the file from the database.
                $file->delete();
                return response("OK", 200);
            } else {
                return abort(403);
            }
        } else {
            return abort(403);
        }
    }
}
