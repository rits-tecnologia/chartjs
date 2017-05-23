<?php

namespace Rits\ChartJS;

abstract class Chart
{
    /**
     * Chart unique identifier.
     *
     * @var string
     */
    protected $id;

    /**
     * Canvas width.
     *
     * @var string
     */
    protected $width;

    /**
     * Canvas height.
     *
     * @var string
     */
    protected $height;

    /**
     * Canvas html attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Chart data.
     *
     * @var array
     */
    protected $dataSets = [];

    /**
     * Chart labels.
     *
     * @var array
     */
    protected $labels = [];

    /**
     * Chart type.
     *
     * @var string
     */
    protected $type;

    /**
     * Specific chart options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Chart default colors.
     *
     * @var array
     */
    protected $colors = [
        ['fill' => 'rgba(220,220,220,0.2)', 'stroke' => 'rgba(220,220,220,1)', 'point' => 'rgba(220,220,220,1)', 'pointStroke' => '#fff'],
        ['fill' => 'rgba(0,0,0,0.2)', 'stroke' => 'rgba(0,0,0,1)', 'point' => 'rgba(0,0,0,1)', 'pointStroke' => '#000'],
    ];

    /**
     * Chart constructor.
     *
     * @param string $id
     * @param string $width
     * @param string $height
     * @param array $attributes
     */
    public function __construct(
        string $id = null,
        string $width = null, string $height = null,
        array $attributes = null
    ) {
        $this->id = $id ?: uniqid('chart_', true);
        $this->width =  $width ?: '100%';
        $this->height = $height ?: '';
        $this->attributes = $attributes ?: [];
    }

    /**
     * Render chart js canvas.
     *
     * @return string
     */
    public function render(): string
    {
        $parameters = $this->parametersToHtml();

        return sprintf('<canvas %s></canvas>', $parameters);
    }

    /**
     * Transform canvas parameters into html parameters.
     *
     * @return string
     */
    protected function parametersToHtml(): string
    {
        $htmlParts = [];
        $parameters = $this->buildParameters();

        foreach ($parameters as $key => $value) {
            array_push($htmlParts, sprintf('%s="%s"', $key, $value));
        }

        return implode(' ', $htmlParts);
    }

    /**
     * Build parameters as an array.
     *
     * @return array
     */
    protected function buildParameters(): array
    {
        return array_merge([
            'id' => $this->id,
            'height' => $this->height,
            'width' => $this->width,
            'data-charts' => $this->type,
            'data-options' => json_encode($this->options),
            'data-data' => json_encode($this->getCompleteDataSets()),
        ], $this->attributes);
    }

    /**
     * Add a new label to the labels array.
     *
     * @param string $label
     * @return Chart|static
     */
    public function addLabel(string $label): Chart
    {
        array_push($this->labels, $label);

        return $this;
    }

    /**
     * Set labels array.
     *
     * @param array $labels
     * @return Chart|static
     */
    public function setLabels(array $labels): Chart
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * Get labels array.
     *
     * @return array
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * Get data sets array.
     *
     * @return array
     */
    public function getDataSets(): array
    {
        return $this->dataSets;
    }

    /**
     * Parse data sets to chart js readable data.
     *
     * @return array
     */
    protected function getCompleteDataSets(): array
    {
        $counter = 0;
        $chart = ['labels' => [], 'datasets' => []];

        foreach ($this->dataSets as $row) {
            $this->fillRemainingColors($row['options'], $counter);

            $chart['datasets'][] = array_merge(
                $row['options'],
                ['data' => $row['data']]
            );

            $counter++;
        }

        $chart['labels'] = $this->getLabels();

        return $chart;
    }

    /**
     * Fill remaining row colors.
     *
     * @param array $options
     * @param int $counter
     */
    protected function fillRemainingColors(array &$options, int $counter): void
    {
        $colors = array_key_exists($counter, $this->colors) ? $this->colors[$counter] : $this->colors[0];

        foreach ($this->requiredColors() as $colorName) {
            if (! array_key_exists($colorName, $options)) {
                $shortName = str_replace('Color', '', $colorName);

                $options[$colorName] = array_key_exists($shortName, $colors)
                    ? $colors[$shortName]
                    : $colors[$this->replacementColors()[$shortName]];
            }
        }
    }

    /**
     * Alias to render function.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * List of required colors.
     *
     * @return array
     */
    abstract protected function requiredColors(): array;

    /**
     * List of replacement colors.
     *
     * @return array
     */
    abstract protected function replacementColors(): array;
}
