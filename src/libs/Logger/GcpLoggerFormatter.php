<?php

namespace Vertuoza\Libs\Logger;

use Monolog\Formatter\JsonFormatter;
use Monolog\LogRecord;

class GcpLoggerFormatter extends JsonFormatter
{
    protected $labelProperties = [];

    function __construct(array $labelProperties = [])
    {
        parent::__construct();
        $this->labelProperties = $labelProperties;
    }

    /**
     * {@inheritdoc}
     */
    // public function format(LogRecord $record): string
    public function format(array|LogRecord $record): string
    {
        $recordArray = $record;
        if ($record instanceof LogRecord) {
            $recordArray = $record->toArray();
        }
        $normalized = $this->normalize($recordArray);
        if (isset($normalized['context']) && $normalized['context'] === []) {
            if ($this->ignoreEmptyContextAndExtra) {
                unset($normalized['context']);
            } else {
                $normalized['context'] = new \stdClass;
            }
        }
        if (isset($normalized['extra']) && $normalized['extra'] === []) {
            if ($this->ignoreEmptyContextAndExtra) {
                unset($normalized['extra']);
            } else {
                $normalized['extra'] = new \stdClass;
            }
        }
        return $this->toJson($this->toGcp($normalized), true) . "\r\n";
    }

    /**
     * Return a JSON-encoded array of records.
     *
     * @param array $records
     *
     * @return string
     */
    protected function formatBatchJson(array $records): string
    {
        return $this->toJson($this->toGcp($records), true) . "\r\n";
    }

    protected function mapLevelToSeverity(string $level)
    {
        $gcpSeverityMap = [
            'EMERGENCY' => 'emergency',
            'ALERT' => 'alert',
            'CRITICAL' => 'critical',
            'ERROR' => 'error',
            'WARNING' => 'warning',
            'DEBUG' => 'notice',
            'INFO' => 'info',
            'DEBUG' => 'debug',
        ];

        return $gcpSeverityMap[$level] ?? 'info';
    }

    protected function toGcp(array $record)
    {
        $formatted = [
            'message' => $record['message'],
            'severity' => $this->mapLevelToSeverity($record['level_name']),
            'timestamp' => $record['datetime'],
            'channel' => $record['channel'],
        ];

        if (is_array($record['context'])) {

            if (count($this->labelProperties) > 0) {
                $formatted['labels'] =
                $formatted['labels'] = array_filter($record['context'], function ($key) {
                    return in_array($key, $this->labelProperties);
                }, ARRAY_FILTER_USE_KEY);
                foreach ($formatted['labels'] as $key => $value) {
                    unset($record['context'][$key]);
                }
            }

            $formatted = array_merge($record['context'], $formatted);
        }

        if (is_array($record['extra'])) {
            $formatted = array_merge($record['extra'], $formatted);
        }

        return $formatted;
    }
}
