<?php

declare(strict_types=1);

namespace Core\Request;

use Core\Request\Formatters\Formatter;

/**
 *
 */
class BaseRequest implements Request
{
    protected array $data = [];
    protected array $headers = [];

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return Request
     */
    public function setData(array $data): Request
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function validate(): array
    {
        $errors = [];

        foreach ($this->getRules() as $field => $rules) {
            foreach ($rules as $rule) {
                if (!$rule->validate($this->data[$field] ?? null)) {
                    $errors[$field] = $rule->getErrors();
                    break;
                }
            }
        }

        return $errors;
    }

    /**
     * TODO: instead of array better use collection of rules
     * @return array
     */
    public function getRules(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getSanitizedData(): array
    {
        $data = [];

        foreach ($this->data as $key => $value) {
            $data[$key] = $value;

            $formatters = $this->getFormatters()[$key] ?? [];

            foreach ($formatters as $formatter) {
                /**
                 * @var Formatter $formatter
                 */
                $data[$key] = $formatter->format($data[$key]);
            }
        }

        return $data;
    }

    /**
     * TODO: instead of array better use collection of formatters
     * @return array
     */
    public function getFormatters(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array $data
     * @return Request
     */
    public function setHeaders(array $data): Request
    {
        $this->headers = $data;
        return $this;
    }
}
