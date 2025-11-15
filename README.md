# PHP Scatter Plot
I was looking for a library to draw a very simple scatter plot in a webpage. [SVGGraph](https://github.com/goat1000/SVGGraph) was good, but I didn't want to include dozens of files in my project, so I decided to write it myself and to release it publicly. Maybe this is what you need, who knows.

The PHP code generates an SVG image that represent the scatter plot of your data.

## Guide
Include [scatterplotlib.php](scatterplotlib.php) to your project
```php
include("scatterplotlib.php");
```

Write the scatter plot features in an associative array and construct one instance of the `ScatterPlot` class
```php
$my_plot = new ScatterPlot($overall_settings);
```

In the settings array you must define the following mandatory items:
- the overall image size: `'width'` and `'height'`
- the axis ticks steps: `'x_step'` and `'y_step'`

You can also define the following facultative parameters:
- the title: `'title'`
- the axis labels: `'x_label'` and `'y_label'`
- the axis limits: `'min_x'`, `'max_x'`, `'min_y'` and `'max_y'`. The default limits are set in order to contain all the data of all the series with a margin equal to half the ticks step on both dimensions.
- the margins: `'margin_l'`, `'margin_r'`, `'margin_t'` and `'margin_b'`. The default values are 100, 25, 50 and 50 when there are labels, 100, 25, 25 and 25 when there aren't.
- the grid color: `'grid_color'`. The default color is light gray (`#ccc`).
- to add a legend to the plot use `'legend' => true`. By default, the legend is present if there is more than one series.

Add the X and Y values of your series, stored as arrays
```php
$my_plot->add_series($x_values, $y_values);
```

You can add the name of the series to be used in the legend
```php
$my_plot->add_series($x_values, $y_values, $series_name);
```

By default the marker will be a red circle 5 pixels wide. You can also customize the marker style
```php
$my_plot->add_series($x_values, $y_values, $series_name, $size, $color, $opacity, $type);
```
Use `$type = "o"` for the circle and `$type = "s"` for the square.

Finally draw the SVG image
```php
$my_plot->draw();
```

## Contributing
Contributions are most welcome by forking the repository and sending a pull request.
