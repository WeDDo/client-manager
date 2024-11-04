<?php

namespace App\Http\Requests\Base;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected abstract function getModelInstance(): Model;

    protected function prepareForValidation(): void
    {
        $this->merge($this->extractAutocompleteIds());
    }

    protected function extractAutocompleteIds(?array $fields = null): array
    {
        if (!$fields) {
            $fields = $this->getAutocompleteFieldNames();
        }

        $data = $this->all();
        $formattedData = [];

        foreach ($fields as $field) {
            if (isset($data[$field]['id']) && is_array($data[$field])) {
                $formattedData[$field] = $data[$field]['id'];
            }
        }

        return array_merge($data, $formattedData);
    }

    protected function getAutocompleteFieldNames(): array
    {
        $model = $this->getModelInstance();
        $autocompleteData = $model?->getAutocompleteData();

        return array_keys($autocompleteData ?? []);
    }
}
