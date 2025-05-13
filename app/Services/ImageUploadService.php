<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ImageUploadService
{
  public static function uploadImage($image, $directory = '')
  {
    $baseDirectory = 'uploads/images/';
    $fullDirectory = public_path($baseDirectory . $directory);

    if (!File::exists($fullDirectory)) {
      File::makeDirectory($fullDirectory, 0755, true);
    }

    if ($image instanceof \Illuminate\Http\UploadedFile) {
      // Validasi file
      $validator = Validator::make(
        ['image' => $image],
        ['image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048']
      );

      if ($validator->fails()) {
        throw new \Exception($validator->errors()->first('image'));
      }

      $imageName = md5(time() . '_' . $image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();
      $image->move($fullDirectory, $imageName);
    } elseif (is_string($image) && preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
      // Tangani base64
      $imageData = substr($image, strpos($image, ',') + 1);
      $imageData = base64_decode($imageData);

      if ($imageData === false) {
        throw new \Exception('Gagal decoding gambar base64.');
      }

      $extension = strtolower($type[1]);
      if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'])) {
        throw new \Exception('Ekstensi gambar tidak didukung.');
      }

      $imageName = md5(time() . '_base64') . '.' . $extension;
      file_put_contents($fullDirectory . '/' . $imageName, $imageData);
    } else {
      throw new \Exception('Format gambar tidak valid.');
    }

    return 'images/' . ($directory ? $directory . '/' : '') . $imageName;
  }

  /**
   * Delete image from the specified directory.
   *
   * @param string $imagePath
   * @return void
   */
  public static function deleteImage($imagePath)
  {
    $fullPath = public_path('uploads/' . $imagePath);

    if (File::exists($fullPath)) {
      File::delete($fullPath);
    }
  }
}
