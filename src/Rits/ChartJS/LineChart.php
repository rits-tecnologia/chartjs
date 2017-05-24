<?php

namespace Rits\ChartJS;

class LineChart extends Chart
{
    /**
     * Chart type.
     *
     * @var string
     */
    protected $type = 'line';

    /**
     * Push new row of data to the data sets.
     *
     * @param array $data
     * @param array $options
     * @param string $name
     */
    public function pushData(array $data = [], array $options = [], string $name = null)
    {
        $name = $name ?: 'set-'.(count($this->dataSets) + 1);

        $this->dataSets[$name]['data'] = $data;
        $this->dataSets[$name]['options'] = $options;
    }

    /**
     * List of required colors.
     *
     * @return array
     */
    protected function requiredColors(): array
    {
        return ['fillColor', 'strokeColor', 'pointColor', 'pointStrokeColor', 'pointHighlightFill', 'pointHighlightStroke'];
    }

    /**
     * List of replacement colors.
     *
     * @return array
     */
    protected function replacementColors(): array
    {
        return ['background' => 'fill', 'pointHighlightFill' => 'point', 'pointHighlightStroke' => 'pointStroke'];
    }
}
