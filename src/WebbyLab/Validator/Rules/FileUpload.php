<?php

declare(strict_types=1);

namespace WebbyLab\Validator\Rules;

use WebbyLab\Validator\AbstractValidator;

class FileUpload extends AbstractValidator
{
    /**
     * @var string
     */
    private string $message = 'This field is required.';

    /**
     * @var string
     */
    private string $mimeTypesMessage = 'The mime type of the file is invalid ({{ type }}). Allowed mime types are {{ types }}.';

    /**
     * @var string
     */
    private string $extensionsMessage = 'The extension of the file is invalid ({{ extension }}). Allowed extensions are {{ extensions }}.';

    /**
     * @var array
     */
    public array $mimeTypes = [];

    /**
     * @var array
     */
    public array $extensions = [];

    public function validate($value): bool
    {
        if (!isset($_FILES["file"]) || $_FILES["file"]["error"] == 4) {
            $this->error($this->message, ['value' => $value]);
            return false;
        }

        if ($this->mimeTypes !== [] && !in_array(pathinfo($value['type'], PATHINFO_EXTENSION), $this->mimeTypes)) {
            $this->error(
              $this->mimeTypesMessage, [
                'value' => $value,
                'type' => $value['type'],
                'types' => implode(', ', $this->mimeTypes),
              ]
            );
            return false;
        }

        $extension = pathinfo($value['name'], PATHINFO_EXTENSION);
        if ($this->extensions !== [] && !in_array($extension, $this->extensions)) {
            $this->error(
              $this->extensionsMessage, [
                'value' => $value,
                'extension' => $extension,
                'extensions' => implode(', ', $this->extensions),
              ]
            );
            return false;
        }

        return true;
    }

    public function message(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @param  array<string>  $mimeTypes
     * @return $this
     */
    public function mimeTypes(array $mimeTypes): self
    {
        $this->mimeTypes = $mimeTypes;
        return $this;
    }

    public function mimeTypesMessage(string $mimeTypesMessage): self
    {
        $this->mimeTypesMessage = $mimeTypesMessage;
        return $this;
    }

    /**
     * @param  array<string>  $mimeTypes
     * @return $this
     */
    public function extensions(array $extensions): self
    {
        $this->extensions = $extensions;
        return $this;
    }

    public function extensionsMessage(string $extensionsMessage): self
    {
        $this->extensionsMessage = $extensionsMessage;
        return $this;
    }
}